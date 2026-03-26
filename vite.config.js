import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/client/checkout.js',
                'resources/js/admin/login.js',
                'resources/js/admin/role-permission.js',
                'resources/js/admin/bookings/create-booking.js',
                'resources/js/admin/bookings/update-booking.js',
                'resources/js/admin/bookings/checkout.js',
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
        origin: 'http://localhost:5173',
        hmr: {
            host: 'localhost',
            protocol: 'ws',
            port: 5173,
            clientPort: 5173,
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
