
import { state }                                                           from './modules/state';
import { initDatePicker, toDbDatetime }                                    from './modules/date-picker';
import { openRoomModal }                                                   from './modules/room-modal';
import { openServiceModal }                                                from './modules/service-modal';
import { 
    addRoomToBooking, 
    removeRoomFromBooking,
    updateRoomDates
} from '../../api';

// ─── Bootstrap ───────────────────────────────────────────────────────────────

window.addEventListener('DOMContentLoaded', () => {
    initializeContextData();
    loadExistingBookingData();
    bindDelegatedActions();
    
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

function bindDelegatedActions() {
    document.addEventListener('click', async (event) => {
        const actionButton = event.target.closest('button[data-action]');
        if (!actionButton) {
            return;
        }

        const { action } = actionButton.dataset;

        if (action === 'remove-room') {
            await removeRoom(
                Number(actionButton.dataset.bookingId),
                Number(actionButton.dataset.roomId),
            );
            return;
        }

        if (action === 'edit-room-dates') {
            await openEditDateModal(
                Number(actionButton.dataset.bookingId),
                Number(actionButton.dataset.roomId),
                actionButton.dataset.checkinDate,
                actionButton.dataset.checkoutDate,
                actionButton.dataset.bookingStatus,
            );
            return;
        }

        if (action === 'add-room-service') {
            openServiceModalForRoom(Number(actionButton.dataset.roomId));
        }
    });
}

function initializeContextData() {
    if (window.bookingData && typeof window.isCompleted !== 'undefined') {
        return;
    }

    const contextElement = document.getElementById('booking-edit-context');
    if (!contextElement) {
        return;
    }

    try {
        window.bookingData = JSON.parse(contextElement.dataset.booking ?? '{}');
    } catch (error) {
        console.error('Không thể parse booking data:', error);
        window.bookingData = null;
    }

    window.isCompleted = contextElement.dataset.isCompleted === 'true';
}

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

async function removeRoom(bookingId, roomId) {
    const result = await Swal.fire({
        title: 'Xác nhận xóa phòng?',
        text: 'Bạn có chắc muốn xóa phòng này khỏi booking?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
    });

    if (!result.isConfirmed) {
        return;
    }

    try {
        await removeRoomFromBooking(bookingId, roomId);
        window.location.reload();
    } catch (error) {
        const msg = error.response?.data?.message ?? 'Có lỗi xảy ra khi xóa phòng.';
        Swal.fire({ icon: 'error', title: 'Thất bại', text: msg });
    }
}

function openServiceModalForRoom(roomId) {
    const event = new CustomEvent('open-service-modal', { detail: { roomId } });
    document.dispatchEvent(event);
}

function formatDateTimeLocal(dateString) {
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hours}:${minutes}`;
}

async function openEditDateModal(bookingId, roomId, checkinDate, checkoutDate, bookingStatus) {
    const isOccupied = bookingStatus === 'Đang ở';

    const { value: formValues } = await Swal.fire({
        title: 'Cập nhật thời gian',
        html: `
            <div class="space-y-4 text-left">
                ${isOccupied ? '<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-3"><p class="text-xs text-yellow-700">Booking đang ở - chỉ có thể cập nhật ngày checkout</p></div>' : ''}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Check-in</label>
                    <input type="datetime-local" id="swal-checkin"
                        value="${formatDateTimeLocal(checkinDate)}"
                        ${isOccupied ? 'disabled' : ''}
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg ${isOccupied ? 'bg-gray-100 cursor-not-allowed' : ''}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Check-out</label>
                    <input type="datetime-local" id="swal-checkout"
                        value="${formatDateTimeLocal(checkoutDate)}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Cập nhật',
        cancelButtonText: 'Hủy',
        preConfirm: () => {
            const checkin = document.getElementById('swal-checkin').value;
            const checkout = document.getElementById('swal-checkout').value;

            if (!checkin || !checkout) {
                Swal.showValidationMessage('Vui lòng nhập đầy đủ thông tin');
                return false;
            }

            if (new Date(checkout) <= new Date(checkin)) {
                Swal.showValidationMessage('Ngày checkout phải sau ngày checkin');
                return false;
            }

            return { checkin, checkout };
        }
    });

    if (!formValues) {
        return;
    }

    try {
        await updateRoomDates(bookingId, roomId, formValues.checkin, formValues.checkout);
        window.location.reload();
    } catch (error) {
        const msg = error.response?.data?.message ?? 'Có lỗi xảy ra khi cập nhật ngày.';
        Swal.fire({ icon: 'error', title: 'Thất bại', text: msg });
    }
}

