import { calculateCheckoutPayment, recordPayment, checkout } from "../../api";
import Swal from "sweetalert2";
let _amountManuallyEdited = false;
let _lastGrandTotal = 0;
let _serverRemaining = 0;
let _allSelected = true;
let BOOKING_ID = 0;
let ALREADY_PAID = 0;

// ===== Helpers =====

function fmt(amount) {
    const abs = new Intl.NumberFormat("vi-VN").format(Math.round(Math.abs(amount)));
    return (amount < 0 ? "-" : "") + abs + "đ";
}

function getCheckedRoomIds() {
    return Array.from(document.querySelectorAll(".room-checkbox:checked"))
        .map(cb => parseInt(cb.dataset.roomId));
}

function getCheckedBookingDetailIds() {
    return Array.from(document.querySelectorAll(".room-checkbox:checked"))
        .map(cb => parseInt(cb.dataset.bookingDetailId));
}

function showLoading(show) {
    document.getElementById("invoice-loading").classList.toggle("hidden", !show);
    const content = document.getElementById("invoice-content");
    content.style.opacity = show ? "0.4" : "1";
    content.style.pointerEvents = show ? "none" : "";
}

// ===== Tính tiền theo phòng được chọn =====

export async function onRoomToggle() {
    const roomIds = getCheckedRoomIds();
    if (roomIds.length === 0) {
        renderInvoice(null);
        return;
    }

    showLoading(true);
    try {
        const data = await calculateCheckoutPayment(BOOKING_ID, roomIds);
        renderInvoice(data);
    } catch (err) {
        document.getElementById("invoice-rooms-list").innerHTML =
            `<div class="text-xs text-red-500">Không thể tải dữ liệu. Vui lòng thử lại.</div>`;
    } finally {
        showLoading(false);
    }
}

// ===== Render hóa đơn =====

function renderInvoice(data) {
    if (!data) {
        ["invoice-rooms-list", "invoice-services-list", "invoice-surcharges-list"].forEach(id => {
            document.getElementById(id).innerHTML = `<div class="text-xs text-gray-400 italic">—</div>`;
        });
        ["total-room-charge", "total-service-charge", "total-surcharge", "grand-total"].forEach(id => {
            document.getElementById(id).textContent = "0đ";
        });
        _lastGrandTotal = 0;
        _serverRemaining = 0;
        renderRemaining();
        return;
    }

    // Tiền phòng
    document.getElementById("invoice-rooms-list").innerHTML = data.rooms.map(r =>
        `<div class="flex justify-between text-gray-600">
            <span class="truncate">Phòng ${r.room_name} (${r.room_type})</span>
            <span class="font-medium text-gray-800 whitespace-nowrap ml-2">${fmt(r.room_amount)}</span>
        </div>
        <div class="text-xs text-gray-500">${r.days ? "Số ngày: " + r.days : "Số giờ: " + r.hours_stayed}</div>`
    ).join("");
    document.getElementById("total-room-charge").textContent = fmt(data.total_room_amount);

    // Dịch vụ
    const svcMap = {};
    data.rooms.forEach(r => (r.services || []).forEach(s => {
        if (!svcMap[s.name]) svcMap[s.name] = { qty: 0, total: 0 };
        svcMap[s.name].qty += s.quantity;
        svcMap[s.name].total += s.total;
    }));
    const svcEntries = Object.entries(svcMap);
    document.getElementById("invoice-services-list").innerHTML = svcEntries.length
        ? svcEntries.map(([name, d]) =>
            `<div class="flex justify-between text-gray-600">
                <span>${name} (${d.qty})</span>
                <span class="font-medium text-gray-800 whitespace-nowrap ml-2">${fmt(d.total)}</span>
            </div>`).join("")
        : `<div class="text-xs text-gray-400 italic">Không có dịch vụ</div>`;
    document.getElementById("total-service-charge").textContent = fmt(data.total_service_amount);

    // Phụ phí
    const surchargeRooms = data.rooms.filter(r => r.surcharge_amount > 0);
    document.getElementById("invoice-surcharges-list").innerHTML = surchargeRooms.length
        ? surchargeRooms.map(r => {
            const labels = [];
            if (r.early_checkin) labels.push(`CI sớm ${formatHours(r.early_checkin.hours_early)}`);
            if (r.late_checkout) labels.push(`CO muộn ${formatHours(r.late_checkout.hours_late)}`);
            return `<div class="flex justify-between text-red-600 font-medium">
                        <span class="flex items-center gap-1">
                            ${labels.join(" + ")} (${r.room_name})
                        </span>
                        <span class="whitespace-nowrap ml-2">${fmt(r.surcharge_amount)}</span>
                    </div>`;
        }).join("")
        : `<div class="text-xs text-gray-400 italic">Không có phụ phí</div>`;

    document.getElementById("total-surcharge").textContent = fmt(data.total_surcharge);
    document.getElementById("grand-total").textContent = fmt(data.sum_total);

    _lastGrandTotal = data.sum_total;
    _serverRemaining = data.remaining;
    renderRemaining();
}

