import Swal from "sweetalert2";

async function handleDeleteClick(btn) {
  const url = btn.dataset.url;
  const roomName = btn.dataset.name;

  const result = await Swal.fire({
    icon: "warning",
    title: "Xác nhận xóa loại phòng",
    html: `Bạn có chắc chắn muốn xóa loại phòng <strong>${roomName}</strong> không?`,
    showCancelButton: true,
    confirmButtonText: "Xác nhận xóa",
    cancelButtonText: "Hủy",
    confirmButtonColor: "#dc2626",
    cancelButtonColor: "#64748b",
    reverseButtons: true,
    focusCancel: true,
  });

  if (!result.isConfirmed) return;

  const form = btn.closest('form');
  if (form) {
    form.submit();
  }
}

document.addEventListener('DOMContentLoaded', () => {
  document.addEventListener('click', (event) => {
    const btn = event.target.closest('.btn-delete');
    if (btn) {
      event.preventDefault();
      handleDeleteClick(btn);
    }
  });
});
