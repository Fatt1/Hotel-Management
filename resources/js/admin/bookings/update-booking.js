
import { state }                                                           from './modules/state';
import { initDatePicker, toDbDatetime }                                    from './modules/date-picker';
import { openRoomModal }                                                   from './modules/room-modal';
import { openServiceModal }                                                from './modules/service-modal';
import { 
    addRoomToBooking, 
    removeRoomFromBooking
} from '../../api';

// ─── Bootstrap ───────────────────────────────────────────────────────────────

window.addEventListener('DOMContentLoaded', () => {
    loadExistingBookingData();
    
    // Only init date picker and add room button if not completed
    if (!window.isCompleted) {
        initDatePicker({ onChange: handleDateChange });
        
        const addRoomBtn = document.getElementById('add-room-btn');
        if (addRoomBtn) {
            addRoomBtn.addEventListener('click', handleAddRoomClick);
        }
    }
    
    // Listen for service modal events
    document.addEventListener('open-service-modal', (e) => {
        const { roomId } = e.detail;
        const booking = window.bookingData;
        openServiceModal(roomId, {
            bookingId: booking?.id,
            onConfirm: handleServicesUpdated
        });
    });
});

// ─── Load Existing Data ──────────────────────────────────────────────────────

function loadExistingBookingData() {
    const booking = window.bookingData;
    if (!booking) return;

    state.currentCustomer = booking.customer;

    // Rooms — from booking_details[].room
    state.selectedRooms = booking.booking_details.map(detail => detail.room);

    // Services — from booking_details[].service_usages[]
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

    // Dates — use booking.checkin_date and booking.checkout_date (master dates)
    // Fallback to first detail if not available
    if (booking.checkin_date && booking.checkout_date) {
        const checkinDate  = new Date(booking.checkin_date);
        const checkoutDate = new Date(booking.checkout_date);

        state.selectedDates = [checkinDate, checkoutDate];

        document.getElementById('checkin-time').value  = checkinDate.toTimeString().substring(0, 5);
        document.getElementById('checkout-time').value = checkoutDate.toTimeString().substring(0, 5);
    } else if (booking.booking_details.length > 0) {
        // Fallback to first detail
        const firstDetail = booking.booking_details[0];
        const checkinDate  = new Date(firstDetail.checkin_date);
        const checkoutDate = new Date(firstDetail.checkout_date);

        state.selectedDates = [checkinDate, checkoutDate];

        document.getElementById('checkin-time').value  = checkinDate.toTimeString().substring(0, 5);
        document.getElementById('checkout-time').value = checkoutDate.toTimeString().substring(0, 5);
    }
}

// ─── Handlers ────────────────────────────────────────────────────────────────

async function handleDateChange() {
    // When dates change in edit mode, just update the hidden inputs
    // The dates will be used when adding new rooms
}

async function handleAddRoomClick() {
    const checkinDatetime = document.getElementById('check_in').value;
    const checkoutDatetime = document.getElementById('check_out').value;
    
    if (!checkinDatetime || !checkoutDatetime) {
        Swal.fire({ 
            icon: 'warning', 
            title: 'Chưa chọn ngày', 
            text: 'Vui lòng chọn ngày check-in và check-out trước khi thêm phòng.' 
        });
        return;
    }

    await openRoomModal(checkinDatetime, checkoutDatetime, { 
        onConfirm: async () => {
            await addRoomsToBooking();
        }
    });
}

async function addRoomsToBooking() {
    const booking = window.bookingData;
    const checkinDatetime = document.getElementById('check_in').value;
    const checkoutDatetime = document.getElementById('check_out').value;
    
    // Find newly selected rooms (not yet in booking)
    const existingRoomIds = booking.booking_details.map(d => d.room.id);
    const newRooms = state.selectedRooms.filter(r => !existingRoomIds.includes(r.id));
    
    if (newRooms.length === 0) {
        return;
    }

    try {
        // Add each new room via API
        for (const room of newRooms) {
            await addRoomToBooking(booking.id, room.id, checkinDatetime, checkoutDatetime);
        }
        
        // Reload page to get fresh data
        window.location.reload();
    } catch (error) {
        const msg = error.response?.data?.message ?? 'Có lỗi xảy ra khi thêm phòng.';
        Swal.fire({ icon: 'error', title: 'Thất bại', text: msg });
    }
}

async function handleServicesUpdated() {
    // Services have been updated via service modal
    // Reload page to get fresh data
    window.location.reload();
}

