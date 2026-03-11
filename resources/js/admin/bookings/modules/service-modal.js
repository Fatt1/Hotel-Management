/**
 * Service selection modal — reusable.
 * Used in: create booking room card, booking detail (future).
 *
 * State is persisted in state.roomServices[roomId] between opens.
 */
import { openModal, closeModal } from '../../../app';
import { formatVND } from '../../../util';
import { getAllServicesApi } from '../../../api';
import { state } from './state';

// Cache services list after first API call
let _servicesCache = null;

/**
 * @param {number} roomId
 * @param {{ onConfirm?: () => void }} options
 */

export async function openServiceModal(roomId, { onConfirm } = {}) {
    if (_servicesCache === null) {
        const result = await getAllServicesApi();
        if (result?.length) _servicesCache = result;
    }
    if (!_servicesCache?.length) return;

    // Work on a draft copy — only committed to state on confirm
    const draft = JSON.parse(JSON.stringify(state.roomServices[roomId] ?? {}));

    openModal(buildModalHtml(_servicesCache, draft));
    attachQtyListeners(_servicesCache, draft);

    // Search filter
    document.getElementById('service-search').addEventListener('input', e => {
        const query = e.target.value.toLowerCase().trim();
        document.getElementById('service-list').innerHTML = buildServiceItems(_servicesCache, draft, query);
        attachQtyListeners(_servicesCache, draft);
    });

    // Close buttons
    document.querySelectorAll('.service-modal-close').forEach(btn =>
        btn.addEventListener('click', closeModal)
    );

    // Confirm — persist draft (only non-zero qty) back to state
    document.getElementById('confirm-services-btn').addEventListener('click', () => {
        state.roomServices[roomId] = Object.fromEntries(
            Object.entries(draft).filter(([, svc]) => svc.quantity > 0)
        );
        closeModal();
        onConfirm?.();
    });
}

// ─── HTML builders ───────────────────────────────────────────────────────────

function buildModalHtml(services, draft) {
    return `
    <div class="w-full max-w-lg">
        <div class="p-6 pb-4 flex items-center justify-between border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-xl">room_service</span>
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">Thêm dịch vụ</h2>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Chọn số lượng cho từng dịch vụ</p>
                </div>
            </div>
            <button class="service-modal-close p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
                <span class="material-symbols-outlined text-slate-400">close</span>
            </button>
        </div>

        <div class="px-6 pt-4 pb-2">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none">search</span>
                <input id="service-search" type="text"
                    placeholder="Tìm kiếm dịch vụ (Mini bar, Giặt là, Spa...)"
                    class="w-full pl-9 pr-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-700 dark:text-slate-300" />
            </div>
        </div>

        <div id="service-list" class="overflow-y-auto px-6 py-3 max-h-[45vh] custom-scrollbar space-y-2">
            ${buildServiceItems(services, draft)}
        </div>

        <div class="p-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
            <button class="service-modal-close px-5 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-200 transition-all">
                Hủy
            </button>
            <button id="confirm-services-btn"
                class="px-6 py-2.5 bg-primary text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">check_circle</span>
                Xác nhận thêm
            </button>
        </div>
    </div>`;
}

function buildServiceItems(services, draft, query = '') {
    const list = query
        ? services.filter(s => s.name.toLowerCase().includes(query))
        : services;

    if (list.length === 0) {
        return `
        <div class="text-center py-8 text-slate-400">
            <span class="material-symbols-outlined text-3xl block mb-2">search_off</span>
            <p class="text-sm">Không tìm thấy dịch vụ</p>
        </div>`;
    }

    return list.map(svc => {
        const qty  = draft[svc.id]?.quantity ?? 0;
        const unit = svc.unit ?? 'lần';
        return `
        <div class="flex items-center gap-4 p-3 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl hover:border-primary/30 transition-colors">
            <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-primary text-base">room_service</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">${svc.name}</p>
                <p class="text-xs font-medium text-primary">
                    ${formatVND(svc.unit_price)}đ
                    <span class="text-slate-400 font-normal">/ ${unit}</span>
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" data-svc-dec="${svc.id}"
                    class="w-7 h-7 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-600 text-slate-500 hover:border-red-400 hover:text-red-500 transition-colors font-bold text-base leading-none">−</button>
                <span data-svc-qty="${svc.id}" class="w-6 text-center text-sm font-bold text-slate-900 dark:text-white">${qty}</span>
                <button type="button" data-svc-inc="${svc.id}"
                    class="w-7 h-7 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-600 text-slate-500 hover:border-primary hover:text-primary transition-colors font-bold text-base leading-none">+</button>
            </div>
        </div>`;
    }).join('');
}

function attachQtyListeners(services, draft) {
    document.querySelectorAll('[data-svc-inc]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id  = parseInt(btn.dataset.svcInc);
            const svc = services.find(s => s.id === id);
            if (!svc) return;
            if (!draft[id]) draft[id] = { ...svc, quantity: 0 };
            draft[id].quantity++;
            const el = document.querySelector(`[data-svc-qty="${id}"]`);
            if (el) el.textContent = draft[id].quantity;
        });
    });

    document.querySelectorAll('[data-svc-dec]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = parseInt(btn.dataset.svcDec);
            if (!draft[id] || draft[id].quantity <= 0) return;
            draft[id].quantity--;
            const el = document.querySelector(`[data-svc-qty="${id}"]`);
            if (el) el.textContent = draft[id].quantity;
        });
    });
}
