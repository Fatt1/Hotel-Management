function initOtpForm() {
	const form = document.getElementById("otp-form");
	if (!form) return;

	const hiddenOtp = document.getElementById("otp-hidden");
	const inputs = Array.from(form.querySelectorAll(".otp-digit"));

	if (!hiddenOtp || inputs.length === 0) return;

	const syncOtp = () => {
		hiddenOtp.value = inputs.map((input) => input.value).join("");
	};

	const focusNext = (index) => {
		if (index < inputs.length - 1) {
			inputs[index + 1].focus();
		}
	};

	const focusPrev = (index) => {
		if (index > 0) {
			inputs[index - 1].focus();
		}
	};

	inputs.forEach((input, index) => {
		input.addEventListener("input", (event) => {
			const value = event.target.value.replace(/\D/g, "");
			event.target.value = value.slice(0, 1);

			if (event.target.value !== "") {
				focusNext(index);
			}

			syncOtp();
		});

		input.addEventListener("keydown", (event) => {
			if (event.key === "Backspace" && input.value === "") {
				focusPrev(index);
			}
		});

		input.addEventListener("paste", (event) => {
			event.preventDefault();
			const pasted = (event.clipboardData || window.clipboardData)
				.getData("text")
				.replace(/\D/g, "")
				.slice(0, 6)
				.split("");

			pasted.forEach((digit, idx) => {
				if (inputs[idx]) {
					inputs[idx].value = digit;
				}
			});

			syncOtp();

			const focusIndex = Math.min(pasted.length, inputs.length - 1);
			inputs[focusIndex].focus();
		});
	});

	form.addEventListener("submit", () => {
		syncOtp();
	});

	syncOtp();
	const firstEmpty = inputs.find((input) => input.value === "");
	(firstEmpty || inputs[0]).focus();
}

document.addEventListener("DOMContentLoaded", initOtpForm);