function formatHours(hours){
    const h = Math.floor(hours);
    const m = Math.round((hours - h) * 60);
    return `${h} giờ ${m > 0 ? " " + m + " phút" : ""}`;
}

// ===== Còn lại =====

function renderRemaining() {
    const remaining = _serverRemaining;
    const el = document.getElementById("payment-remaining");
    el.textContent = fmt(remaining);
    el.className = remaining > 0
        ? "text-sm font-bold text-rose-500"
        : "text-sm font-bold text-emerald-600";

    if (!_amountManuallyEdited && remaining > 0) {
        document.getElementById("payment-amount").value = Math.round(remaining);
    }
}

// ===== Radio: Thanh toán / Hoàn tiền =====

export function onPaymentTypeChange() {
    const isRefund = document.getElementById("type-refund").checked;
    document.getElementById("remaining-label").textContent = isRefund ? "Thừa" : "Còn lại";
    document.getElementById("submit-payment-label").textContent = isRefund ? "Ghi nhận hoàn tiền" : "Ghi nhận thanh toán";
    document.getElementById("submit-payment-btn").className = isRefund
        ? "w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg font-bold text-sm transition-colors flex items-center justify-center gap-2"
        : "w-full py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg font-bold text-sm transition-colors flex items-center justify-center gap-2";
}

// ===== Ghi nhận thanh toán =====

export async function submitPayment() {
    const amount = parseFloat(document.getElementById("payment-amount").value);
    if (!amount || amount <= 0) {
        alert("Vui lòng nhập số tiền hợp lệ.");
        return;
    }

    const paymentMethod = document.getElementById("payment-method").value;
    const paymentType = document.getElementById("type-refund").checked ? "refund" : "payment";

    const btn = document.getElementById("submit-payment-btn");
    btn.disabled = true;
    document.getElementById("submit-payment-label").textContent = "Đang xử lý...";

    try {
        await recordPayment(BOOKING_ID, { amount, paymentMethod, paymentType });
        window.location.reload();
    } catch (err) {
        alert("Lỗi: " + (err.response?.data?.message ?? err.message));
        btn.disabled = false;
        onPaymentTypeChange();
    }
}

// ===== Chọn / bỏ chọn tất cả =====

export function toggleSelectAll() {
    _allSelected = !_allSelected;
    document.querySelectorAll(".room-checkbox").forEach(cb => { cb.checked = _allSelected; });
    document.getElementById("select-all-btn").textContent = _allSelected ? "Bỏ chọn tất cả" : "Chọn tất cả";
    onRoomToggle();
}

// ===== Khởi tạo =====

document.addEventListener("DOMContentLoaded", function () {
    const meta = document.getElementById("checkout-meta");
    BOOKING_ID = parseInt(meta.dataset.bookingId);
    ALREADY_PAID = parseFloat(meta.dataset.alreadyPaid);

    document.querySelectorAll(".room-checkbox").forEach(cb => {
        cb.addEventListener("change", onRoomToggle);
    });

    document.getElementById("payment-method").addEventListener("change", () => renderRemaining());
    document.getElementById("type-payment").addEventListener("change", onPaymentTypeChange);
    document.getElementById("type-refund").addEventListener("change", onPaymentTypeChange);

    onRoomToggle();
});

function handleCheckoutClick() {
        const bookingDetailIds = getCheckedBookingDetailIds();
        if (bookingDetailIds.length === 0) {
            alert("Vui lòng chọn ít nhất một phòng để checkout.");
            return;
        }
        Swal.fire({
            title: "Xác nhận checkout",
            text: "Bạn có chắc chắn muốn checkout các phòng đã chọn không?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Có, checkout",
            cancelButtonText: "Hủy",
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    await checkout(BOOKING_ID, bookingDetailIds);
                    Swal.fire("Thành công", "Checkout thành công!", "success").then(() => {
                        window.location.href = `/admin/bookings`; // Chuyển về trang chi tiết booking sau khi checkout
                    });
                } catch (err) {
                    Swal.fire("Lỗi", err.response?.data?.message ?? err.message, "error");
                    };
                }
            }
        );
    }


document.addEventListener("DOMContentLoaded", function () {
    // ... (các phần khởi tạo khác)
    document.getElementById("checkout-btn").addEventListener("click", (e) => {
        e.preventDefault();
        handleCheckoutClick();
    });
})
// Expose để inline event handlers trong blade vẫn hoạt động
window.onRoomToggle = onRoomToggle;
window.toggleSelectAll = toggleSelectAll;
window.submitPayment = submitPayment;
window.onPaymentTypeChange = onPaymentTypeChange;