import axios from "axios";

document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("deleteMaintenanceTicketModal");
    const confirmDeleteBtn = document.getElementById("confirmDeleteMaintenanceTicketBtn");
    const cancelDeleteBtn = document.getElementById("cancelDeleteMaintenanceTicketBtn");
    const deleteTicketCode = document.getElementById("deleteTicketCode");

    let currentTicketId = null;
    const deleteButtons = document.querySelectorAll(".delete-maintenance-ticket-btn");

    deleteButtons.forEach((button) => {
        button.addEventListener("click", (event) => {
            event.preventDefault();

            const id = button.getAttribute("data-ticket-id");
            const code = button.getAttribute("data-ticket-code");
            currentTicketId = id;
            deleteTicketCode.textContent = code;

            modal.classList.remove("hidden");
            modal.classList.add("flex");
        });
    });

    cancelDeleteBtn?.addEventListener("click", () => {
        modal.classList.add("hidden");
        modal.classList.remove("flex");
        currentTicketId = null;
    });

    modal?.addEventListener("click", (event) => {
        if (event.target.id === "deleteMaintenanceTicketModal") {
            modal.classList.add("hidden");
            modal.classList.remove("flex");
            currentTicketId = null;
        }
    });

    confirmDeleteBtn?.addEventListener("click", () => {
        if (!currentTicketId) {
            return;
        }

        const originalText = confirmDeleteBtn.textContent;
        confirmDeleteBtn.disabled = true;
        confirmDeleteBtn.textContent = "Đang xóa...";

        deleteTicket(currentTicketId).finally(() => {
            confirmDeleteBtn.disabled = false;
            confirmDeleteBtn.textContent = originalText;
        });
    });

    function deleteTicket(id) {
        return axios
            .delete(`/admin/maintenance-tickets/${id}`)
            .then((response) => {
                modal.classList.add("hidden");
                modal.classList.remove("flex");
                currentTicketId = null;

                const alertDiv = document.createElement("div");
                alertDiv.className = "fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-40";
                alertDiv.textContent = response.data?.message || "Xóa phiếu sửa chữa thành công";
                document.body.appendChild(alertDiv);

                setTimeout(() => {
                    window.location.reload();
                }, 1200);
            })
            .catch((error) => {
                const alertDiv = document.createElement("div");
                alertDiv.className = "fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-40";
                alertDiv.textContent =
                    error.response?.data?.message ||
                    "Không thể xóa phiếu sửa chữa. Vui lòng thử lại.";
                document.body.appendChild(alertDiv);

                setTimeout(() => {
                    alertDiv.remove();
                }, 2500);
            });
    }
});
