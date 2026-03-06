
import { openModal, closeModal } from "../../app";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
import { Vietnamese } from "flatpickr/dist/l10n/vn.js";
import { formatVND } from "../../util";
import { getRoomTypes, getAllRoomsApi, getCustomerByEmail } from "../../api";

// ─── State ─────────────────────────────────────────────────────────────────────────────
let selectedDates = [];
let selectedRooms = [];
let currentRoomTypeFilter = "";
let currentCustomer = null; // null = chưa tìm, object = khách hàng đã tìm được, 'new' = tạo mới

// Input refs — được gán trong DOMContentLoaded
let customerInputs = null;

// ─── Date / Time Utilities ──────────────────────────────────────────────────────────
function formatDisplay(date) {
    const d = String(date.getDate()).padStart(2, "0");
    const m = String(date.getMonth() + 1).padStart(2, "0");
    const y = date.getFullYear();
    return `${d}/${m}/${y}`;
}

function toDbDatetime(date, time) {
    const d = String(date.getDate()).padStart(2, "0");
    const m = String(date.getMonth() + 1).padStart(2, "0");
    const y = date.getFullYear();
    return `${y}-${m}-${d} ${time}:00`;
}

function syncHiddenInputs(checkinDate, checkoutDate) {
    const checkinTime = document.getElementById("checkin-time").value;
    const checkoutTime = document.getElementById("checkout-time").value;
    if (checkinDate) document.getElementById("check_in").value = toDbDatetime(checkinDate, checkinTime);
    if (checkoutDate) document.getElementById("check_out").value = toDbDatetime(checkoutDate, checkoutTime);
}

function getDaysBetween(checkin, checkout) {
    const diff = Math.ceil((checkout - checkin) / (1000 * 60 * 60 * 24));
    return Math.max(1, diff);
}

// ─── Init ───────────────────────────────────────────────────────────────────────────────
window.addEventListener("DOMContentLoaded", function () {
    customerInputs = {
        firstName: document.getElementById("nc-first-name"),
        lastName:  document.getElementById("nc-last-name"),
        email:     document.getElementById("nc-email"),
        phone:     document.getElementById("nc-phone"),
        country:   document.querySelector("#nc-country-field input[type='hidden']"),
    };

    initDatePicker();
    initCustomerSearch();
    document.getElementById("add-room-btn").addEventListener("click", handleAddRoomClick);
    document.getElementById("confirm-booking-btn").addEventListener("click", validateAndSubmit);
});

// ─── Customer Search ─────────────────────────────────────────────────────────────────
function initCustomerSearch() {
    const btn = document.getElementById("search-customer-btn");
    const input = document.getElementById("customer-email-input");

    btn.addEventListener("click", () => searchCustomerByEmail(input.value.trim()));
    input.addEventListener("keydown", (e) => {
        if (e.key === "Enter") searchCustomerByEmail(input.value.trim());
    });
}

async function searchCustomerByEmail(email) {
    if (!email) return;

    const btn = document.getElementById("search-customer-btn");
    const icon = document.getElementById("search-customer-icon");
    const section = document.getElementById("customer-info-section");

    btn.disabled = true;
    icon.textContent = "progress_activity";
    icon.classList.add("animate-spin");

    try {
        currentCustomer = await getCustomerByEmail(email);
        renderExistingCustomer(currentCustomer);
    } catch (error) {
        if (error.response?.status === 404) {
            currentCustomer = "new";
            renderNewCustomerForm(email);
        } else {
            currentCustomer = null;
            section.classList.add("hidden");
        }
    } finally {
        btn.disabled = false;
        icon.textContent = "search";
        icon.classList.remove("animate-spin");
        section.classList.remove("hidden");
    }
}

