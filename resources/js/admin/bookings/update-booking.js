/**
 * Update Booking — main entry point.
 *
 * Similar to create-booking.js but:
 * - Customer fields are read-only
 * - Pre-loads existing booking data
 * - Single "Update" button instead of "Check-in" and "Reserve"
 */
import { state }                                                           from './modules/state';
import { initDatePicker, toDbDatetime }                                    from './modules/date-picker';
import { openRoomModal }                                                   from './modules/room-modal';
import { renderRoomList }                                                  from './modules/room-list';
import { renderPayment }                                                   from './modules/payment';
import { updateBooking }                                                   from '../../api';

// ─── Bootstrap ───────────────────────────────────────────────────────────────

window.addEventListener('DOMContentLoaded', () => {
    loadExistingBookingData();
    initDatePicker({ onChange: refresh });
    document.getElementById('add-room-btn').addEventListener('click', handleAddRoomClick);
    document.getElementById('btn-update').addEventListener('click', validateAndSubmit);
    
    // Initial render
    refresh();
});

// ─── Load Existing Data ──────────────────────────────────────────────────────

function loadExistingBookingData() {
    const bookingData = window.bookingData;
    if (!bookingData) return;

    // Set customer (read-only, already filled in HTML)
    state.currentCustomer = bookingData.customer;

    // Set selected rooms
    state.selectedRooms = bookingData.booking_details.map(detail => detail.room);
    
    // Set services for each room
    state.roomServices = {};
    bookingData.booking_details.forEach(detail => {
        const roomId = detail.room.id;
        state.roomServices[roomId] = {};
        
        detail.services.forEach(service => {
            state.roomServices[roomId][service.id] = {
                id: service.id,
                name: service.name,
                unit_price: service.unit_price,
                unit: service.unit,
                group: service.group,
                quantity: service.quantity,
            };
        });
    });

    // Set dates (will be picked up by date-picker module)
    const firstDetail = bookingData.booking_details[0];
    if (firstDetail) {
        const checkinDate = new Date(firstDetail.checkin_date);
        const checkoutDate = new Date(firstDetail.checkout_date);
        
        state.selectedDates = [checkinDate, checkoutDate];
        
        // Set time inputs
        document.getElementById('checkin-time').value = 
            checkinDate.toTimeString().substring(0, 5);
        document.getElementById('checkout-time').value = 
            checkoutDate.toTimeString().substring(0, 5);
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
    const bookingData = window.bookingData;
    
    if (state.selectedRooms.length === 0) {
        Swal.fire({ 
            icon: 'warning', 
            title: 'Chưa chọn phòng', 
            text: 'Vui lòng chọn ít nhất 1 phòng trước khi cập nhật.' 
        });
        return;
    }
    
    if (state.selectedDates.length < 2) {
        Swal.fire({ 
            icon: 'warning', 
            title: 'Chưa chọn ngày', 
            text: 'Vui lòng chọn đủ ngày check-in và check-out.' 
        });
        return;
    }

    const checkinTime  = document.getElementById('checkin-time').value;
    const checkoutTime = document.getElementById('checkout-time').value;
    const [ciDate, coDate] = state.selectedDates;

    const payload = {
        // Customer data (unchanged)
        first_name: bookingData.customer.first_name,
        last_name: bookingData.customer.last_name,
        email: bookingData.customer.email,
        phone_number: bookingData.customer.phone_number,
        country: bookingData.customer.country,
        
        // Booking data
        booking_date: toDbDatetime(ciDate, checkinTime),
        status: bookingData.status, // Keep current status
        
        // Booking details
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

    const btn = document.getElementById('btn-update');
    const originalHtml = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = `<span class="material-symbols-outlined animate-spin text-sm">progress_activity</span> Đang xử lý...`;

    try {
        const result = await updateBooking(bookingData.id, payload);
        await Swal.fire({
            icon: 'success',
            title: 'Cập nhật thành công!',
            text: `Booking #${result.booking_id} đã được cập nhật.`,
            confirmButtonText: 'Xem danh sách đặt phòng',
        });
        window.location.href = '/admin/bookings';
    } catch (error) {
        const msg = error.response?.data?.message ?? 'Có lỗi xảy ra. Vui lòng thử lại.';
        Swal.fire({ 
            icon: 'error', 
            title: 'Cập nhật thất bại', 
            text: msg 
        });
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    }
}
