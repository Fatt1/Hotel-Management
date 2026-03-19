import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const API_LOADING_OVERLAY_ID = 'api-loading-overlay';
const API_LOADING_ACTIVE_CLASS = 'api-loading-active';

let pendingApiRequests = 0;

function ensureApiLoadingOverlay() {
	if (typeof document === 'undefined') {
		return null;
	}

	let overlay = document.getElementById(API_LOADING_OVERLAY_ID);
	if (overlay) {
		return overlay;
	}

	overlay = document.createElement('div');
	overlay.id = API_LOADING_OVERLAY_ID;
	overlay.className = 'api-loading-overlay hidden';
	overlay.setAttribute('aria-live', 'polite');
	overlay.innerHTML = `
		<div class="api-loading-spinner" role="status" aria-label="Đang tải"></div>
	`;

	document.body.appendChild(overlay);
	return overlay;
}

function toggleApiLoading(show) {
	const overlay = ensureApiLoadingOverlay();
	if (!overlay) {
		return;
	}

	overlay.classList.toggle('hidden', !show);
	document.body.classList.toggle(API_LOADING_ACTIVE_CLASS, show);
}

function increasePendingApiRequests() {
	pendingApiRequests += 1;
	if (pendingApiRequests === 1) {
		toggleApiLoading(true);
	}
}

function decreasePendingApiRequests() {
	pendingApiRequests = Math.max(0, pendingApiRequests - 1);
	if (pendingApiRequests === 0) {
		toggleApiLoading(false);
	}
}

if (!window.__apiLoadingInterceptorRegistered) {
	window.__apiLoadingInterceptorRegistered = true;

	window.axios.interceptors.request.use(
		(config) => {
			increasePendingApiRequests();
			return config;
		},
		(error) => {
			decreasePendingApiRequests();
			return Promise.reject(error);
		},
	);

	window.axios.interceptors.response.use(
		(response) => {
			decreasePendingApiRequests();
			return response;
		},
		(error) => {
			decreasePendingApiRequests();
			return Promise.reject(error);
		},
	);
}

if (typeof document !== 'undefined') {
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', ensureApiLoadingOverlay, { once: true });
	} else {
		ensureApiLoadingOverlay();
	}
}
