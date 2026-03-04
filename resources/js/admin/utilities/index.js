import axios from "axios";
import Swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", function () {
    // Delete btn
    const deleteButtons = document.querySelectorAll(".delete-utility-btn");
    deleteButtons.forEach((button) => {
        button.addEventListener("click", (e) => {
            e.preventDefault();
            const utilityId = button.getAttribute("data-utility-id");
            const utilityName = button.getAttribute("data-utility-name");
            Swal.fire({
                icon: "warning",
                title: "Xác nhận",
                text: `Bạn có chắc muốn xóa tiện ích "${utilityName}" không?`,
                confirmButtonText: "Xóa",
                showDenyButton: true,
                denyButtonText: "Hủy",
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteUtility(utilityId);
                }
            });
        });
    });

    function deleteUtility(utilityId) {
        axios
            .delete(`/admin/utilities/${utilityId}`)
            .then((response) => {
                Swal.fire({
                    icon: "success",
                    title: "Thành công",
                    text: "Tiện ích đã được xóa!",
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            })
            .catch((error) => {
                console.error("Error deleting utility:", error);
                Swal.fire({
                    icon: "error",
                    title: "Lỗi",
                    text: error.response?.data?.message || "Không thể xóa tiện ích này. Vui lòng thử lại.",
                });
            });
    }
});
