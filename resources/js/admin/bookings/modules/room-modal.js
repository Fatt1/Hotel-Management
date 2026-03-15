/**
 * Room selection modal — reusable.
 * Used in: create booking, booking detail (future).
 */
import { openModal, closeModal } from '../../../app';
import { formatVND } from '../../../util';
import { getRoomTypes, getAllRoomsApi } from '../../../api';
import { state } from './state';

/**
 * @param {string} checkinDate  - DB datetime string  e.g. "2024-05-20 14:00:00"
 * @param {string} checkoutDate - DB datetime string
 * @param {{ onConfirm?: () => void }} options
 */
export async function openRoomModal(checkinDate, checkoutDate, { onConfirm } = {}) {
    const [roomsData, roomTypes] = await Promise.all([
        getAllRoomsApi(checkinDate, checkoutDate, state.currentRoomTypeFilter || null, null),
        getRoomTypes(),
    ]);
    const rooms = roomsData?.available_rooms ?? [];

    openModal(buildModalHtml(rooms, roomTypes));
    attachCheckboxListeners(rooms);

    document.querySelectorAll('.close-modal-btn').forEach(btn =>
        btn.addEventListener('click', closeModal)
    );

    document.getElementById('confirm-rooms-btn').addEventListener('click', () => {
        closeModal();
        onConfirm?.();
    });

    document.getElementById('room-type-filter').addEventListener('change', () =>
        handleRoomTypeChange(checkinDate, checkoutDate)
    );
}

// ─── Internal ────────────────────────────────────────────────────────────────

async function handleRoomTypeChange(checkinDate, checkoutDate) {
    state.currentRoomTypeFilter = document.getElementById('room-type-filter').value;
    const data = await getAllRoomsApi(checkinDate, checkoutDate, state.currentRoomTypeFilter || null, null);
    const rooms = data?.available_rooms ?? [];
    const grid = document.getElementById('room-grid');
    if (grid) {
        grid.innerHTML = buildRoomGrid(rooms);
        attachCheckboxListeners(rooms);
    }
}

function buildRoomGrid(rooms) {
    if (rooms.length === 0) {
        return `
        <div class="col-span-2 flex flex-col items-center justify-center py-10 text-slate-400">
            <span class="material-symbols-outlined text-4xl mb-2">search_off</span>
            <p class="text-sm">Không có phòng trống trong khoảng thời gian này</p>
        </div>`;
    }
    return rooms.map(room => buildRoomCard(room)).join('');
}

function buildRoomCard(room) {
    const isSelected   = state.selectedRooms.some(r => r.id === room.id);
    const selectedCls  = isSelected ? '!border-primary bg-primary/5' : 'border-slate-100 dark:border-slate-700';
    return `
    <label class="group relative flex items-center gap-4 p-5 bg-white dark:bg-slate-800 rounded-2xl border ${selectedCls}
        hover:border-primary/50 hover:shadow-lg hover:shadow-primary/5 transition-all cursor-pointer">
        <input ${isSelected ? 'checked' : ''} data-room-id="${room.id}"
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

function buildModalHtml(rooms, roomTypes) {
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
            <select id="room-type-filter"
                class="block w-55 px-3 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 text-slate-600 dark:text-slate-300">
                <option value="">Tất cả loại phòng</option>
                ${roomTypes?.map(rt =>
                    `<option value="${rt.id}" ${state.currentRoomTypeFilter == rt.id ? 'selected' : ''}>${rt.name}</option>`
                ).join('')}
            </select>
        </div>

        <div class="overflow-y-auto p-8 max-h-[50vh] custom-scrollbar">
            <div id="room-grid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                ${buildRoomGrid(rooms)}
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
                <button id="confirm-rooms-btn"
                    class="px-6 py-3 bg-primary text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">check_circle</span>
                    Xác nhận thêm
                </button>
            </div>
        </div>
    </div>`;
}

function attachCheckboxListeners(rooms) {
    document.querySelectorAll('.room-checkbox').forEach(checkbox => {
        const roomId = parseInt(checkbox.dataset.roomId);
        checkbox.checked = state.selectedRooms.some(r => r.id === roomId);
        checkbox.closest('label').classList.toggle('!border-primary', checkbox.checked);
        checkbox.closest('label').classList.toggle('bg-primary/5', checkbox.checked);

        checkbox.addEventListener('change', () => {
            const room = rooms.find(r => r.id === roomId);
            if (checkbox.checked) {
                if (room && !state.selectedRooms.some(r => r.id === roomId)) {
                    state.selectedRooms.push(room);
                }
                checkbox.closest('label').classList.add('!border-primary', 'bg-primary/5');
            } else {
                state.selectedRooms = state.selectedRooms.filter(r => r.id !== roomId);
                checkbox.closest('label').classList.remove('!border-primary', 'bg-primary/5');
            }
            updateModalBadges();
        });
    });
    updateModalBadges();
}

function updateModalBadges() {
    const countEl = document.getElementById('selected-count');
    if (countEl) countEl.textContent = `${state.selectedRooms.length} phòng`;

    const badgesEl = document.getElementById('selected-room-badges');
    if (!badgesEl) return;

    if (state.selectedRooms.length === 0) {
        badgesEl.innerHTML = `<span class="text-slate-400 text-[10px] italic">Chưa chọn phòng nào</span>`;
        return;
    }

    badgesEl.innerHTML = state.selectedRooms.map(r => `
        <span class="inline-flex items-center gap-1 px-2 py-1 bg-primary/10 text-primary text-[10px] font-black uppercase rounded-lg">
            ${r.name}
            <span class="text-primary/60 font-medium normal-case">· ${r.room_type.name}</span>
            <button type="button" data-remove-id="${r.id}" class="ml-0.5 hover:text-red-500 transition-colors leading-none">✕</button>
        </span>`).join('');

    badgesEl.querySelectorAll('[data-remove-id]').forEach(btn => {
        btn.addEventListener('click', e => {
            e.stopPropagation();
            const id = parseInt(btn.dataset.removeId);
            state.selectedRooms = state.selectedRooms.filter(r => r.id !== id);
            const cb = document.querySelector(`.room-checkbox[data-room-id="${id}"]`);
            if (cb) {
                cb.checked = false;
                cb.closest('label').classList.remove('!border-primary', 'bg-primary/5');
            }
            updateModalBadges();
        });
    });
}
