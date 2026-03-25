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
    const phoneCodeSelect = document.querySelector('select[name="phone_code"]');

    const supportedPhoneCodes = ["+84", "+1", "+81", "+82", "+65", "+61", "+44"];

    function parsePhone(phoneRaw) {
        const raw = String(phoneRaw || "").trim().replace(/\s+/g, "");
        if (!raw) {
            return { code: "+84", number: "" };
        }

        for (const code of supportedPhoneCodes) {
            if (raw.startsWith(code)) {
                return {
                    code,
                    number: raw.slice(code.length),
                };
            }
        }

        return { code: "+84", number: raw };
    }

    function setPhoneFields(phoneRaw) {
        const { code, number } = parsePhone(phoneRaw);
        if (phoneCodeSelect) {
            phoneCodeSelect.value = code;
        }
        if (phoneInput) {
            phoneInput.value = number;
        }
    }

    function validateCheckout() {
        if (
            !lastNameInput ||
            !firstNameInput ||
            !countryInput ||
            !phoneInput ||
            !checkoutBtn
        )
            return;

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
    if (lastNameInput)
        lastNameInput.addEventListener("input", validateCheckout);
    if (firstNameInput)
        firstNameInput.addEventListener("input", validateCheckout);
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
                const phoneValue =
                    response?.data?.phone || response?.data?.phone_number || "";

                // Auto-fill success, hide error message
                if (errorMessage) {
                    errorMessage.classList.remove("hidden");
                    errorMessage.classList.remove("text-red-500");
                    errorMessage.classList.add("text-green-600");
                    errorMessage.innerHTML = `<i class="fas fa-check-circle text-[10px]"></i> Đã tìm thấy tài khoản. Thông tin đã được điền.`;
                }

                if (lastNameInput) {
                    lastNameInput.value = response.data.last_name || "";
                    if (response.data.last_name) {
                        lastNameInput.readOnly = true;
                        lastNameInput.classList.add(
                            "bg-gray-100",
                            "cursor-not-allowed",
                            "border-gray-300",
                            "text-gray-500",
                        );
                    } else {
                        lastNameInput.readOnly = false;
                        lastNameInput.classList.remove(
                            "bg-gray-100",
                            "cursor-not-allowed",
                            "border-gray-300",
                            "text-gray-500",
                        );
                    }
                }

                if (firstNameInput) {
                    firstNameInput.value = response.data.first_name || "";
                    if (response.data.first_name) {
                        firstNameInput.readOnly = true;
                        firstNameInput.classList.add(
                            "bg-gray-100",
                            "cursor-not-allowed",
                            "border-gray-300",
                            "text-gray-500",
                        );
                    } else {
                        firstNameInput.readOnly = false;
                        firstNameInput.classList.remove(
                            "bg-gray-100",
                            "cursor-not-allowed",
                            "border-gray-300",
                            "text-gray-500",
                        );
                    }
                }

                if (phoneInput) {
                    setPhoneFields(phoneValue);
                    if (phoneValue) {
                        phoneInput.readOnly = true;
                        phoneInput.classList.add(
                            "bg-gray-100",
                            "cursor-not-allowed",
                            "border-gray-300",
                            "text-gray-500",
                        );
                        // also disable phone_code
                        if (phoneCodeSelect) {
                            phoneCodeSelect.parentElement.classList.add(
                                "pointer-events-none",
                                "opacity-80",
                            );
                        }
                    } else {
                        phoneInput.readOnly = false;
                        phoneInput.classList.remove(
                            "bg-gray-100",
                            "cursor-not-allowed",
                            "border-gray-300",
                            "text-gray-500",
                        );
                        if (phoneCodeSelect) {
                            phoneCodeSelect.parentElement.classList.remove(
                                "pointer-events-none",
                                "opacity-80",
                            );
                        }
                    }
                }

                if (countryInput && response.data.country) {
                    const val = response.data.country;
                    const item = document.querySelector(
                        `.cp-item[data-value="${val}"]`,
                    );
                    if (item) {
                        item.click();
                    } else {
                        countryInput.value = val;
                    }
                    countryInput
                        .closest(".relative")
                        .classList.add("pointer-events-none", "opacity-80");

                    // Add hidden input so that disabled select still submits correctly, actually we only used pointer-events-none so it will still submit.
                } else if (countryInput) {
                    countryInput
                        .closest(".relative")
                        .classList.remove("pointer-events-none", "opacity-80");
                }

                validateCheckout();
            } else {
                // Not found -> Unlock all fields so user can type
                if (errorMessage) {
                    errorMessage.classList.remove("hidden");
                    errorMessage.classList.remove("text-green-600");
                    errorMessage.classList.add("text-red-500");
                    errorMessage.innerHTML = `<i class="fas fa-circle-info text-[10px]"></i> ${response.message || "Không tìm thấy tài khoản."}`;
                }

                if (lastNameInput) {
                    lastNameInput.value = "";
                    lastNameInput.readOnly = false;
                    lastNameInput.classList.remove(
                        "bg-gray-100",
                        "cursor-not-allowed",
                        "border-gray-300",
                        "text-gray-500",
                    );
                }
                if (firstNameInput) {
                    firstNameInput.value = "";
                    firstNameInput.readOnly = false;
                    firstNameInput.classList.remove(
                        "bg-gray-100",
                        "cursor-not-allowed",
                        "border-gray-300",
                        "text-gray-500",
                    );
                }
                if (phoneInput) {
                    phoneInput.value = "";
                    phoneInput.readOnly = false;
                    phoneInput.classList.remove(
                        "bg-gray-100",
                        "cursor-not-allowed",
                        "border-gray-300",
                        "text-gray-500",
                    );
                    if (phoneCodeSelect) {
                        phoneCodeSelect.value = "+84";
                        phoneCodeSelect.parentElement.classList.remove(
                            "pointer-events-none",
                            "opacity-80",
                        );
                    }
                }
                if (countryInput) {
                    countryInput
                        .closest(".relative")
                        .classList.remove("pointer-events-none", "opacity-80");
                }

                validateCheckout();
            }
        });
    }
});
