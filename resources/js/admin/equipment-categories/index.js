import axios from "axios";
import Swal from "sweetalert2";
import { openModal, closeModal } from "../../app";

function getFormModalHtml(category = null) {
    const isEdit = category !== null;
    const modalTitle = isEdit ? "Chỉnh sửa nhóm thiết bị" : "Thêm nhóm thiết bị mới";
    const submitButtonText = isEdit ? "Cập nhật" : "Lưu thông tin";
    
    return `
        <div id='categoryFormModal'>
            <div class="flex justify-between items-center pb-4 border-b border-slate-200">
                <div class="flex flex-col">
                    <h2 class="text-lg font-bold text-slate-900">${modalTitle}</h2>
                    <p class="text-xs text-slate-500 uppercase">Cập nhật thông tin danh mục hệ thống.</p>
                </div>
                <button id="close-modal-sticky-btn" class="p-2 hover:bg-slate-100 rounded-xl transition-colors text-slate-400 cursor-pointer">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form class="mt-6 space-y-4">
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wide">Tên nhóm thiết bị (NAME) <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="categoryName" value="${isEdit ? category.name : ''}" placeholder="VD: Thiết bị nhà bếp"
                        class="w-full px-4 py-3 border border-slate-300 rounded-lg bg-slate-50 text-slate-700 text-sm font-normal transition-all focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 outline-none">
                    <span class="text-xs text-slate-500">Tên nhóm sẽ được sử dụng để phân loại các thiết bị trong hệ thống.</span>
                    <div class="text-red-500 text-sm mt-1"></div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button id="close-modal-btn" type="button" class="cursor-pointer px-6 py-3 bg-white text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors font-bold text-sm">
                        Hủy
                    </button>
                    <button type="submit" id="submitBtn" class="px-6 py-3 bg-blue-900 text-white rounded-lg hover:bg-blue-800 transition-colors cursor-pointer font-bold text-sm">
                        ${submitButtonText}
                    </button>
                </div>
            </form>
        </div>
    `;
}

document.addEventListener("DOMContentLoaded", function () {
    // Add button
    const addBtn = document.querySelector('button[onclick="openCreateModal()"]');
    if (addBtn) {
        addBtn.addEventListener("click", (e) => {
            e.preventDefault();
            openCategoryModal();
        });
    }

    // Edit buttons
    const editButtons = document.querySelectorAll(".edit-category-btn");
    editButtons.forEach((button) => {
        button.addEventListener("click", async (e) => {
            e.preventDefault();
            const categoryId = parseInt(button.getAttribute("data-category-id"));
            const categoryName = button.getAttribute("data-category-name");
            const categoryData = { id: categoryId, name: categoryName };
            openCategoryModal(categoryData);
        });
    });

    // Delete buttons
    const deleteButtons = document.querySelectorAll(".delete-category-btn");
    deleteButtons.forEach((button) => {
        button.addEventListener("click", (e) => {
            e.preventDefault();
            const categoryId = parseInt(button.getAttribute("data-category-id"));
            const categoryName = button.getAttribute("data-category-name");
            
            Swal.fire({
                icon: "warning",
                title: "Xác nhận xóa",
                html: `Bạn có chắc chắn muốn xóa nhóm thiết bị <strong>${categoryName}</strong> không?<br><small style="color: #9ca3af; font-size: 0.75rem;">Hành động này không thể hoàn tác.</small>`,
                confirmButtonText: "Xóa",
                showDenyButton: true,
                denyButtonText: "Hủy",
                confirmButtonColor: "#dc2626",
                customClass: {
                    popup: 'rounded-xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteCategory(categoryId);
                }
            });
        });
    });

    function openCategoryModal(category = null) {
        const modalHtml = getFormModalHtml(category);
        openModal(modalHtml);
        
        document.getElementById("close-modal-btn").addEventListener("click", closeCategoryModal);
        document.getElementById("close-modal-sticky-btn").addEventListener("click", closeCategoryModal);
        
        document.getElementById("submitBtn").addEventListener("click", function (event) {
            const categoryId = category ? category.id : null;
            handleClickSubmit(event, categoryId);
        });
    }

    function closeCategoryModal() {
        closeModal();
    }

    function handleClickSubmit(event, categoryId = null) {
        event.preventDefault();
        const categoryNameInput = document.getElementById("categoryName");
        const categoryName = categoryNameInput.value.trim();
        let isValid = true;

        if (categoryName === "") {
            categoryNameInput.nextElementSibling.nextElementSibling.textContent = "Vui lòng nhập tên nhóm thiết bị";
            isValid = false;
        }

        if (isValid) {
            if (categoryId) {
                updateCategory(categoryId, categoryName);
            } else {
                addCategory(categoryName);
            }
        }
    }

    function addCategory(categoryName) {
        axios
            .post("/admin/equipment-categories", {
                name: categoryName,
            })
            .then((response) => {
                Swal.fire({
                    icon: "success",
                    title: "Thêm thành công!",
                    text: `Nhóm "${categoryName}" đã được thêm.`,
                    confirmButtonColor: "#1e3a8a",
                    customClass: {
                        popup: 'rounded-xl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            })
            .catch((error) => {
                console.error("Error creating category:", error);
                Swal.fire({
                    icon: "error",
                    title: "Lỗi",
                    text: error.response?.data?.message || "Có lỗi xảy ra",
                    customClass: {
                        popup: 'rounded-xl'
                    }
                });
            });
    }

    function updateCategory(categoryId, categoryName) {
        const id = parseInt(categoryId);
        axios
            .put(`/admin/equipment-categories/${id}`, {
                name: categoryName,
            })
            .then((response) => {
                Swal.fire({
                    icon: "success",
                    title: "Cập nhật thành công!",
                    text: `Nhóm "${categoryName}" đã được cập nhật.`,
                    confirmButtonColor: "#1e3a8a",
                    customClass: {
                        popup: 'rounded-xl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            })
            .catch((error) => {
                console.error("Error updating category:", error);
                Swal.fire({
                    icon: "error",
                    title: "Lỗi",
                    text: error.response?.data?.message || "Có lỗi xảy ra",
                    customClass: {
                        popup: 'rounded-xl'
                    }
                });
            });
    }

    function deleteCategory(categoryId) {
        const id = parseInt(categoryId);
        axios
            .delete(`/admin/equipment-categories/${id}`)
            .then((response) => {
                Swal.fire({
                    icon: "success",
                    title: "Xóa thành công!",
                    text: "Nhóm thiết bị đã được xóa.",
                    confirmButtonColor: "#1e3a8a",
                    customClass: {
                        popup: 'rounded-xl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            })
            .catch((error) => {
                console.error("Error deleting category:", error);
                Swal.fire({
                    icon: "error",
                    title: "Lỗi",
                    text: error.response?.data?.message || "Có lỗi xảy ra",
                    customClass: {
                        popup: 'rounded-xl'
                    }
                });
            });
    }
});
