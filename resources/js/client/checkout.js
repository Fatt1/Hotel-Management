import { verifyClientEmail } from "../api";

document.addEventListener("DOMContentLoaded", () => {
    const emailVerifyInput = document.getElementById("emailVerify");
    const verifyBtn = document.getElementById("verifyEmailBtn");
    const errorMessage = document.getElementById("verifyMessage");

    const lastNameInput = document.getElementById("lastNameInput");
    const firstNameInput = document.getElementById("firstNameInput");
    const phoneInput = document.getElementById("phoneInput");
    const countryInput = document.getElementById("countryInput");
    const checkoutBtn = document.getElementById("checkoutBtn");

    function validateCheckout() {
        if (!lastNameInput || !firstNameInput || !countryInput || !phoneInput || !checkoutBtn) return;
        
        const lastName = lastNameInput.value.trim();
        const firstName = firstNameInput.value.trim();
        const country = countryInput.value;
        const phone = phoneInput.value.trim();
        const email = emailVerifyInput ? emailVerifyInput.value.trim() : "";
        const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

        const ok =
            emailOk &&
            lastName.length > 0 &&
            firstName.length > 0 &&
            country !== "" &&
            phone.length >= 6;
        
        checkoutBtn.disabled = !ok;
    }

    if (emailVerifyInput) {
        emailVerifyInput.addEventListener("input", () => {
            validateCheckout();
        });
    }

    // Bind validation events
    if (lastNameInput) lastNameInput.addEventListener("input", validateCheckout);
    if (firstNameInput) firstNameInput.addEventListener("input", validateCheckout);
    if (countryInput) countryInput.addEventListener("change", validateCheckout);
    if (phoneInput) phoneInput.addEventListener("input", validateCheckout);

    // Initial check on load
    validateCheckout();

    // Verify Email Button Click
    if (verifyBtn && emailVerifyInput) {
        verifyBtn.addEventListener("click", async () => {
            const email = emailVerifyInput.value.trim();
            if (!email) {
                if (errorMessage) {
                    errorMessage.classList.remove("hidden");
                    errorMessage.innerHTML = `<i class="fas fa-circle-info text-[10px]"></i> Vui lòng nhập email trước khi xác thực.`;
                }
                emailVerifyInput.focus();
                return;
            }

            // Loading state
            const originalText = verifyBtn.innerHTML;
            verifyBtn.innerHTML = "Khởi tạo...";
            verifyBtn.disabled = true;
            verifyBtn.classList.add("opacity-70");

            const response = await verifyClientEmail(email);

            // Restore state
            verifyBtn.innerHTML = originalText;
            verifyBtn.disabled = false;
            verifyBtn.classList.remove("opacity-70");

            if (response && response.success) {
                // Auto-fill success, hide error message
                if (errorMessage) errorMessage.classList.add("hidden");

                if (lastNameInput) lastNameInput.value = response.data.last_name || "";
                if (firstNameInput) firstNameInput.value = response.data.first_name || "";
                if (phoneInput) phoneInput.value = response.data.phone || "";
                
                if (countryInput && response.data.country) {
                    const val = response.data.country;
                    const item = document.querySelector(`.cp-item[data-value="${val}"]`);
                    if (item) {
                        item.click();
                    } else {
                        countryInput.value = val;
                    }
                }

                validateCheckout();
            } else {
                // Show error message
                if (errorMessage) {
                    errorMessage.classList.remove("hidden");
                    errorMessage.innerHTML = `<i class="fas fa-circle-info text-[10px]"></i> ${response.message || "Không tìm thấy tài khoản."}`;
                }
            }
        });
    }
});