function renderExistingCustomer(customer) {
    // Card → green
    const card = document.getElementById("customer-info-card");
    card.className = "rounded-xl border border-green-200 bg-green-50 p-4";
    const header = document.getElementById("customer-status-header");
    header.className = "flex items-center justify-between gap-2 text-green-700 mb-3";

    document.getElementById("customer-status-icon").textContent = "check_circle";
    document.getElementById("customer-status-text").textContent = "Đã tìm thấy khách hàng";

    // Show edit link
    const editLink = document.getElementById("customer-edit-link");
    editLink.href = `/admin/customers/${customer.id}/edit`;
    editLink.classList.remove("hidden");

    // Fill + readonly tất cả inputs
    const readonlyClass = "w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed";
    [["nc-first-name", customer.first_name], ["nc-last-name", customer.last_name],
     ["nc-email", customer.email], ["nc-phone", customer.phone_number]].forEach(([id, val]) => {
        const input = document.getElementById(id);
        input.value = val ?? "";
        input.readOnly = true;
        input.className = readonlyClass;
    });

    // Disable country picker
    const trigger = document.querySelector("#nc-country-field .cp-trigger");
    if (trigger) trigger.disabled = true;
}

function renderNewCustomerForm(email) {
    // Card → amber
    const card = document.getElementById("customer-info-card");
    card.className = "rounded-xl border border-amber-200 bg-amber-50 p-4";
    const header = document.getElementById("customer-status-header");
    header.className = "flex items-center justify-between gap-2 text-amber-700 mb-3";

    document.getElementById("customer-status-icon").textContent = "person_add";
    document.getElementById("customer-status-text").textContent = "Khách hàng chưa tồn tại — Nhập thông tin để tạo mới";

    // Hide edit link
    document.getElementById("customer-edit-link").classList.add("hidden");

    // Email readonly
    const emailInput = document.getElementById("nc-email");
    emailInput.value = email;
    emailInput.readOnly = true;
    emailInput.className = "w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed";

    // Reset các input còn lại
    const editableClass = "w-full px-3 py-2 text-sm border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/30";
    ["nc-first-name", "nc-last-name", "nc-phone"].forEach((id) => {
        const input = document.getElementById(id);
        input.value = "";
        input.readOnly = false;
        input.className = editableClass;
    });

    // Enable country picker
    const trigger = document.querySelector("#nc-country-field .cp-trigger");
    if (trigger) trigger.disabled = false;

    // Xóa các lỗi cũ (nếu có)
    clearCustomerErrors();
}

// ─── Validation ──────────────────────────────────────────────────────────────────────
function setError(spanId, message) {
    const span = document.getElementById(spanId);
    if (!span) return;
    span.textContent = message;
    span.classList.toggle("hidden", !message);
}

function clearCustomerErrors() {
    ["nc-first-name-error", "nc-last-name-error", "nc-email-error", "nc-phone-error", "nc-country-error"]
        .forEach((id) => setError(id, ""));
}

function validateCustomer() {
    if (currentCustomer === null) {
        Swal.fire({ icon: "warning", title: "Chưa tìm khách hàng", text: "Vui lòng tìm kiếm khách hàng theo email trước khi đặt phòng." });
        return false;
    }

    // Nếu đã tìm thấy khách hàng cũ, không cần validate form
    if (currentCustomer !== "new") return true;

    clearCustomerErrors();
    let valid = true;

    if (!customerInputs.firstName.value.trim()) {
        setError("nc-first-name-error", "Vui lòng nhập họ.");
        valid = false;
    }
    if (!customerInputs.lastName.value.trim()) {
        setError("nc-last-name-error", "Vui lòng nhập tên.");
        valid = false;
    }
    if (!customerInputs.phone.value.trim()) {
        setError("nc-phone-error", "Vui lòng nhập số điện thoại.");
        valid = false;
    } else if (!/^(0|\+84)[0-9]{9}$/.test(customerInputs.phone.value.trim())) {
        setError("nc-phone-error", "Số điện thoại không hợp lệ (VD: 0901234567).");
        valid = false;
    }
    if (!customerInputs.country.value) {
        setError("nc-country-error", "Vui lòng chọn quốc gia.");
        valid = false;
    }

    return valid;
}

function validateRooms() {
    if (selectedRooms.length === 0) {
        Swal.fire({ icon: "warning", title: "Chưa chọn phòng", text: "Vui lòng chọn ít nhất 1 phòng trước khi đặt." });
        return false;
    }
    return true;
}

async function validateAndSubmit() {
    if (!validateCustomer()) return;
    if (!validateRooms()) return;

    // TODO: gọi API tạo booking
}





