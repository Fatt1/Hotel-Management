import axios from "axios";
import Swal from "sweetalert2";
import { openModal, closeModal } from "../../app";

function getServiceGroups() {
    return window.SERVICE_GROUPS || [];
}

function buildGroupOptions(selectedGroupId = null) {
    const groups = getServiceGroups();
    let options = `<option value="">Chọn nhóm dịch vụ</option>`;
    groups.forEach((group) => {
        const selected = selectedGroupId && group.id == selectedGroupId ? "selected" : "";
        options += `<option value="${group.id}" ${selected}>${group.service_name}</option>`;
    });
    return options;
}

function getFormModalHtml(service = null) {
    const isEdit = service !== null;
    const modalTitle = isEdit ? "Chỉnh sửa dịch vụ" : "Thêm dịch vụ mới";
    const submitButtonText = isEdit ? "Cập nhật dịch vụ" : "Lưu dịch vụ";
    const selectedGroupId = isEdit ? service.group_id : null;

    return `
        <div id='serviceFormModal'>
            <div class="flex justify-between items-center pb-4 border-b border-slate-200">
                <div class="flex flex-col">
                    <h2 class="text-lg font-bold text-slate-900">${modalTitle}</h2>
                    <p class="text-xs text-slate-500 uppercase">THÔNG TIN DỊCH VỤ</p>
                </div>
                <button id="close-modal-sticky-btn" class="p-2 hover:bg-slate-100 rounded-xl transition-colors text-slate-400 cursor-pointer">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form class="mt-6 space-y-4">
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wide">TÊN DỊCH VỤ <span class="text-red-500">*</span></label>
                    <input type="text" id="serviceName" value="${isEdit ? service.name : ''}" placeholder="VD: Buffet Sáng, Spa Thụy Điển..."
                        class="w-full px-4 py-3 border border-slate-300 rounded-lg bg-slate-50 text-slate-700 text-sm transition-all focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 outline-none">
                    <div class="text-red-500 text-sm" id="serviceNameError"></div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wide">NHÓM DỊCH VỤ <span class="text-red-500">*</span></label>
                    <select id="serviceGroupId" class="w-full px-4 py-3 border border-slate-300 rounded-lg bg-slate-50 text-slate-700 text-sm transition-all focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 outline-none">
                        ${buildGroupOptions(selectedGroupId)}
                    </select>
                    <div class="text-red-500 text-sm" id="serviceGroupError"></div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wide">ĐƠN GIÁ <span class="font-normal text-slate-400 normal-case">(nghìn VND)</span> <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm font-bold">×1K</span>
                            <input type="number" id="serviceUnitPrice" value="${isEdit ? service.unit_price / 1000 : ''}" min="1" step="1" placeholder="VD: 450 (= 450.000đ)"
                                class="w-full pl-12 pr-4 py-3 border border-slate-300 rounded-lg bg-slate-50 text-slate-700 text-sm transition-all focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 outline-none">
                        </div>
                        <div class="text-red-500 text-sm" id="serviceUnitPriceError"></div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wide">ĐƠN VỊ TÍNH <span class="text-red-500">*</span></label>
                        <input type="text" id="serviceUnit" value="${isEdit ? service.unit : ''}" placeholder="Lượt, Kg, Chuyến..."
                            class="w-full px-4 py-3 border border-slate-300 rounded-lg bg-slate-50 text-slate-700 text-sm transition-all focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 outline-none">
                        <div class="text-red-500 text-sm" id="serviceUnitError"></div>
                    </div>
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
    const addBtn = document.querySelector('button[onclick="openCreateServiceModal()"]');
    if (addBtn) {
        addBtn.addEventListener("click", (e) => {
            e.preventDefault();
            openServiceModal();
        });
    }

    // Edit buttons
    document.querySelectorAll(".edit-service-btn").forEach((button) => {
        button.addEventListener("click", (e) => {
            e.preventDefault();
            const serviceData = {
                id: parseInt(button.getAttribute("data-service-id")),
                name: button.getAttribute("data-service-name"),
                group_id: parseInt(button.getAttribute("data-service-group-id")),
                unit_price: parseFloat(button.getAttribute("data-service-unit-price")),
                unit: button.getAttribute("data-service-unit"),
            };
            openServiceModal(serviceData);
        });
    });

    // Delete buttons
    document.querySelectorAll(".delete-service-btn").forEach((button) => {
        button.addEventListener("click", (e) => {
            e.preventDefault();
            const serviceId = parseInt(button.getAttribute("data-service-id"));
            const serviceName = button.getAttribute("data-service-name");

            Swal.fire({
                icon: "warning",
                title: "Xác nhận xóa",
                html: `Bạn có chắc chắn muốn xóa dịch vụ <strong>${serviceName}</strong> không?<br><small style="color: #9ca3af; font-size: 0.75rem;">Hành động này không thể hoàn tác.</small>`,
                confirmButtonText: "Xóa",
                showDenyButton: true,
                denyButtonText: "Hủy",
                confirmButtonColor: "#dc2626",
                customClass: { popup: "rounded-xl" },
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteService(serviceId);
                }
            });
        });
    });

    // Expose to window so empty-state anchor href="javascript:openCreateServiceModal()" works
    window.openCreateServiceModal = function () { openServiceModal(); };

    function openServiceModal(service = null) {
        const modalHtml = getFormModalHtml(service);
        openModal(modalHtml);

        document.getElementById("close-modal-btn").addEventListener("click", closeModal);
        document.getElementById("close-modal-sticky-btn").addEventListener("click", closeModal);

        document.getElementById("submitBtn").addEventListener("click", function (event) {
            event.preventDefault();
            if (!validateForm()) return;

            const payload = {
                name: document.getElementById("serviceName").value.trim(),
                group_id: parseInt(document.getElementById("serviceGroupId").value),
                // Nhân 1000: người dùng gõ 450 → lưu 450.000
                unit_price: parseFloat(document.getElementById("serviceUnitPrice").value) * 1000,
                unit: document.getElementById("serviceUnit").value.trim(),
            };

            if (service) {
                updateService(service.id, payload);
            } else {
                addService(payload);
            }
        });
    }

    function validateForm() {
        let isValid = true;

        const name = document.getElementById("serviceName").value.trim();
        const groupId = document.getElementById("serviceGroupId").value;
        const unitPrice = document.getElementById("serviceUnitPrice").value;
        const unit = document.getElementById("serviceUnit").value.trim();

        document.getElementById("serviceNameError").textContent = "";
        document.getElementById("serviceGroupError").textContent = "";
        document.getElementById("serviceUnitPriceError").textContent = "";
        document.getElementById("serviceUnitError").textContent = "";

        if (!name) {
            document.getElementById("serviceNameError").textContent = "Vui lòng nhập tên dịch vụ";
            isValid = false;
        }
        if (!groupId) {
            document.getElementById("serviceGroupError").textContent = "Vui lòng chọn nhóm dịch vụ";
            isValid = false;
        }
        if (unitPrice === "" || isNaN(parseFloat(unitPrice)) || parseFloat(unitPrice) < 1) {
            document.getElementById("serviceUnitPriceError").textContent = "Vui lòng nhập đơn giá (tối thiểu 1 nghìn đồng)";
            isValid = false;
        }
        if (!unit) {
            document.getElementById("serviceUnitError").textContent = "Vui lòng nhập đơn vị tính";
            isValid = false;
        }

        return isValid;
    }

    function addService(payload) {
        axios.post("/admin/services", payload)
            .then(() => {
                Swal.fire({
                    icon: "success",
                    title: "Thêm thành công!",
                    text: `Dịch vụ "${payload.name}" đã được thêm.`,
                    confirmButtonColor: "#1e3a8a",
                    customClass: { popup: "rounded-xl" },
                }).then((r) => {
                    if (r.isConfirmed) {
                        // Redirect về trang gốc (bỏ filter) để luôn thấy dịch vụ mới thêm
                        window.location.href = '/admin/services';
                    }
                });
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

    function updateService(id, payload) {
        axios.put(`/admin/services/${id}`, payload)
            .then(() => {
                Swal.fire({
                    icon: "success",
                    title: "Cập nhật thành công!",
                    text: `Dịch vụ "${payload.name}" đã được cập nhật.`,
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

    function deleteService(id) {
        axios.delete(`/admin/services/${id}`)
            .then(() => {
                Swal.fire({
                    icon: "success",
                    title: "Xóa thành công!",
                    text: "Dịch vụ đã được xóa.",
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
