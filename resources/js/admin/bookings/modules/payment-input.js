
import { formatVND } from '../../../util';

let _total = 0;
let _alreadyPaid = 0;

export function initPaymentInput(alreadyPaid = 0) {
    _alreadyPaid = alreadyPaid;
    const amountInput = document.getElementById('payment-amount');
    if (!amountInput) return;
    amountInput.addEventListener('input', _renderRemaining);
}

/**
 * Returns payment data if amount > 0, otherwise null (no payment to save).
 */
export function getPaymentData() {
    const amount = parseFloat(document.getElementById('payment-amount')?.value ?? '0') || 0;
    if (amount <= 0) return null;

    return {
        amount,
        method:   document.getElementById('payment-method')?.value   ?? 'cash',
        currency: document.getElementById('payment-currency')?.value ?? 'VND',
    };
}

/**
 * Called by payment.js whenever the grand total changes (room/date/service updates).
 */
export function updateRemaining(total) {
    _total = total;
    _renderRemaining();
}

function _renderRemaining() {
    const el = document.getElementById('payment-remaining');
    if (!el) return;

    const paid      = (parseFloat(document.getElementById('payment-amount')?.value ?? '0') || 0) + _alreadyPaid;
    const remaining = _total - paid;

    el.textContent = `${formatVND(remaining)} đ`;
    el.className   = remaining > 0
        ? 'text-sm font-bold text-rose-500'
        : 'text-sm font-bold text-emerald-600';
}
