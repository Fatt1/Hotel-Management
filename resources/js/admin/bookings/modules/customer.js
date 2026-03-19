import { getCustomerByEmail } from '../../../api';
import { state } from './state';

// ─── Public API ──────────────────────────────────────────────────────────────

export function initCustomerSearch() {
    const btn   = document.getElementById('search-customer-btn');
    const input = document.getElementById('customer-email-input');
    btn.addEventListener('click', () => doSearch(input.value.trim()));
    input.addEventListener('keydown', e => {
        if (e.key === 'Enter') doSearch(input.value.trim());
    });
}

export function validateCustomer() {
    if (state.currentCustomer === null) {
        Swal.fire({ icon: 'warning', title: 'Chưa tìm khách hàng', text: 'Vui lòng tìm kiếm khách hàng theo email trước khi đặt phòng.' });
        return false;
    }
    if (state.currentCustomer !== 'new') return true;

    clearCustomerErrors();
    let valid = true;

    if (!document.getElementById('nc-first-name').value.trim()) {
        setError('nc-first-name-error', 'Vui lòng nhập họ.');
        valid = false;
    }
    if (!document.getElementById('nc-last-name').value.trim()) {
        setError('nc-last-name-error', 'Vui lòng nhập tên.');
        valid = false;
    }
    const phone = document.getElementById('nc-phone').value.trim();
    if (!phone) {
        setError('nc-phone-error', 'Vui lòng nhập số điện thoại.');
        valid = false;
    } else if (!/^(0|\+84)[0-9]{9}$/.test(phone)) {
        setError('nc-phone-error', 'Số điện thoại không hợp lệ (VD: 0901234567).');
        valid = false;
    }
    if (!document.querySelector('#nc-country-field input[type="hidden"]')?.value) {
        setError('nc-country-error', 'Vui lòng chọn quốc gia.');
        valid = false;
    }

    return valid;
}

export function getCustomerSubmitData() {
    return {
        email:        document.getElementById('nc-email').value,
        phone_number: document.getElementById('nc-phone').value,
        first_name:   document.getElementById('nc-first-name').value,
        last_name:    document.getElementById('nc-last-name').value,
        country:      document.querySelector('#nc-country-field input[type="hidden"]')?.value ?? '',
    };
}

// ─── Internal ────────────────────────────────────────────────────────────────

async function doSearch(email) {
    if (!email) {
        Swal.fire({
            icon: 'warning',
            title: 'Thiếu email',
            text: 'Vui lòng nhập email trước khi tìm kiếm khách hàng.',
        });
        return;
    }

    if (!isValidEmail(email)) {
        Swal.fire({
            icon: 'warning',
            title: 'Email không hợp lệ',
            text: 'Vui lòng nhập đúng định dạng email (ví dụ: abc@gmail.com).',
        });
        return;
    }

    const btn     = document.getElementById('search-customer-btn');
    const icon    = document.getElementById('search-customer-icon');
    const section = document.getElementById('customer-info-section');

    btn.disabled = true;
    icon.textContent = 'progress_activity';
    icon.classList.add('animate-spin');

    try {
        state.currentCustomer = await getCustomerByEmail(email);
        renderExistingCustomer(state.currentCustomer);
    } catch (error) {
        if (error.response?.status === 404) {
            state.currentCustomer = 'new';
            renderNewCustomerForm(email);
        } else {
            state.currentCustomer = null;
            section.classList.add('hidden');
        }
    } finally {
        btn.disabled = false;
        icon.textContent = 'search';
        icon.classList.remove('animate-spin');
        section.classList.remove('hidden');
    }
}

