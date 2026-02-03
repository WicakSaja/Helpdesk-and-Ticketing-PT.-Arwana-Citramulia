import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
  plugins: [
    laravel({
      input: [
        "resources/css/app.css",
        "resources/js/app.js",
        "resources/css/auth-style.css",

        "resources/css/create-ticket.css",

        "resources/css/dashboard-helpdesk.css",
        "resources/css/dashboard-requester.css",
        "resources/css/dashboard-superadmin.css",
        "resources/css/dashboard-technician.css",
        "resources/css/department.css",
        "resources/css/global.css",
        "resources/css/helpdesk-all-tickets.css",
        "resources/css/helpdesk-incoming.css",
        "resources/css/profile.css",
        "resources/css/ticket-style.css",
        "resources/css/users.css",
      ],
      refresh: true,
    }),
  ],
});
