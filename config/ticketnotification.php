<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Ticket Notification Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk fitur notifikasi email saat ticket baru masuk.
    | Notifikasi dikirim ke seluruh helpdesk aktif via Queue.
    |
    */

    // Aktifkan/nonaktifkan notifikasi ticket baru
    // Set false untuk disable saat development
    'enabled' => (bool) env('TICKET_NOTIFICATION_ENABLED', true),

    // URL frontend untuk link ke detail ticket
    'frontend_url' => env('TICKET_NOTIFICATION_FRONTEND_URL', 'http://localhost:8000'),

    // Path detail ticket di frontend (ticket_number akan ditambahkan)
    'ticket_detail_path' => env('TICKET_NOTIFICATION_TICKET_PATH', '/tickets'),

];
