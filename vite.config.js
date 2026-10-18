import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite"; // Import

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/flowbite.min.js",
                "resources/js/preline.js",
                "resources/js/select2.min.js",
                "resources/js/chart.js",
                "resources/js/apexcharts.min.js",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
    },
});
