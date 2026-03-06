import axios from "axios";
import Swal from "sweetalert2";
import { openModal, closeModal } from "../../app";

function getFormModalHtml(group = null) {
    const isEdit = group !== null;
    const modalTitle = isEdit ? "Chỉnh sửa loại dịch vụ" : "Thêm loại dịch vụ mới";
    const submitButtonText = isEdit ? "Cập nhật" : "Lưu loại dịch vụ";

    return `
        <div id='serviceGroupFormModal'>
            <div class="flex justify-between items-center pb-4 border-b border-slate-200">
                <div class="flex flex-col">
                    <h2 class="text-lg font-bold text-slate-900">${modalTitle}</h2>
                    <p class="text-xs text-slate-500 uppercase">THÔNG TIN NHÓM DỊCH VỤ</p>
                </div>
                <button id="close-modal-sticky-btn" class="p-2 hover:bg-slate-100 rounded-xl transition-colors text-slate-400 cursor-pointer">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form class="mt-6 space-y-4">
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wide">TÊN NHÓM DỊCH VỤ (SERVICE_NAME) <span class="text-red-500">*</span></label>
                    <input type="text" name="service_name" id="groupName" value="${isEdit ? group.service_name : ''}" placeholder="VD: Dịch vụ ăn uống, Spa & Wellness..."
                        class="w-full px-4 py-3 border border-slate-300 rounded-lg bg-slate-50 text-slate-700 text-sm font-normal transition-all focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 outline-none">
                    <div class="text-red-500 text-sm mt-1" id="groupNameError"></div>
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
            openGroupModal();
        });
    }

    // Edit buttons
    document.querySelectorAll(".edit-group-btn").forEach((button) => {
        button.addEventListener("click", (e) => {
            e.preventDefault();
            const groupData = {
                id: parseInt(button.getAttribute("data-group-id")),
                service_name: button.getAttribute("data-group-name"),
            };
            openGroupModal(groupData);
        });
    });

    // Delete buttons
    document.querySelectorAll(".delete-group-btn").forEach((button) => {
        button.addEventListener("click", (e) => {
            e.preventDefault();
            const groupId = parseInt(button.getAttribute("data-group-id"));
            const groupName = button.getAttribute("data-group-name");

            Swal.fire({
                icon: "warning",
                title: "Xác nhận xóa",
                html: `Bạn có chắc chắn muốn xóa loại dịch vụ <strong>${groupName}</strong> không?<br><small style="color: #9ca3af; font-size: 0.75rem;">Hành động này không thể hoàn tác.</small>`,
                confirmButtonText: "Xóa",
                showDenyButton: true,
                denyButtonText: "Hủy",
                confirmButtonColor: "#dc2626",
                customClass: { popup: "rounded-xl" },
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteGroup(groupId);
                }
            });
        });
    });

    // Expose to window so empty-state anchor href="javascript:openCreateModal()" works
    window.openCreateModal = function () { openGroupModal(); };

    function openGroupModal(group = null) {
        const modalHtml = getFormModalHtml(group);
        openModal(modalHtml);

        document.getElementById("close-modal-btn").addEventListener("click", closeModal);
        document.getElementById("close-modal-sticky-btn").addEventListener("click", closeModal);

        document.getElementById("submitBtn").addEventListener("click", function (event) {
            event.preventDefault();
            const nameInput = document.getElementById("groupName");
            const name = nameInput.value.trim();
            const errorEl = document.getElementById("groupNameError");

            if (!name) {
                errorEl.textContent = "Vui lòng nhập tên loại dịch vụ";
                return;
            }
            errorEl.textContent = "";

            if (group) {
                updateGroup(group.id, name);
            } else {
                addGroup(name);
            }
        });
    }

    function addGroup(name) {
        axios.post("/admin/service-groups", { service_name: name })
            .then(() => {
                Swal.fire({
                    icon: "success",
                    title: "Thêm thành công!",
                    text: `Loại dịch vụ "${name}" đã được thêm.`,
                    confirmButtonColor: "#1e3a8a",
                    customClass: { popup: "rounded-xl" },
                }).then((r) => r.isConfirmed && window.location.reload());
            })
            .catch((error) => {
                Swal.fire({
                    icon: "error",
                    title: "Lỗi",
                    text: error.response?.data?.message || "Có lỗi xảy ra",
                    customClass: { popup: "rounded-xl" },
                });
            });
    }

    function updateGroup(id, name) {
        axios.put(`/admin/service-groups/${id}`, { service_name: name })
            .then(() => {
                Swal.fire({
                    icon: "success",
                    title: "Cập nhật thành công!",
                    text: `Loại dịch vụ "${name}" đã được cập nhật.`,
                    confirmButtonColor: "#1e3a8a",
                    customClass: { popup: "rounded-xl" },
                }).then((r) => r.isConfirmed && window.location.reload());
            })
            .catch((error) => {
                Swal.fire({
                    icon: "error",
                    title: "Lỗi",
                    text: error.response?.data?.message || "Có lỗi xảy ra",
                    customClass: { popup: "rounded-xl" },
                });
            });
    }

    function deleteGroup(id) {
        axios.delete(`/admin/service-groups/${id}`)
            .then(() => {
                Swal.fire({
                    icon: "success",
                    title: "Xóa thành công!",
                    text: "Loại dịch vụ đã được xóa.",
                    confirmButtonColor: "#1e3a8a",
                    customClass: { popup: "rounded-xl" },
                }).then((r) => r.isConfirmed && window.location.reload());
            })
            .catch((error) => {
                Swal.fire({
                    icon: "error",
                    title: "Lỗi",
                    text: error.response?.data?.message || "Có lỗi xảy ra",
                    customClass: { popup: "rounded-xl" },
                });
            });
    }
});
