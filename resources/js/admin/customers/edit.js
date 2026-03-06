import Swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", function () {
    const flashError = document.getElementById("flash-error");
    if (flashError) {
        Swal.fire({
            icon: "error",
            title: "Lỗi",
            text: flashError.dataset.message,
            confirmButtonColor: "#1e3a8a"
        });
    }
});