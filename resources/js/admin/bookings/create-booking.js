
/**
 * Create Booking — main entry point.
 *
 * Wires together the reusable modules:
 *   state        — shared mutable state
 *   date-picker  — flatpickr date/time range
 *   customer     — email search + form rendering
 *   room-modal   — select available rooms popup
 *   room-list    — render chosen rooms with services on page
 *   payment      — billing breakdown sidebar
 *   service-modal — (opened from room-list, no direct reference needed here)
 */
import { state }                                                           from './modules/state';
import { initDatePicker, toDbDatetime }                                    from './modules/date-picker';
import { initCustomerSearch, validateCustomer, getCustomerSubmitData }     from './modules/customer';
import { openRoomModal }                                                   from './modules/room-modal';
import { renderRoomList }                                                  from './modules/room-list';
import { renderPayment }                                                   from './modules/payment';
import { initPaymentInput, getPaymentData }                                from './modules/payment-input';
import { createBooking, getRoomById }                                      from '../../api';

// ─── Bootstrap ───────────────────────────────────────────────────────────────

window.addEventListener('DOMContentLoaded', () => {
    initCustomerSearch();
    initDatePicker({ onChange: refresh });
    initPaymentInput();
    document.getElementById('add-room-btn').addEventListener('click', handleAddRoomClick);
    document.getElementById('btn-checkin').addEventListener('click', () => validateAndSubmit('Đang ở'));
    document.getElementById('btn-reserve').addEventListener('click', () => validateAndSubmit('Đã đặt'));
    refresh();
    preselectRoomFromQuery();
});

// ─── Helpers ─────────────────────────────────────────────────────────────────

function refresh() {
    renderRoomList({ onRemoveRoom: handleRemoveRoom, onServicesUpdated: refresh });
    renderPayment();
    updateCheckinButtonState();
}

function getSelectedBookingDatetime() {
    if (state.selectedDates.length < 1) {
        return null;
    }

    const checkinTime = document.getElementById('checkin-time').value || '00:00';
    const [h, m] = checkinTime.split(':').map(v => parseInt(v, 10));
    const checkinDate = new Date(state.selectedDates[0]);
    checkinDate.setHours(Number.isFinite(h) ? h : 0, Number.isFinite(m) ? m : 0, 0, 0);

    return checkinDate;
}

function canCheckinNow() {
    const bookingDatetime = getSelectedBookingDatetime();
    if (!bookingDatetime) {
        return false;
    }
    return bookingDatetime.getTime() <= Date.now();
}

function updateCheckinButtonState() {
    const btnCheckin = document.getElementById('btn-checkin');
    if (!btnCheckin) {
        return;
    }

    const allowed = canCheckinNow();
    btnCheckin.disabled = !allowed;
    btnCheckin.classList.toggle('opacity-60', !allowed);
    btnCheckin.classList.toggle('cursor-not-allowed', !allowed);
    btnCheckin.title = allowed
        ? ''
        : 'Không thể nhận phòng khi thời gian đặt lớn hơn thời gian hiện tại';
}

function handleRemoveRoom(roomId) {
    state.selectedRooms = state.selectedRooms.filter(r => Number(r.id) !== Number(roomId));
    delete state.roomServices[roomId];
    refresh();
}

async function handleAddRoomClick() {
    await openRoomModal(
        document.getElementById('check_in').value,
        document.getElementById('check_out').value,
        { onConfirm: refresh }
    );
}

async function preselectRoomFromQuery() {
    const params = new URLSearchParams(window.location.search);
    const roomId = Number(params.get('room'));

    if (!Number.isInteger(roomId) || roomId <= 0) {
        return;
    }

    if (state.selectedRooms.some(r => Number(r.id) === roomId)) {
        return;
    }

    try {
        const room = await getRoomById(roomId);
        if (!room) {
            return;
        }

        state.selectedRooms.push(room);
        refresh();
    } catch (error) {
        const msg = error.response?.data?.message ?? 'Không thể lấy thông tin phòng từ layout-room.';
        Swal.fire({ icon: 'warning', title: 'Không tự động thêm phòng', text: msg });
    }
}

// ─── Submit ───────────────────────────────────────────────────────────────────

async function validateAndSubmit(status) {
    if (!validateCustomer()) return;

    if (state.selectedRooms.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Chưa chọn phòng', text: 'Vui lòng chọn ít nhất 1 phòng trước khi đặt.' });
        return;
    }
    if (state.selectedDates.length < 2) {
        Swal.fire({ icon: 'warning', title: 'Chưa chọn ngày', text: 'Vui lòng chọn đủ ngày check-in và check-out.' });
        return;
    }

    if (status === 'Đang ở' && !canCheckinNow()) {
        Swal.fire({
            icon: 'warning',
            title: 'Không thể nhận phòng',
            text: 'Ngày giờ check-in đang lớn hơn thời điểm hiện tại.',
        });
        return;
    }

    const checkinTime  = document.getElementById('checkin-time').value;
    const checkoutTime = document.getElementById('checkout-time').value;
    const [ciDate, coDate] = state.selectedDates;

    const payload = {
        ...getCustomerSubmitData(),
        booking_date: toDbDatetime(ciDate, checkinTime),
        status,
        booking_details: state.selectedRooms.map(room => ({
            room_id:       room.id,
            checkin_date:  toDbDatetime(ciDate, checkinTime),
            checkout_date: toDbDatetime(coDate, checkoutTime),
            services: Object.values(state.roomServices[room.id] ?? {}).map(s => ({
                service_id: s.id,
                quantity:   s.quantity,

            })),
        })),
        payment: getPaymentData(),
    };

    const isCheckin = status === 'Đang ở';
    const btnId     = isCheckin ? 'btn-checkin' : 'btn-reserve';
    const btn       = document.getElementById(btnId);
    const otherBtn  = document.getElementById(isCheckin ? 'btn-reserve' : 'btn-checkin');

    btn.disabled = true;
    otherBtn.disabled = true;
    btn.innerHTML = `<span class="material-symbols-outlined animate-spin text-sm">progress_activity</span> Đang xử lý...`;

    try {
        const result = await createBooking(payload);
        await Swal.fire({
            icon: 'success',
            title: isCheckin ? 'Nhận phòng thành công!' : 'Đặt phòng thành công!',
            text: `Mã đặt phòng: #${result.booking_id}`,
            confirmButtonText: 'Xem danh sách đặt phòng',
        });
        window.location.href = '/admin/bookings';
    } catch (error) {
        const msg = error.response?.data?.message ?? 'Có lỗi xảy ra. Vui lòng thử lại.';
        Swal.fire({ icon: 'error', title: 'Thao tác thất bại', text: msg });
    } finally {
        btn.disabled = false;
        otherBtn.disabled = false;
        btn.innerHTML = isCheckin
            ? `<span class="material-symbols-outlined text-sm">door_front</span> Nhận phòng`
            : `<span class="material-symbols-outlined text-sm">bookmark_add</span> Đặt phòng`;
        updateCheckinButtonState();
    }
}
