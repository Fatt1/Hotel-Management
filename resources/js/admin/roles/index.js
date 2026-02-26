import axios from "axios";
import Swal from "sweetalert2";
import { openModal, closeModal } from "../../app";
function getFormModalHtml(role = null) {
    const isEdit = role !== null;
    const modalTitle = isEdit ? "Chỉnh sửa vai trò" : "Thêm vai trò";
    const submitButtonText = isEdit ? "Cập nhật" : "Tạo";
    return `
                   <div id='roleFormModal'>
                        <div class="flex justify-between items-center pb-4 border-b border-slate-200">
                    <div class="flex flex-col">
                        <h2 class="text-xl font-bold text-slate-900">${modalTitle}</h2>
                        <p class="text-sm text-slate-500 uppercase">Role</p>
                    </div>
    
                    <button id="close-modal-sticky-btn"
                        class="p-2 hover:bg-slate-100 rounded-xl transition-colors text-slate-400 cursor-pointer">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
    
                <form class="mt-5 space-y-5">
                    <div class="flex flex-col gap-1">
                        <label for="roleName" class="text-sm font-medium text-slate-700">Tên vai trò <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" id="roleName" value="${isEdit ? role.name : ""}" placeholder="Nhập tên vai trò mới"
                            class="w-full rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary focus:outline-none px-3 py-2">
                            <div class="text-red-500 text-sm mt-1"></div>
    
                    </div>
                    <div class="flex justify-end gap-2">
    
                        <button id="close-modal-btn" type="button"
                            class="cursor-pointer px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                            Hủy
                        </button>
    
                        <button type="submit" id="submitBtn"
                            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors cursor-pointer">
                            ${submitButtonText}
                        </button>
                    </div>
                </form>
                   </div>
    `;
}

document.addEventListener("DOMContentLoaded", function () {
    // add btn
    const addRoleBtn = document.getElementById("addRoleBtn");
    addRoleBtn.addEventListener("click", (e) => {
        openRoleModal();
    });
    // Update btn
    const editButtons = document.querySelectorAll(".edit-role-btn");
    editButtons.forEach((button) => {
        button.addEventListener("click", async (e) => {
            const roleId = button.getAttribute("data-role-id");
            const roleData = await getRoleById(roleId);
            if (roleData) {
                openRoleModal(roleData);
            }
        });
    });
    //Delete btn
    const deleteButtons = document.querySelectorAll(".btn-delete");
    deleteButtons.forEach((button) => {
        button.addEventListener("click", (e) => {
            const roleId = button.getAttribute("data-role-id");
            Swal.fire({
                icon: "warning",
                title: "Xác nhận",
                text: "Bạn có chắc muốn xóa vai trò này không?",
                confirmButtonText: "Xóa",
                showDenyButton: true,
                denyButtonText: "Hủy",
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteRole(roleId);
                }
            });
        });
    });
    function openRoleModal(role = null) {
        const modalHtml = getFormModalHtml(role);
        openModal(modalHtml);
        // Add event listener for close button
        document
            .getElementById("close-modal-btn")
            .addEventListener("click", closeRoleModal);
        document
            .getElementById("close-modal-sticky-btn")
            .addEventListener("click", closeRoleModal);

        document
            .getElementById("submitBtn")
            .addEventListener("click", function (event) {
                const roleId = role ? role.id : null;
                handleClickSubmit(event, roleId);
            });
    }

    function closeRoleModal() {
        closeModal();
    }

    function handleClickSubmit(event, roleId = null) {
        event.preventDefault();
        const roleNameInput = document.getElementById("roleName");
        const roleName = roleNameInput.value.trim();
        let isValid = true;
        if (roleName === "") {
            roleNameInput.nextElementSibling.textContent =
                "Tên vai trò không được để trống";
            isValid = false;
        }
        if (isValid) {
            if (roleId) {
                updateRole(roleId, roleName);
            } else addRole(roleName);
        }
    }
    function addRole(roleName) {
        axios
            .post("/admin/roles", {
                name: roleName,
            })
            .then((response) => {
                Swal.fire({
                    icon: "success",
                    title: "Thành công",
                    text: "Vai trò mới đã được tạo!",
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            })
            .catch((error) => {
                // Bắ lỗi hiện thị input nếu có lỗi validate
                if (error.response.status === 422) {
                    const errors = error.response.data.errors;
                    handleErrorInput(errors);
                } else {
                    console.error("Error creating role:", error);
                    Swal.fire({
                        icon: "error",
                        title: "Lỗi",
                        text: error.response.data.message,
                    });
                }
            });
    }

    function getRoleById(roleId) {
        return axios
            .get(`/admin/roles/${roleId}`)
            .then((response) => response.data)
            .catch((error) => {
                console.error("Error fetching role data:", error);
                alert("Không thể lấy dữ liệu vai trò. Vui lòng thử lại.");
                return null;
            });
    }

    function updateRole(roleId, roleName) {
        axios
            .put(`/admin/roles/${roleId}`, {
                id: roleId,
                name: roleName,
            })
            .then((response) => {
                Swal.fire({
                    icon: "success",
                    title: "Thành công",
                    text: "Cập nhật vai trò thành công!",
                }).then((result) => {
                    window.location.reload();
                });
            })
            .catch((error) => {
                // Bắ lỗi hiện thị input nếu có lỗi validate
                if (error.response.status === 422) {
                    const errors = error.response.data.errors;
                    handleErrorInput(errors);
                } else {
                    console.error("Error updating role:", error);
                    Swal.fire({
                        icon: "error",
                        title: "Lỗi",
                        text: error.response.data.message,
                    });
                }
            });
    }
    function handleErrorInput(errors) {
        for (const err in errors) {
            const input = document.querySelector(`input[name="${err}"]`);
            if (input) {
                input.nextElementSibling.textContent = errors[err][0];
            }
        }
    }

    function deleteRole(roleId) {
        axios
            .delete(`/admin/roles/${roleId}`)
            .then((response) => {
                Swal.fire({
                    icon: "success",
                    title: "Thành công",
                    text: "Vai trò đã được xóa!",
                }).then((result) => {
                    window.location.reload();
                });
            })
            .catch((error) => {
                console.error("Error deleting role:", error);
                alert("Không thể xóa vai trò này. Vui lòng thử lại.");
            });
    }
});