function initDatePicker() {
    const checkin = new Date();
    checkin.setHours(0, 0, 0, 0);
    const checkout = new Date(checkin);
    checkout.setDate(checkout.getDate() + 1);
    selectedDates = [checkin, checkout];

    document.getElementById("checkin-display").textContent = formatDisplay(checkin);
    document.getElementById("checkout-display").textContent = formatDisplay(checkout);
    syncHiddenInputs(checkin, checkout);

    const fpRange = flatpickr("#flatpickr-range", {
        mode: "range",
        showMonths: 2,
        locale: Vietnamese,
        defaultDate: [checkin, checkout],
        dateFormat: "Y-m-d",
        minDate: "today",
        positionElement: document.getElementById("date-range-bar"),
        onReady(_, __, fp) {
            fp.calendarContainer.classList.add("booking-datepicker");
        },
        onChange(dates) {
            selectedDates = dates;
            if (dates.length >= 1) document.getElementById("checkin-display").textContent = formatDisplay(dates[0]);
            if (dates.length === 2) document.getElementById("checkout-display").textContent = formatDisplay(dates[1]);
            syncHiddenInputs(dates[0] ?? null, dates[1] ?? null);
            renderSelectedRoomsOnPage();
            renderPaymentDetails();
        },
    });

    document.getElementById("checkin-display").addEventListener("click", () => fpRange.open());
    document.getElementById("checkout-display").addEventListener("click", () => fpRange.open());

    document.getElementById("checkin-time").addEventListener("change", () => {
        syncHiddenInputs(selectedDates[0] ?? null, selectedDates[1] ?? null);
        renderSelectedRoomsOnPage();
        renderPaymentDetails();
    });
    document.getElementById("checkout-time").addEventListener("change", () => {
        syncHiddenInputs(selectedDates[0] ?? null, selectedDates[1] ?? null);
        renderSelectedRoomsOnPage();
        renderPaymentDetails();
    });

    // Ngăn click time input mở calendar
    ["checkin-time", "checkout-time"].forEach((id) =>
        document.getElementById(id).addEventListener("click", (e) => e.stopPropagation())
    );
}

// ─── Room Modal ──────────────────────────────────────────────────────────────────────
async function handleAddRoomClick() {
    const rooms = (await getAllRoomsApi(
        document.getElementById("check_in").value,
        document.getElementById("check_out").value,
        currentRoomTypeFilter || null,
        null
    )).available_rooms;
    await openRoomModal(rooms);
}

async function openRoomModal(rooms) {
    const roomTypes = await getRoomTypes();
    openModal(getModalHtml(buildRoomGrid(rooms), roomTypes));
    document.querySelectorAll(".close-modal-btn").forEach((btn) =>
        btn.addEventListener("click", closeModal)
    );
    document.getElementById("confirm-rooms-btn").addEventListener("click", () => {
        closeModal();
        renderSelectedRoomsOnPage();
        renderPaymentDetails();
    });
    document.getElementById("room-type-filter").addEventListener("change", handleRoomTypeChange);
    attachCheckboxListeners(rooms);
}

async function handleRoomTypeChange() {
    currentRoomTypeFilter = document.getElementById("room-type-filter").value;
    const rooms = (await getAllRoomsApi(
        document.getElementById("check_in").value,
        document.getElementById("check_out").value,
        currentRoomTypeFilter || null,
        null
    )).available_rooms;
    const grid = document.getElementById("room-grid");
    if (grid) {
        grid.innerHTML = buildRoomGrid(rooms);
        attachCheckboxListeners(rooms);
    }
}

function buildRoomGrid(rooms) {
    if (rooms.length === 0) {
        return `<div class="col-span-2 flex flex-col items-center justify-center py-10 text-slate-400">
            <span class="material-symbols-outlined text-4xl mb-2">search_off</span>
            <p class="text-sm">Không có phòng trống trong khoảng thời gian này</p>
        </div>`;
    }
    return rooms.map((room) => getRoomCardHtml(room)).join("");
}

