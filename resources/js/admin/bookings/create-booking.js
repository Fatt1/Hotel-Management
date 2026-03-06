import axios from "axios";
import { openModal, closeModal } from "../../app";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
import { Vietnamese } from "flatpickr/dist/l10n/vn.js";
import { formatVND } from "../../util";
import { getRoomTypes, getAllRoomsApi } from "../../api";


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

let selectedDates = [];

document.addEventListener("DOMContentLoaded", async function () {
    // Default: check-in hôm nay 14:00, check-out ngày mai 12:00
    const checkin = new Date();
    checkin.setHours(0, 0, 0, 0);
    const checkout = new Date(checkin);
    checkout.setDate(checkout.getDate() + 1);
    selectedDates = [checkin, checkout];

    // Set initial display
    document.getElementById("checkin-display").textContent = formatDisplay(checkin);
    document.getElementById("checkout-display").textContent = formatDisplay(checkout);
    syncHiddenInputs(checkin, checkout);

    // Single range flatpickr
    const dateRangeBar = document.getElementById("date-range-bar");
    const fpRange = flatpickr("#flatpickr-range", {
        mode: "range",
        showMonths: 2,
        locale: Vietnamese,
        defaultDate: [checkin, checkout],
        dateFormat: "Y-m-d",
        minDate: "today",
        positionElement: dateRangeBar,
        onReady(_, __, fp) {
            fp.calendarContainer.classList.add("booking-datepicker");
        },
        onChange(dates) {
            selectedDates = dates;
            if (dates.length >= 1) {
                document.getElementById("checkin-display").textContent = formatDisplay(dates[0]);
            }
            if (dates.length === 2) {
                document.getElementById("checkout-display").textContent = formatDisplay(dates[1]);
            }
            syncHiddenInputs(dates[0] ?? null, dates[1] ?? null);
        },
    });

    // Click date text → open calendar
    document.getElementById("checkin-display").addEventListener("click", () => fpRange.open());
    document.getElementById("checkout-display").addEventListener("click", () => fpRange.open());

    // Time input change → sync hidden inputs
    document.getElementById("checkin-time").addEventListener("change", () => {
        syncHiddenInputs(selectedDates[0] ?? null, selectedDates[1] ?? null);
    });
    document.getElementById("checkout-time").addEventListener("change", () => {
        syncHiddenInputs(selectedDates[0] ?? null, selectedDates[1] ?? null);
    });

    // Prevent time input click from opening calendar
    document.getElementById("checkin-time").addEventListener("click", (e) => e.stopPropagation());
    document.getElementById("checkout-time").addEventListener("click", (e) => e.stopPropagation());

    const addRoomBtn = document.getElementById("add-room-btn");
    addRoomBtn.addEventListener("click", handleAddRoomClick);
});



async function handleAddRoomClick() {
   
    // Lấy danh sách phòng trống từ API
    const rooms = (await getAllRoomsApi(
        document.getElementById("check_in").value,
        document.getElementById("check_out").value
    )).available_rooms;
   await renderRoomList(rooms);
}

async function renderRoomList(rooms) {
     let roomListContainer = '';
    rooms.forEach((room) => {
        const roomHtml = getRoomHtml(false, room);
        roomListContainer += roomHtml;
    });
    const modalContent = await getModelHtmlRoom(roomListContainer);
     openModal(modalContent);
    document.querySelectorAll(".close-modal-btn").forEach((btn) => {
        btn.addEventListener("click", closeModal);
    });
    document.getElementById("room-type-filter").addEventListener("change", handleRoomTypeChange);
}

async function getModelHtmlRoom(content) {
    const roomTypes = await getRoomTypes();
    return `
   <div class="w-full max-w-3xl">
        <div class="p-8 pb-6 flex items-center justify-between border-b border-slate-100 dark:border-slate-800 ">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center text-primary">
                <span class="material-symbols-outlined !text-2xl">add_home_work</span>
            </div>
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Chọn phòng trống</h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Hệ thống quản lý Urban Luxe
                </p>
            </div>
        </div>
        <button class="close-modal-btn p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
            <span class="material-symbols-outlined text-slate-400">close</span>
        </button>
    </div>
    <div
        class="px-8 py-6 bg-slate-50/50 dark:bg-slate-800/30 flex flex-wrap gap-4 border-b border-slate-100 dark:border-slate-800">
        <div class="">
            <select id="room-type-filter" class="block w-55 px-3 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 text-slate-600 dark:text-slate-300">
                <option value="">Tất cả loại phòng</option>
                ${roomTypes?.map((rt) => `<option value="${rt.id}">${rt.name}</option>`).join("")}
            </select>
        </div>
        <div class="">
            <select id="floor-filter" class="block w-44 px-3 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 text-slate-600 dark:text-slate-300">
                <option>Tất cả tầng</option>
                <option>Tầng 1</option>
                <option>Tầng 2</option>
                <option>Tầng 3</option>
                <option>Tầng 4</option>
                <option>Tầng 5</option>
            </select>
        </div>
        
    </div>
    <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            ${content} 
        </div>
    </div>
    <div class="p-8 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Đã chọn: <span
                class="text-primary font-black">0 phòng</span></p>
        <div class="flex items-center gap-4">
            <button
                class="close-modal-btn px-8 py-3.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-slate-200 transition-all">Đóng</button>
            <button
                class="px-8 py-3.5 bg-primary text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined">check_circle</span>
                Xác nhận thêm
            </button>
        </div>
    </div>
    
   </div>
    `;
}

function getRoomHtml(isSelected, room) {
    return `
     <label
            class="group relative flex items-center gap-4 p-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 hover:border-primary/50 hover:shadow-lg hover:shadow-primary/5 transition-all cursor-pointer">
            <input ${isSelected ? "checked" : ""} class="w-5 h-5 rounded-lg border-slate-300 text-primary focus:ring-primary/20 transition-all"
                type="checkbox" />
            <div class="flex-1">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-base font-black text-slate-900 dark:text-white uppercase">${room.name}</span>
                    <span
                        class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase rounded">Sẵn
                        sàng</span>
                </div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">${room.room_type.name}</div>
                <div class="flex items-center justify-between">
                    <div class="text-[11px] font-bold text-slate-500 uppercase">${formatVND(room.room_type.daily_price)} <span
                            class="lowercase">đ/ngày</span></div>
                    <div class="text-[11px] font-bold text-slate-500 uppercase">${formatVND(room.room_type.hourly_price)} <span
                            class="lowercase">đ/giờ</span></div>
                </div>
            </div>
        </label>`;
}



async function handleRoomTypeChange() {
        const roomTypeId = document.getElementById("room-type-filter").value;
        console.log("Selected room type ID:", roomTypeId);
        const floorId = null;
        const rooms = await getAllRoomsApi(
            document.getElementById("check_in").value,
            document.getElementById("check_out").value,
            roomTypeId || null,
            floorId || null
        );
        await renderRoomList(rooms.available_rooms);
}
