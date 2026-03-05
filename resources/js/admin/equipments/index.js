import axios from "axios";
import Swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", function () {
    // Delete btn
    const deleteButtons = document.querySelectorAll(".delete-equipment-btn");
    deleteButtons.forEach((button) => {
        button.addEventListener("click", (e) => {
            e.preventDefault();
            const equipmentId = button.getAttribute("data-equipment-id");
            const equipmentName = button.getAttribute("data-equipment-name");
            Swal.fire({
                icon: "warning",
                title: "Xác nhận",
                text: `Bạn có chắc muốn xóa thiết bị "${equipmentName}" không?`,
                confirmButtonText: "Xóa",
                showDenyButton: true,
                denyButtonText: "Hủy",
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteEquipment(equipmentId);
                }
            });
        });
    });

    function deleteEquipment(equipmentId) {
        axios
            .delete(`/admin/equipments/${equipmentId}`)
            .then((response) => {
                Swal.fire({
                    icon: "success",
                    title: "Thành công",
                    text: "Thiết bị đã được xóa!",
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            })
            .catch((error) => {
                console.error("Error deleting equipment:", error);
                Swal.fire({
                    icon: "error",
                    title: "Lỗi",
                    text: error.response?.data?.message || "Không thể xóa thiết bị này. Vui lòng thử lại.",
                });
            });
    }
});
