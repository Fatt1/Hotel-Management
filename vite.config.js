import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

const appUrl = process.env.APP_URL ?? 'http://localhost:8000';
const appHostname = (() => {
    try {
        return new URL(appUrl).hostname;
    } catch {
        return 'localhost';
    }
})();

const hmrHost = process.env.VITE_HMR_HOST ?? appHostname;
const hmrProtocol = process.env.VITE_HMR_PROTOCOL ?? 'ws';
const hmrPort = Number(process.env.VITE_HMR_PORT ?? 5173);

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/api.js',
                'resources/js/app.js',
                'resources/js/bootstrap.js',
                'resources/js/util.js',
                'resources/js/client/checkout.js',
                'resources/js/client/login.js',
                'resources/js/admin/login.js',
                'resources/js/admin/role-permission.js',
                'resources/js/admin/bookings/index.js',
                'resources/js/admin/bookings/create-booking.js',
                'resources/js/admin/bookings/update-booking.js',
                'resources/js/admin/bookings/checkout.js',
                'resources/js/admin/bookings/modules/customer.js',
                'resources/js/admin/bookings/modules/date-picker.js',
                'resources/js/admin/bookings/modules/payment-input.js',
                'resources/js/admin/bookings/modules/payment.js',
                'resources/js/admin/bookings/modules/room-list.js',
                'resources/js/admin/bookings/modules/room-modal.js',
                'resources/js/admin/bookings/modules/service-modal.js',
                'resources/js/admin/bookings/modules/state.js',
                'resources/js/admin/customers/index.js',
                'resources/js/admin/customers/edit.js',
                'resources/js/admin/equipment-categories/index.js',
                'resources/js/admin/equipments/index.js',
                'resources/js/admin/general-config/surcharge.js',
                'resources/js/admin/layout-room/index.js',
                'resources/js/admin/maintenance-tickets/index.js',
                'resources/js/admin/room-types/create.js',
                'resources/js/admin/room-types/edit.js',
                'resources/js/admin/room-types/index.js',
                'resources/js/admin/roles/index.js',
                'resources/js/admin/service-groups/index.js',
                'resources/js/admin/services/index.js',
                'resources/js/admin/utilities/index.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        // Bind inside container, but expose browser-facing URL via localhost.
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        cors: true,
        hmr: {
            host: hmrHost,
            protocol: hmrProtocol,
            port: hmrPort,
            clientPort: hmrPort,
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
