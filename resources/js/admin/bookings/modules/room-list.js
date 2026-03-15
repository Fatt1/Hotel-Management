/**
 * Renders the selected rooms list on the booking page.
 * Each room card includes a services sub-section with "＋ Thêm dịch vụ" button.
 */
import { formatVND } from '../../../util';
import { state } from './state';
import { openServiceModal } from './service-modal';

/**
 * @param {{ onRemoveRoom?: () => void, onServicesUpdated?: () => void, bookingId?: number }} callbacks
 */
export function renderRoomList({ onRemoveRoom, onServicesUpdated, bookingId } = {}) {
    const container = document.getElementById('selectedRoomsList');
    if (!container) return;

    if (state.selectedRooms.length === 0) {
        container.innerHTML = `
        <div class="flex flex-col items-center justify-center py-8 text-gray-400 dark:text-gray-600 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg">
            <span class="material-symbols-outlined text-3xl mb-2">bed</span>
            <p class="text-sm">Chưa có phòng nào được chọn</p>
        </div>`;
        return;
    }

    const days = state.selectedDates.length === 2 ? getDaysBetween(state.selectedDates[0], state.selectedDates[1]) : 1;
    container.innerHTML = state.selectedRooms.map(room => buildRoomCard(room, days)).join('');

    // Remove room
    container.querySelectorAll('[data-remove-room-id]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = parseInt(btn.dataset.removeRoomId);
            if (onRemoveRoom) {
                onRemoveRoom(id);
            } else {
                // Fallback: just remove from state
                state.selectedRooms = state.selectedRooms.filter(r => r.id !== id);
                delete state.roomServices[id];
                renderRoomList({ onRemoveRoom, onServicesUpdated, bookingId });
            }
        });
    });

    // Add service to room
    container.querySelectorAll('[data-add-service-room]').forEach(btn => {
        btn.addEventListener('click', () => {
            const roomId = parseInt(btn.dataset.addServiceRoom);
            openServiceModal(roomId, {
                bookingId: bookingId,
                onConfirm: () => {
                    renderRoomList({ onRemoveRoom, onServicesUpdated, bookingId });
                    onServicesUpdated?.();
                },
            });
        });
    });
}

// ─── Internal ────────────────────────────────────────────────────────────────

function getDaysBetween(checkin, checkout) {
    return Math.max(1, Math.ceil((checkout - checkin) / (1000 * 60 * 60 * 24)));
}

function buildRoomCard(room, days) {
    const roomCost  = days * room.room_type.daily_price;
    const services  = Object.values(state.roomServices[room.id] ?? {});
    const hasServices = services.length > 0;

    const serviceRows = hasServices
        ? services.map(s => `
            <div class="flex items-center justify-between py-0.5">
                <span class="text-xs text-gray-600 dark:text-gray-400">
                    ${s.name}
                    <span class="text-gray-400 dark:text-gray-500">×${s.quantity}</span>
                </span>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300 shrink-0 ml-4">
                    ${formatVND(s.unit_price * s.quantity)} đ
                </span>
            </div>`).join('')
        : `<p class="text-xs text-gray-400 dark:text-gray-500 italic">Chưa có dịch vụ nào</p>`;

    return `
    <div class="p-4 bg-white dark:bg-gray-800 border border-border-light dark:border-border-dark rounded-xl space-y-3">
        
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary shrink-0">
                    <span class="material-symbols-outlined text-lg">bed</span>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900 dark:text-white uppercase">${room.name}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">${room.room_type.name} · ${days} ngày</p>
                </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <div class="text-right">
                    <p class="text-sm font-bold text-primary">${formatVND(roomCost)} đ</p>
                    <p class="text-[10px] text-gray-400">${formatVND(room.room_type.daily_price)} đ/ngày</p>
                </div>
                <button type="button" data-remove-room-id="${room.id}"
                    class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors shrink-0">
                    <span class="material-symbols-outlined text-base">delete</span>
                </button>
            </div>
        </div>

        <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                    - Dịch vụ đã dùng
                </span>
                <button type="button" data-add-service-room="${room.id}"
                    class="flex items-center gap-0.5 text-[11px] font-semibold text-primary hover:text-blue-800 dark:hover:text-blue-400 transition-colors">
                    <span class="material-symbols-outlined text-sm">add</span>
                    Thêm dịch vụ
                </button>
            </div>
            <div class="space-y-0.5">${serviceRows}</div>
        </div>
    </div>`;
}
