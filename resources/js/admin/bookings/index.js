import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
import { Vietnamese } from "flatpickr/dist/l10n/vn.js";

function initBookingDateRangeFilter() {
	const bookingDateRangeInput = document.getElementById("booking-date-range");
	const fromDateInput = document.getElementById("from_date");
	const toDateInput = document.getElementById("to_date");
	const clearDateRangeBtn = document.getElementById("clear-date-range");

	if (!bookingDateRangeInput || !fromDateInput || !toDateInput || !clearDateRangeBtn) {
		return;
	}

	const filterForm = bookingDateRangeInput.closest("form");
	const submitFilterForm = () => {
		if (filterForm) {
			filterForm.requestSubmit();
		}
	};

	const toggleClearDateBtn = () => {
		const hasDateRange = Boolean(fromDateInput.value && toDateInput.value);
		clearDateRangeBtn.classList.toggle("hidden", !hasDateRange);
	};

	const fp = flatpickr(bookingDateRangeInput, {
		mode: "range",
		dateFormat: "Y-m-d",
		altInput: true,
		altFormat: "d/m/Y",
		allowInput: false,
		locale: Vietnamese,
		defaultDate: fromDateInput.value && toDateInput.value ? [fromDateInput.value, toDateInput.value] : null,
		onChange: function (selectedDates) {
			if (selectedDates.length === 2) {
				const firstDate = selectedDates[0];
				const secondDate = selectedDates[1];

				if (firstDate > secondDate) {
					Swal.fire({
						icon: "error",
						title: "Khoảng ngày không hợp lệ",
						text: "Ngày bắt đầu không được lớn hơn ngày kết thúc.",
					});
					fromDateInput.value = "";
					toDateInput.value = "";
					fp.clear();
					toggleClearDateBtn();
					return;
				}

				fromDateInput.value = fp.formatDate(firstDate, "Y-m-d");
				toDateInput.value = fp.formatDate(secondDate, "Y-m-d");
				toggleClearDateBtn();
				submitFilterForm();
				return;
			}

			if (selectedDates.length === 0) {
				fromDateInput.value = "";
				toDateInput.value = "";
				toggleClearDateBtn();
			}
		},
	});

	clearDateRangeBtn.addEventListener("click", () => {
		fp.clear();
		fromDateInput.value = "";
		toDateInput.value = "";
		toggleClearDateBtn();
		submitFilterForm();
	});

	if (filterForm) {
		filterForm.addEventListener("submit", function (e) {
			const hasOnlyOneDate = (fromDateInput.value && !toDateInput.value) || (!fromDateInput.value && toDateInput.value);

			if (hasOnlyOneDate) {
				e.preventDefault();
				Swal.fire({
					icon: "warning",
					title: "Thiếu ngày lọc",
					text: "Vui lòng chọn đủ từ ngày và đến ngày.",
				});
				return;
			}

			if (fromDateInput.value && toDateInput.value && fromDateInput.value > toDateInput.value) {
				e.preventDefault();
				Swal.fire({
					icon: "error",
					title: "Khoảng ngày không hợp lệ",
					text: "Ngày bắt đầu không được lớn hơn ngày kết thúc.",
				});
			}
		});
	}

	toggleClearDateBtn();
}

function initCancelBookingConfirm() {
	const forms = document.querySelectorAll(".cancel-booking-form");

	if (!forms.length) {
		return;
	}

	forms.forEach((form) => {
		form.addEventListener("submit", function (e) {
			e.preventDefault();

			Swal.fire({
				title: "Xác nhận hủy đặt phòng",
				text: "Bạn có chắc chắn muốn hủy đặt phòng này?",
				icon: "warning",
				showCancelButton: true,
				confirmButtonColor: "#d33",
				cancelButtonColor: "#3085d6",
				confirmButtonText: "Hủy đặt phòng",
				cancelButtonText: "Không",
			}).then((result) => {
				if (result.isConfirmed) {
					form.submit();
				}
			});
		});
	});
}

function initBookingIndexPage() {
	initBookingDateRangeFilter();
	initCancelBookingConfirm();
}

if (document.readyState === "loading") {
	document.addEventListener("DOMContentLoaded", initBookingIndexPage);
} else {
	initBookingIndexPage();
}
