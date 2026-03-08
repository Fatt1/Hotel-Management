/**
 * Renders the payment breakdown sidebar.
 * Shows room cost + per-room service costs + grand total.
 */
import { formatVND } from '../../../util';
import { state } from './state';

export function renderPayment() {
    const paymentEl = document.getElementById('paymentDetails');
    const totalEl   = document.getElementById('totalAmount');
    if (!paymentEl || !totalEl) return;

    if (state.selectedRooms.length === 0) {
        paymentEl.innerHTML = `<p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">Chưa có phòng nào được chọn</p>`;
        totalEl.textContent = '0 đ';
        return;
    }

    const days = state.selectedDates.length === 2 ? getDaysBetween(state.selectedDates[0], state.selectedDates[1]) : 1;
    let totalRoomAmount = 0;
    let totalServiceAmount = 0;
    const rows = [];

    state.selectedRooms.forEach(room => {
        const roomCost = days * room.room_type.daily_price;
        totalRoomAmount += roomCost;

        rows.push(`
        <div class="flex items-start justify-between gap-2">
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white uppercase">${room.name}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500">${formatVND(room.room_type.daily_price)} đ × ${days} ngày</p>
            </div>
            <span class="text-sm font-semibold text-gray-900 dark:text-white shrink-0">${formatVND(roomCost)} đ</span>
        </div>`);

        const services = Object.values(state.roomServices[room.id] ?? {});
        services.forEach(svc => {
            const svcCost = svc.unit_price * svc.quantity;
            totalServiceAmount += svcCost;
            rows.push(`
        <div class="flex items-start justify-between gap-2 pl-4 border-l-2 border-primary/20">
            <p class="text-xs text-gray-600 dark:text-gray-400">
                ${svc.name}
                <span class="text-gray-400">×${svc.quantity}</span>
            </p>
            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 shrink-0">${formatVND(svcCost)} đ</span>
        </div>`);
        });
    });

    // Thêm phần tổng kết
    const separator = `<div class="border-t border-gray-100 dark:border-gray-700"></div>`;
    const summaryRows = [];
    
    summaryRows.push(`
        <div class="flex items-center justify-between gap-2">
            <p class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Tổng tiền phòng</p>
            <span class="text-sm font-semibold text-gray-900 dark:text-white">${formatVND(totalRoomAmount)} đ</span>
        </div>`);
    
    if (totalServiceAmount > 0) {
        summaryRows.push(`
        <div class="flex items-center justify-between gap-2">
            <p class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Tổng tiền dịch vụ</p>
            <span class="text-sm font-semibold text-gray-900 dark:text-white">${formatVND(totalServiceAmount)} đ</span>
        </div>`);
    }

    const grandTotal = totalRoomAmount + totalServiceAmount;
    paymentEl.innerHTML = rows.join(separator) + separator + summaryRows.join('');
    totalEl.textContent = `${formatVND(grandTotal)} đ`;
}

function getDaysBetween(checkin, checkout) {
    return Math.max(1, Math.ceil((checkout - checkin) / (1000 * 60 * 60 * 24)));
}
