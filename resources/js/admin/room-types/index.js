import Swal from "sweetalert2";

async function openDeleteModal(event, url, roomName) {
  event.preventDefault();

  const result = await Swal.fire({
    icon: "warning",
    title: "Xac nhan xoa loai phong",
    html: `Ban co chac chan muon xoa loai phong <strong>${roomName}</strong> khong?`,
    showCancelButton: true,
    confirmButtonText: "Xac nhan xoa",
    cancelButtonText: "Huy",
    confirmButtonColor: "#dc2626",
    cancelButtonColor: "#64748b",
    reverseButtons: true,
    focusCancel: true,
  });

  if (!result.isConfirmed) return;

  const hiddenForm = document.getElementById('deleteForm');
  if (!hiddenForm) return;
  hiddenForm.action = url;
  hiddenForm.submit();
}

document.addEventListener('DOMContentLoaded', () => {
  Object.assign(window, {
    openDeleteModal,
  });
});
