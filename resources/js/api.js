import axios from "axios";

export async function getCustomerByEmail(email) {
    const response = await axios.get("/admin/customers/email", {
        params: { email },
    });
    return response.data;
}

export async function getRoomTypes() {
    try {
        const response = await axios.get("/admin/room-types/all");
        return response.data;
    } catch (error) {
        console.error("Error fetching room types:", error);
        alert("Không thể lấy dữ liệu loại phòng. Vui lòng thử lại.");
        return null;
    }
}

export async function createBooking(payload) {
    try {
        const response = await axios.post("/admin/bookings", payload);
        return response.data;
    } catch (error) {
        console.error("Error creating booking:", error);
        alert("Không thể tạo đơn đặt phòng. Vui lòng thử lại.");
        return null;
    }
}

export async function getAllRoomsApi(
    checkinDate,
    checkoutDate,
    roomTypeId = null,
    floorId = null,
) {
    // Gọi API để lấy danh sách phòng trống dựa trên ngày check-in, check-out, loại phòng và tầng
    try {
        const repsonse = await axios.get("/admin/rooms/available", {
            params: {
                checkin_date: checkinDate,
                checkout_date: checkoutDate,
                room_type_id: roomTypeId,
                floor_id: floorId,
            },
        });
        return repsonse.data;
    } catch (error) {
        console.error("Lỗi khi lấy danh sách phòng trống:", error);
    }
}
