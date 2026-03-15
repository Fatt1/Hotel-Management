import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
import { Vietnamese } from 'flatpickr/dist/l10n/vn.js';
import { state } from './state';

export function formatDisplay(date) {
    const d = String(date.getDate()).padStart(2, '0');
    const m = String(date.getMonth() + 1).padStart(2, '0');
    return `${d}/${m}/${date.getFullYear()}`;
}

export function toDbDatetime(date, time) {
    const d = String(date.getDate()).padStart(2, '0');
    const m = String(date.getMonth() + 1).padStart(2, '0');
    return `${date.getFullYear()}-${m}-${d} ${time}:00`;
}

function isToday(date) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const compare = new Date(date);
    compare.setHours(0, 0, 0, 0);
    return compare.getTime() === today.getTime();
}

function getSmartCheckinTime(date) {
    if (isToday(date)) {
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        return `${h}:${m}`;
    }
    return '14:00';
}

export function syncHiddenInputs() {
    const checkinTime  = document.getElementById('checkin-time').value;
    const checkoutTime = document.getElementById('checkout-time').value;
    const [ci, co] = state.selectedDates;
    if (ci) document.getElementById('check_in').value  = toDbDatetime(ci, checkinTime);
    if (co) document.getElementById('check_out').value = toDbDatetime(co, checkoutTime);
}

export function initDatePicker({ onChange }) {
    const checkin = new Date();
    checkin.setHours(0, 0, 0, 0);
    const checkout = new Date(checkin);
    checkout.setDate(checkout.getDate() + 1);
    state.selectedDates = [checkin, checkout];

    // Set giờ check-in thông minh: hôm nay = giờ hiện tại, tương lai = 14:00
    document.getElementById('checkin-time').value = getSmartCheckinTime(checkin);

    document.getElementById('checkin-display').textContent  = formatDisplay(checkin);
    document.getElementById('checkout-display').textContent = formatDisplay(checkout);
    syncHiddenInputs();

    const fpRange = flatpickr('#flatpickr-range', {
        mode: 'range',
        showMonths: 2,
        locale: Vietnamese,
        defaultDate: [checkin, checkout],
        dateFormat: 'Y-m-d',
        minDate: 'today',
        positionElement: document.getElementById('date-range-bar'),
        onReady(_, __, fp) {
            fp.calendarContainer.classList.add('booking-datepicker');
        },
        onChange(dates) {
            state.selectedDates = dates;
            if (dates.length >= 1) {
                document.getElementById('checkin-display').textContent = formatDisplay(dates[0]);
                // Tự động update giờ check-in khi chọn ngày mới
                document.getElementById('checkin-time').value = getSmartCheckinTime(dates[0]);
            }
            if (dates.length === 2) document.getElementById('checkout-display').textContent = formatDisplay(dates[1]);
            syncHiddenInputs();
            onChange?.();
        },
    });

    document.getElementById('checkin-display').addEventListener('click', () => fpRange.open());
    document.getElementById('checkout-display').addEventListener('click', () => fpRange.open());

    ['checkin-time', 'checkout-time'].forEach(id => {
        const el = document.getElementById(id);
        el.addEventListener('click', e => e.stopPropagation());
        el.addEventListener('change', () => { syncHiddenInputs(); onChange?.(); });
    });
}
