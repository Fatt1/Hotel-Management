
import { state }                                                           from './modules/state';
import { initDatePicker, toDbDatetime }                                    from './modules/date-picker';
import { openRoomModal }                                                   from './modules/room-modal';
import { renderRoomList }                                                  from './modules/room-list';
import { renderPayment }                                                   from './modules/payment';
import { initPaymentInput }                                                from './modules/payment-input';
import { updateBooking }                                                   from '../../api';

// ─── Bootstrap ───────────────────────────────────────────────────────────────

window.addEventListener('DOMContentLoaded', () => {
    loadExistingBookingData();
    initDatePicker({ onChange: refresh });
    const alreadyPaid = (window.bookingData?.payments ?? []).reduce((sum, p) => sum + parseFloat(p.amount ?? 0), 0);
    initPaymentInput(alreadyPaid);
    document.getElementById('add-room-btn').addEventListener('click', handleAddRoomClick);
    document.getElementById('btn-update').addEventListener('click', validateAndSubmit);
    refresh();
});

// ─── Load Existing Data ──────────────────────────────────────────────────────

function loadExistingBookingData() {
    const booking = window.bookingData;
    if (!booking) return;

    state.currentCustomer = booking.customer;

    // Rooms — from booking_details[].room (Eloquent relationship key)
    state.selectedRooms = booking.booking_details.map(detail => detail.room);

    // Services — from booking_details[].service_usages[].service (Eloquent)
    state.roomServices = {};
    booking.booking_details.forEach(detail => {
        const roomId = detail.room.id;
        state.roomServices[roomId] = {};

        (detail.service_usages ?? []).forEach(usage => {
            const svc = usage.service;
            state.roomServices[roomId][svc.id] = {
                id:         svc.id,
                name:       svc.name,
                unit_price: parseFloat(usage.unit_price),
                unit:       svc.unit,
                group:      svc.group?.name ?? '',
                quantity:   usage.quantity,
            };
        });
    });

    // Dates
    const firstDetail = booking.booking_details[0];
    if (firstDetail) {
        const checkinDate  = new Date(firstDetail.checkin_date);
        const checkoutDate = new Date(firstDetail.checkout_date);

        state.selectedDates = [checkinDate, checkoutDate];

        document.getElementById('checkin-time').value  = checkinDate.toTimeString().substring(0, 5);
        document.getElementById('checkout-time').value = checkoutDate.toTimeString().substring(0, 5);
    }
}

// ─── Helpers ─────────────────────────────────────────────────────────────────

function refresh() {
    renderRoomList({ onRemoveRoom: refresh, onServicesUpdated: refresh });
    renderPayment();
}

async function handleAddRoomClick() {
    await openRoomModal(
        document.getElementById('check_in').value,
        document.getElementById('check_out').value,
        { onConfirm: refresh }
    );
}

// ─── Submit ───────────────────────────────────────────────────────────────────

async function validateAndSubmit() {
    const booking = window.bookingData;

    if (state.selectedRooms.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Chưa chọn phòng', text: 'Vui lòng chọn ít nhất 1 phòng trước khi cập nhật.' });
        return;
    }
    if (state.selectedDates.length < 2) {
        Swal.fire({ icon: 'warning', title: 'Chưa chọn ngày', text: 'Vui lòng chọn đủ ngày check-in và check-out.' });
        return;
    }

    const checkinTime  = document.getElementById('checkin-time').value;
    const checkoutTime = document.getElementById('checkout-time').value;
    const [ciDate, coDate] = state.selectedDates;

    const payload = {
        first_name:   booking.customer.first_name,
        last_name:    booking.customer.last_name,
        email:        booking.customer.email,
        phone_number: booking.customer.phone_number,
        country:      booking.customer.country,
        booking_date: toDbDatetime(ciDate, checkinTime),
        status:       booking.status,
        booking_details: state.selectedRooms.map(room => ({
            room_id:       room.id,
            checkin_date:  toDbDatetime(ciDate, checkinTime),
            checkout_date: toDbDatetime(coDate, checkoutTime),
            services: Object.values(state.roomServices[room.id] ?? {}).map(s => ({
                service_id: s.id,
                quantity:   s.quantity,
            })),
        })),
    };

    const btn         = document.getElementById('btn-update');
    const originalHtml = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = `<span class="material-symbols-outlined animate-spin text-sm">progress_activity</span> Đang xử lý...`;

    try {
        const result = await updateBooking(booking.id, payload);
        await Swal.fire({
            icon: 'success',
            title: 'Cập nhật thành công!',
            text: `Booking #${result.booking_id} đã được cập nhật.`,
            confirmButtonText: 'Xem danh sách đặt phòng',
        });
        window.location.href = '/admin/bookings';
    } catch (error) {
        const msg = error.response?.data?.message ?? 'Có lỗi xảy ra. Vui lòng thử lại.';
        Swal.fire({ icon: 'error', title: 'Cập nhật thất bại', text: msg });
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    }
}