export function setCountryPicker(countryValue) {
    const field  = document.getElementById('nc-country-field');
    if (!field) return;

    const hidden = field.querySelector('input[type="hidden"]');
    const nameEl = field.querySelector('.cp-trigger .cp-name');
    const flagEl = field.querySelector('.cp-trigger .cp-flag');

    if (hidden) hidden.value = countryValue ?? '';

    if (nameEl) {
        nameEl.textContent = countryValue || 'Chọn quốc gia';
        nameEl.classList.toggle('text-slate-400', !countryValue);
        nameEl.classList.toggle('text-slate-700', !!countryValue);
    }

    if (countryValue) {
        const matchingItem = document.querySelector(`.cp-item[data-value="${CSS.escape(countryValue)}"]`);
        if (matchingItem && flagEl) {
            const iso = matchingItem.dataset.iso;
            if (iso) {
                flagEl.className     = `cp-flag fi fi-${iso}`;
                flagEl.style.cssText = 'width:1.5rem;height:1.125rem;display:inline-block;flex-shrink:0;border-radius:2px';
                flagEl.textContent   = '';
            }
            matchingItem.closest('ul')?.querySelectorAll('.cp-check')
                .forEach(el => el.classList.add('invisible'));
            matchingItem.querySelector('.cp-check')?.classList.remove('invisible');
        }
    } else if (flagEl) {
        flagEl.className     = 'cp-flag material-symbols-outlined';
        flagEl.style.cssText = 'font-size:1.125rem;flex-shrink:0;color:#94a3b8';
        flagEl.textContent   = 'language';
    }
}

function renderExistingCustomer(customer) {
    document.getElementById('customer-info-card').className      = 'rounded-xl border border-green-200 bg-green-50 p-4';
    document.getElementById('customer-status-header').className  = 'flex items-center justify-between gap-2 text-green-700 mb-3';
    document.getElementById('customer-status-icon').textContent  = 'check_circle';
    document.getElementById('customer-status-text').textContent  = 'Đã tìm thấy khách hàng';

    const editLink = document.getElementById('customer-edit-link');
    editLink.href = `/admin/customers/${customer.id}/edit`;
    editLink.classList.remove('hidden');

    const readonlyClass = 'w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed';
    [['nc-first-name', customer.first_name], ['nc-last-name', customer.last_name],
     ['nc-email', customer.email],           ['nc-phone', customer.phone_number]].forEach(([id, val]) => {
        const input = document.getElementById(id);
        input.value    = val ?? '';
        input.readOnly = true;
        input.className = readonlyClass;
    });

    setCountryPicker(customer.country ?? '');
    const trigger = document.querySelector('#nc-country-field .cp-trigger');
    if (trigger) trigger.disabled = true;
}

function renderNewCustomerForm(email) {
    document.getElementById('customer-info-card').className      = 'rounded-xl border border-amber-200 bg-amber-50 p-4';
    document.getElementById('customer-status-header').className  = 'flex items-center justify-between gap-2 text-amber-700 mb-3';
    document.getElementById('customer-status-icon').textContent  = 'person_add';
    document.getElementById('customer-status-text').textContent  = 'Khách hàng chưa tồn tại — Nhập thông tin để tạo mới';
    document.getElementById('customer-edit-link').classList.add('hidden');

    const emailInput    = document.getElementById('nc-email');
    emailInput.value    = email;
    emailInput.readOnly = true;
    emailInput.className = 'w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed';

    const editableClass = 'w-full px-3 py-2 text-sm border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/30';
    ['nc-first-name', 'nc-last-name', 'nc-phone'].forEach(id => {
        const input     = document.getElementById(id);
        input.value     = '';
        input.readOnly  = false;
        input.className = editableClass;
    });

    setCountryPicker('');
    const trigger = document.querySelector('#nc-country-field .cp-trigger');
    if (trigger) trigger.disabled = false;

    clearCustomerErrors();
}

function clearCustomerErrors() {
    ['nc-first-name-error', 'nc-last-name-error', 'nc-email-error', 'nc-phone-error', 'nc-country-error']
        .forEach(id => setError(id, ''));
}

function setError(spanId, message) {
    const span = document.getElementById(spanId);
    if (!span) return;
    span.textContent = message;
    span.classList.toggle('hidden', !message);
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}