function getRoomCardHtml(room) {
    const isSelected = selectedRooms.some((r) => r.id === room.id);
    const selectedClass = isSelected ? "!border-primary bg-primary/5" : "border-slate-100 dark:border-slate-700";
    return `
    <label class="group relative flex items-center gap-4 p-5 bg-white dark:bg-slate-800 rounded-2xl border ${selectedClass}
        hover:border-primary/50 hover:shadow-lg hover:shadow-primary/5 transition-all cursor-pointer">
        <input ${isSelected ? "checked" : ""}
            data-room-id="${room.id}"
            class="room-checkbox w-5 h-5 rounded-lg border-slate-300 text-primary focus:ring-primary/20 transition-all"
            type="checkbox" />
        <div class="flex-1">
            <div class="flex items-center justify-between mb-1">
                <span class="text-base font-black text-slate-900 dark:text-white uppercase">${room.name}</span>
                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-600 text-[10px] font-black uppercase rounded">Sẵn sàng</span>
            </div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">${room.room_type.name}</div>
            <div class="flex items-center justify-between">
                <div class="text-[11px] font-bold text-slate-500">${formatVND(room.room_type.daily_price)} <span class="font-normal lowercase">đ/ngày</span></div>
                <div class="text-[11px] font-bold text-slate-500">${formatVND(room.room_type.hourly_price)} <span class="font-normal lowercase">đ/giờ</span></div>
            </div>
        </div>
    </label>`;
}

function getModalHtml(gridContent, roomTypes) {
    return `
    <div class="w-full max-w-3xl">
        <div class="p-8 pb-6 flex items-center justify-between border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined !text-2xl">add_home_work</span>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Chọn phòng trống</h2>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Hệ thống quản lý Urban Luxe</p>
                </div>
            </div>
            <button class="close-modal-btn p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
                <span class="material-symbols-outlined text-slate-400">close</span>
            </button>
        </div>

        <div class="px-8 py-4 bg-slate-50/50 dark:bg-slate-800/30 border-b border-slate-100 dark:border-slate-800">
            <select id="room-type-filter" class="block w-55 px-3 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 text-slate-600 dark:text-slate-300">
                <option value="">Tất cả loại phòng</option>
                ${roomTypes?.map((rt) => `<option value="${rt.id}" ${currentRoomTypeFilter == rt.id ? "selected" : ""}>${rt.name}</option>`).join("")}
            </select>
        </div>

        <div class="overflow-y-auto p-8 max-h-[50vh] custom-scrollbar">
            <div id="room-grid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                ${gridContent}
            </div>
        </div>

        <div class="p-8 border-t border-slate-100 dark:border-slate-800 flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">
                    Đã chọn: <span id="selected-count" class="text-primary font-black">0 phòng</span>
                </p>
                <div id="selected-room-badges" class="flex flex-wrap gap-1.5">
                    <span class="text-slate-400 text-[10px] italic">Chưa chọn phòng nào</span>
                </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <button class="close-modal-btn px-6 py-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-slate-200 transition-all">
                    Đóng
                </button>
                <button id="confirm-rooms-btn" class="px-6 py-3 bg-primary text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">check_circle</span>
                    Xác nhận thêm
                </button>
            </div>
        </div>
    </div>`;
}

function attachCheckboxListeners(rooms) {
    document.querySelectorAll(".room-checkbox").forEach((checkbox) => {
        const roomId = parseInt(checkbox.dataset.roomId);
        const isSelected = selectedRooms.some((r) => r.id === roomId);
        checkbox.checked = isSelected;
        checkbox.closest("label").classList.toggle("!border-primary", isSelected);
        checkbox.closest("label").classList.toggle("bg-primary/5", isSelected);

        checkbox.addEventListener("change", () => {
            const room = rooms.find((r) => r.id === roomId);
            if (checkbox.checked) {
                if (room && !selectedRooms.some((r) => r.id === roomId)) selectedRooms.push(room);
                checkbox.closest("label").classList.add("!border-primary", "bg-primary/5");
            } else {
                selectedRooms = selectedRooms.filter((r) => r.id !== roomId);
                checkbox.closest("label").classList.remove("!border-primary", "bg-primary/5");
            }
            updateModalBadges();
        });
    });
    updateModalBadges();
}

