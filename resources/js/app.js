import "./bootstrap";
import Swal from "sweetalert2";
window.Swal = Swal;
export function openModal(htmlContent) {
    const modal = document.getElementById("global-modal");
    const modalContent = modal.querySelector("#global-modal-content");
    modalContent.innerHTML = htmlContent;
    modal.classList.remove("hidden");
}
export function closeModal() {
    const modal = document.getElementById("global-modal");
    modal.classList.add("hidden");
    modal.style.display = "";
}
window.openModal = openModal;
window.closeModal = closeModal;