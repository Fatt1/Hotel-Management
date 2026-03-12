document.addEventListener('DOMContentLoaded', function() {
    const counters = {
        checkin_early: window.GeneralConfig?.checkinEarlyCount ?? 0,
        checkout_late: window.GeneralConfig?.checkoutLateCount ?? 0,
    };
    window.addRow = function(type, containerId) {
        const container = document.getElementById(containerId);
        if(!container) return;

        const index = counters[type]++;
        const row = document.createElement('div');
        row.className = 'flex items-center gap-3';
        row.innerHTML = `
            <input type="number" name="${type}[${index}][hour_mark]"  placeholder="Giờ"
                class="w-50 px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-900/20 outline-none" />
            <span class="text-sm text-slate-400 font-medium">VND</span>
            <input type="number" name="${type}[${index}][price]"  placeholder="Giá"
                class="flex-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-900/20 outline-none text-right" />
            <button type="button" onclick="removeRow(this)"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        `;
        container.appendChild(row);
    };

    window.removeRow = function(button) {
        button.closest('div.flex').remove();
    };
});