function updateModalBadges() {
    const countEl = document.getElementById("selected-count");
    if (countEl) countEl.textContent = `${selectedRooms.length} phòng`;

    const badgesEl = document.getElementById("selected-room-badges");
    if (!badgesEl) return;

    if (selectedRooms.length === 0) {
        badgesEl.innerHTML = `<span class="text-slate-400 text-[10px] italic">Chưa chọn phòng nào</span>`;
        return;
    }

    badgesEl.innerHTML = selectedRooms
        .map(
            (r) => `
        <span class="inline-flex items-center gap-1 px-2 py-1 bg-primary/10 text-primary text-[10px] font-black uppercase rounded-lg">
            ${r.name}
            <span class="text-primary/60 font-medium normal-case">· ${r.room_type.name}</span>
            <button type="button" data-remove-id="${r.id}" class="ml-0.5 hover:text-red-500 transition-colors leading-none">✕</button>
        </span>`
        )
        .join("");

    badgesEl.querySelectorAll("[data-remove-id]").forEach((btn) => {
        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            const id = parseInt(btn.dataset.removeId);
            selectedRooms = selectedRooms.filter((r) => r.id !== id);
            const cb = document.querySelector(`.room-checkbox[data-room-id="${id}"]`);
            if (cb) {
                cb.checked = false;
                cb.closest("label").classList.remove("!border-primary", "bg-primary/5");
            }
            updateModalBadges();
        });
    });
}

// ─── Trang chính: Danh sách phòng đã chọn & Tính giá ──────────────────────────────────────
function renderSelectedRoomsOnPage() {
    const container = document.getElementById("selectedRoomsList");
    if (!container) return;

    if (selectedRooms.length === 0) {
        container.innerHTML = `
        <div class="flex flex-col items-center justify-center py-8 text-gray-400 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg">
            <span class="material-symbols-outlined text-3xl mb-2">bed</span>
            <p class="text-sm">Chưa có phòng nào được chọn</p>
        </div>`;
        return;
    }

    const days = selectedDates.length === 2 ? getDaysBetween(selectedDates[0], selectedDates[1]) : 1;

    container.innerHTML = selectedRooms
        .map(
            (room) => `
        <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 border border-border-light dark:border-border-dark rounded-xl gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary shrink-0">
                    <span class="material-symbols-outlined text-lg">bed</span>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900 dark:text-white uppercase">${room.name}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">${room.room_type.name} &middot; ${days} ngày</p>
                </div>
            </div>
            <div class="text-right shrink-0">
                <p class="text-sm font-bold text-primary">${formatVND(days * room.room_type.daily_price)} đ</p>
                <p class="text-[10px] text-gray-400">${formatVND(room.room_type.daily_price)} đ/ngày</p>
            </div>
            <button type="button" data-remove-room-id="${room.id}"
                class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors shrink-0">
                <span class="material-symbols-outlined text-base">delete</span>
            </button>
        </div>`
        )
        .join("");

    container.querySelectorAll("[data-remove-room-id]").forEach((btn) => {
        btn.addEventListener("click", () => {
            selectedRooms = selectedRooms.filter((r) => r.id !== parseInt(btn.dataset.removeRoomId));
            renderSelectedRoomsOnPage();
            renderPaymentDetails();
        });
    });
}

function renderPaymentDetails() {
    const paymentEl = document.getElementById("paymentDetails");
    const totalEl = document.getElementById("totalAmount");
    if (!paymentEl || !totalEl) return;

    if (selectedRooms.length === 0) {
        paymentEl.innerHTML = `<p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">Chưa có phòng nào được chọn</p>`;
        totalEl.textContent = "0 đ";
        return;
    }

    const days = selectedDates.length === 2 ? getDaysBetween(selectedDates[0], selectedDates[1]) : 1;
    let grandTotal = 0;

    const rows = selectedRooms.map((room) => {
        const subtotal = days * room.room_type.daily_price;
        grandTotal += subtotal;
        return `
        <div class="flex items-start justify-between gap-2">
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white uppercase">${room.name}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500">${formatVND(room.room_type.daily_price)} đ &times; ${days} ngày</p>
            </div>
            <span class="text-sm font-semibold text-gray-900 dark:text-white shrink-0">${formatVND(subtotal)} đ</span>
        </div>`;
    });

    paymentEl.innerHTML = rows.join(`<div class="border-t border-gray-100 dark:border-gray-700"></div>`);
    totalEl.textContent = `${formatVND(grandTotal)} đ`;
}

