import axios from "axios";
import Swal from "sweetalert2";
import { closeModal, openModal } from "../../app";

function getDeleteModalHtml(customerId) {
    return `
        <div id="deleteCustomerModal">
            <div class="flex justify-between items-center pb-4 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-red-500">warning</span>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900">Xác nhận xóa</h2>
                </div>
                <button id="close-delete-modal-btn"
                    class="p-2 hover:bg-slate-100 rounded-xl transition-colors text-slate-400 cursor-pointer">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="mt-4 mb-6">
                <p class="text-slate-600 text-sm leading-relaxed">
                    Bạn có chắc chắn muốn xóa khách hàng này không?
                    Mọi dữ liệu liên quan sẽ bị loại bỏ khỏi hệ thống và không thể khôi phục.
                </p>
            </div>
            <div class="flex justify-end gap-3">
                <button id="cancel-delete-btn"
                    class="cursor-pointer px-5 py-2.5 border border-slate-300 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-colors">
                    Quay lại
                </button>
                <button id="confirm-delete-btn" data-customer-id="${customerId}"
                    class="cursor-pointer px-5 py-2.5 bg-rose-500 text-white rounded-xl text-sm font-bold hover:bg-rose-600 transition-colors">
                    Xác nhận xóa
                </button>
            </div>
        </div>`;
}

function deleteCustomer(customerId) {
    axios.delete(`/admin/customers/${customerId}`)
        .then(() => {
            closeModal();
            Swal.fire({
                icon: "success",
                title: "Thành công",
                text: "Khách hàng đã được xóa thành công.",
                confirmButtonColor: "#1e3a8a"
            }).then(() => {
                window.location.reload();
            });
        })
        .catch((error) => {
            closeModal();
            Swal.fire({
                icon: "error",
                title: "Không thể xóa",
                text: error.response?.data?.message ||"Đã xảy ra lỗi khi xóa khách hàng.",
                confirmButtonColor: "#1e3a8a"
            });
        });
}

document.addEventListener("DOMContentLoaded", function () {
    const flashSuccess = document.getElementById("flash-success");
    const flashError = document.getElementById("flash-error");
    if (flashSuccess) {
        Swal.fire({
            icon: "success",
            title: "Thành công",
            text: flashSuccess.dataset.message,
            confirmButtonColor: "#1e3a8a"
        });
    }
    if (flashError) {
        Swal.fire({
            icon: "error",
            title: "Lỗi",
            text: flashError.dataset.message,
            confirmButtonColor: "#1e3a8a"
        });
    }

    document.getElementById("sort-btn")?.addEventListener("click", function () {
        const dirInput = document.getElementById("sort-dir-input");
        dirInput.value = dirInput.value === "desc" ? "asc" : "desc";
        document.getElementById("filter-form").submit();
    });

    const deleteButtons = document.querySelectorAll(".btn-delete-customer");
    deleteButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const customerId = button.getAttribute("data-customer-id");
            openModal(getDeleteModalHtml(customerId));

            document.getElementById("close-delete-modal-btn").addEventListener("click", closeModal);
            document.getElementById("cancel-delete-btn").addEventListener("click", closeModal);
            document.getElementById("confirm-delete-btn").addEventListener("click", function () {
                deleteCustomer(customerId);
            });
        }); 
    });
});