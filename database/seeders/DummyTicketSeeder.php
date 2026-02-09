<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\TicketAssignment;
use App\Models\TicketSolution;
use App\Models\TicketLog;
use App\Models\TechnicianTicketHistory;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;

class DummyTicketSeeder extends Seeder
{
    private $requesters;
    private $technicians;
    private $helpdeskUsers;
    private $categories;
    private $statuses;

    // Akun utama (akan di-assign lebih sering)
    private $mainRequester;
    private $mainTechnician;
    private $mainHelpdesk;

    /**
     * Generate dummy tickets spread across 2025-2026
     * 5 tickets per week, weighted toward main accounts
     */
    public function run(): void
    {
        $this->command->info('Generating dummy tickets...');

        $this->loadReferences();

        $ticketCounter = 1;

        // Status weights: open=10%, assigned=15%, in_progress=20%, resolved=30%, closed=25%
        $statusPool = array_merge(
            array_fill(0, 10, 'OPEN'),
            array_fill(0, 15, 'ASSIGNED'),
            array_fill(0, 20, 'IN PROGRESS'),
            array_fill(0, 30, 'RESOLVED'),
            array_fill(0, 25, 'CLOSED'),
        );

        // Generate tickets for each week from Jan 2025 to Feb 2026
        $startDate = Carbon::create(2025, 1, 6); // First Monday of 2025
        $endDate = Carbon::create(2026, 2, 8);   // Up to Feb 2026

        $currentWeek = $startDate->copy();

        while ($currentWeek->lte($endDate)) {
            $year = $currentWeek->year;

            for ($i = 0; $i < 5; $i++) {
                // Random day within this week (Mon-Fri)
                $dayOffset = rand(0, 4);
                $createdAt = $currentWeek->copy()->addDays($dayOffset)
                    ->setTime(rand(7, 17), rand(0, 59), rand(0, 59));

                // Pick random status
                $statusName = $statusPool[array_rand($statusPool)];

                // Pick requester — 40% chance main requester
                $requester = (rand(1, 100) <= 40)
                    ? $this->mainRequester
                    : $this->requesters->random();

                $this->createTicket($statusName, $ticketCounter, $createdAt, $year, $requester);
                $ticketCounter++;
            }

            $currentWeek->addWeek();
        }

        $totalTickets = $ticketCounter - 1;
        $this->command->info("{$totalTickets} dummy tickets created!");
        $this->command->info('Date range: Jan 2025 - Feb 2026');
    }

    private function loadReferences()
    {
        $this->requesters = User::role('requester')->get();
        $this->technicians = User::role('technician')->get();
        $this->helpdeskUsers = User::role('helpdesk')->get();
        $this->categories = Category::all();

        $this->statuses = [
            'OPEN' => TicketStatus::where('name', 'open')->first(),
            'ASSIGNED' => TicketStatus::where('name', 'assigned')->first(),
            'IN PROGRESS' => TicketStatus::where('name', 'in progress')->first(),
            'RESOLVED' => TicketStatus::where('name', 'resolved')->first(),
            'CLOSED' => TicketStatus::where('name', 'closed')->first(),
        ];

        // Main accounts
        $this->mainRequester = User::where('email', 'requester@arwanacitra.com')->first()
            ?? $this->requesters->first();
        $this->mainTechnician = User::where('email', 'technician@arwanacitra.com')->first()
            ?? $this->technicians->first();
        $this->mainHelpdesk = User::where('email', 'helpdesk@arwanacitra.com')->first()
            ?? $this->helpdeskUsers->first();

        if ($this->requesters->isEmpty() || $this->technicians->isEmpty()) {
            $this->command->error('No users found! Run DummyUserSeeder first.');
            exit(1);
        }
    }

    private function createTicket($statusName, $counter, $createdAt, $year, $requester)
    {
        $ticketNumber = "TKT-{$year}-" . str_pad($counter, 6, '0', STR_PAD_LEFT);
        $category = $this->categories->random();
        $ticketData = $this->generateTicketContent($category->name);

        $ticket = Ticket::create([
            'ticket_number' => $ticketNumber,
            'requester_id' => $requester->id,
            'status_id' => $this->statuses[$statusName]->id,
            'category_id' => $category->id,
            'subject' => $ticketData['subject'],
            'description' => $ticketData['description'],
            'channel' => $this->getRandomChannel(),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
            'closed_at' => $statusName === 'CLOSED' ? $createdAt->copy()->addDays(rand(1, 5)) : null,
        ]);

        // Initial log
        TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $requester->id,
            'action' => 'open',
            'description' => 'Ticket dibuat oleh ' . $requester->name,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        // Status workflow
        switch ($statusName) {
            case 'OPEN':
                break;
            case 'ASSIGNED':
                $this->assignTicket($ticket, $createdAt);
                break;
            case 'IN PROGRESS':
                $assignedAt = $this->assignTicket($ticket, $createdAt);
                $this->confirmTicket($ticket, $assignedAt);
                break;
            case 'RESOLVED':
                $assignedAt = $this->assignTicket($ticket, $createdAt);
                $inProgressAt = $this->confirmTicket($ticket, $assignedAt);
                $this->resolveTicket($ticket, $inProgressAt);
                break;
            case 'CLOSED':
                $assignedAt = $this->assignTicket($ticket, $createdAt);
                $inProgressAt = $this->confirmTicket($ticket, $assignedAt);
                $resolvedAt = $this->resolveTicket($ticket, $inProgressAt);
                $this->closeTicket($ticket, $resolvedAt);
                break;
        }
    }

    private function assignTicket($ticket, $previousTime)
    {
        $assignedAt = $previousTime->copy()->addMinutes(rand(10, 120));

        // 50% chance main technician
        $technician = (rand(1, 100) <= 50)
            ? $this->mainTechnician
            : $this->technicians->random();

        // 60% chance main helpdesk
        $helpdesk = (rand(1, 100) <= 60)
            ? $this->mainHelpdesk
            : $this->helpdeskUsers->random();

        TicketAssignment::create([
            'ticket_id' => $ticket->id,
            'assigned_to' => $technician->id,
            'assigned_by' => $helpdesk->id,
            'assigned_at' => $assignedAt,
            'notes' => 'Ditugaskan oleh ' . $helpdesk->name,
        ]);

        TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $helpdesk->id,
            'action' => 'assigned',
            'description' => "Ticket ditugaskan ke {$technician->name} oleh {$helpdesk->name}",
            'created_at' => $assignedAt,
            'updated_at' => $assignedAt,
        ]);

        $ticket->update([
            'status_id' => $this->statuses['ASSIGNED']->id,
            'updated_at' => $assignedAt,
        ]);

        return $assignedAt;
    }

    private function confirmTicket($ticket, $previousTime)
    {
        $confirmedAt = $previousTime->copy()->addMinutes(rand(15, 180));
        $technician = $ticket->assignment->technician;

        TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $technician->id,
            'action' => 'in_progress',
            'description' => "Ticket dikonfirmasi oleh {$technician->name}",
            'created_at' => $confirmedAt,
            'updated_at' => $confirmedAt,
        ]);

        $ticket->update([
            'status_id' => $this->statuses['IN PROGRESS']->id,
            'updated_at' => $confirmedAt,
        ]);

        return $confirmedAt;
    }

    private function resolveTicket($ticket, $previousTime)
    {
        $resolvedAt = $previousTime->copy()->addHours(rand(2, 48));
        $technician = $ticket->assignment->technician;
        $solutionText = $this->generateSolutionText($ticket->category->name);

        TicketSolution::create([
            'ticket_id' => $ticket->id,
            'solved_by' => $technician->id,
            'solution_text' => $solutionText,
            'solved_at' => $resolvedAt,
        ]);

        TechnicianTicketHistory::create([
            'ticket_id' => $ticket->id,
            'technician_id' => $technician->id,
            'resolved_at' => $resolvedAt,
            'solution_text' => $solutionText,
        ]);

        TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $technician->id,
            'action' => 'resolved',
            'description' => "Ticket diselesaikan oleh {$technician->name}",
            'created_at' => $resolvedAt,
            'updated_at' => $resolvedAt,
        ]);

        $ticket->update([
            'status_id' => $this->statuses['RESOLVED']->id,
            'updated_at' => $resolvedAt,
        ]);

        return $resolvedAt;
    }

    private function closeTicket($ticket, $previousTime)
    {
        $closedAt = $previousTime->copy()->addHours(rand(1, 24));
        $closer = rand(0, 1) === 0 ? $this->helpdeskUsers->random() : $ticket->requester;

        TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $closer->id,
            'action' => 'closed',
            'description' => "Ticket ditutup oleh {$closer->name}",
            'created_at' => $closedAt,
            'updated_at' => $closedAt,
        ]);

        $ticket->update([
            'status_id' => $this->statuses['CLOSED']->id,
            'closed_at' => $closedAt,
            'updated_at' => $closedAt,
        ]);

        return $closedAt;
    }

    private function generateTicketContent($categoryName)
    {
        $templates = [
            'Hardware' => [
                ['subject' => 'Komputer tidak bisa menyala', 'description' => 'Komputer di ruang produksi tidak mau menyala sejak pagi. Sudah dicoba beberapa kali tetap tidak ada respon. Mohon segera dicek karena menghambat pekerjaan.'],
                ['subject' => 'Printer error terus menerus', 'description' => 'Printer Canon di lantai 2 selalu error saat mencetak. Muncul pesan "Paper Jam" padahal tidak ada kertas yang nyangkut. Sudah dicoba restart tetap sama.'],
                ['subject' => 'Keyboard rusak beberapa tombol', 'description' => 'Keyboard user tidak berfungsi untuk tombol huruf A, S, D. Mengganggu pekerjaan input data. Mohon diganti atau diperbaiki.'],
                ['subject' => 'Monitor mati total', 'description' => 'Monitor komputer di meja saya tiba-tiba mati dan tidak mau hidup lagi. Lampu indikator tidak menyala sama sekali.'],
                ['subject' => 'Mouse wireless tidak connect', 'description' => 'Mouse wireless tidak bisa connect ke komputer. Sudah ganti baterai tetap tidak terdeteksi. USB receiver sudah dipasang dengan benar.'],
                ['subject' => 'UPS berbunyi alarm terus', 'description' => 'UPS di ruangan server berbunyi alarm terus menerus. Khawatir daya listrik tidak stabil dan bisa merusak perangkat.'],
                ['subject' => 'Scanner tidak terdeteksi', 'description' => 'Scanner Epson tidak terdeteksi oleh komputer. Sudah coba ganti kabel USB tetapi tetap tidak muncul di device manager.'],
            ],
            'Software' => [
                ['subject' => 'Aplikasi ERP tidak bisa login', 'description' => 'Tidak bisa login ke aplikasi ERP sejak update kemarin. Muncul pesan error "Authentication Failed". Username dan password sudah benar.'],
                ['subject' => 'Microsoft Office tidak aktif', 'description' => 'Microsoft Office di komputer saya muncul notifikasi "Product Activation Failed". Mohon dibantu aktivasi ulang agar bisa digunakan.'],
                ['subject' => 'Email tidak bisa kirim attachment', 'description' => 'Saat mengirim email dengan attachment file PDF lebih dari 5MB selalu gagal. Muncul pesan error "Failed to send". Urgent karena harus kirim laporan.'],
                ['subject' => 'Antivirus expired perlu update', 'description' => 'Antivirus di komputer sudah expired dan muncul notifikasi terus menerus. Mohon diupdate agar tetap aman dari virus.'],
                ['subject' => 'Aplikasi inventory crash', 'description' => 'Aplikasi inventory tiba-tiba crash saat input data. Setiap kali buka aplikasi langsung not responding. Mohon segera diperbaiki.'],
                ['subject' => 'Windows update gagal terus', 'description' => 'Windows update selalu gagal dengan error code 0x800f0922. Sudah dicoba beberapa kali tetap gagal. Komputer jadi lambat.'],
                ['subject' => 'Browser tidak bisa buka website internal', 'description' => 'Browser Chrome tidak bisa membuka website internal perusahaan. Muncul error ERR_CONNECTION_REFUSED. Website diakses dari komputer lain bisa.'],
            ],
            'Network' => [
                ['subject' => 'Internet sangat lambat', 'description' => 'Koneksi internet di ruangan kami sangat lambat sejak tadi pagi. Loading website lama sekali, bahkan untuk buka email saja lemot. Mohon dicek.'],
                ['subject' => 'WiFi tidak bisa connect', 'description' => 'WiFi kantor tidak bisa diakses dari laptop saya. Muncul pesan "Can\'t connect to this network". WiFi terlihat tapi tidak bisa connect.'],
                ['subject' => 'Tidak bisa akses shared folder', 'description' => 'Tidak bisa membuka shared folder di server. Muncul pesan "Network path not found". Kemarin masih bisa akses normal.'],
                ['subject' => 'VPN tidak konek', 'description' => 'VPN untuk remote access tidak bisa connect. Stuck di "Connecting..." terus. Perlu akses server dari rumah untuk WFH.'],
                ['subject' => 'Jaringan LAN putus-putus', 'description' => 'Koneksi internet via kabel LAN sering putus-putus. Harus cabut pasang berkali-kali baru bisa connect lagi. Sangat mengganggu pekerjaan.'],
                ['subject' => 'Tidak bisa print via jaringan', 'description' => 'Printer network di ruang meeting tidak bisa diakses. Error "The printer is not available". Komputer lain juga mengalami hal yang sama.'],
            ],
            'Account & Access' => [
                ['subject' => 'Lupa password sistem', 'description' => 'Saya lupa password untuk login ke sistem payroll. Sudah coba beberapa kali tetap salah. Mohon dibantu reset password.'],
                ['subject' => 'Request akun baru karyawan', 'description' => 'Karyawan baru memerlukan akun email dan akses ke sistem ERP. Mohon dibuatkan akun secepatnya.'],
                ['subject' => 'Akun terkunci setelah salah password', 'description' => 'Akun saya terkunci karena salah input password 3x. Tidak bisa login ke semua sistem. Mohon segera dibuka kembali aksesnya.'],
                ['subject' => 'Butuh akses tambahan ke folder', 'description' => 'Saya perlu akses ke folder Finance di shared drive untuk keperluan audit. Saat ini tidak punya permission untuk buka folder tersebut.'],
                ['subject' => 'Email tidak bisa menerima', 'description' => 'Email saya tidak bisa menerima email baru sejak kemarin sore. Saat ada yang kirim email, mereka dapat bounce back message. Mohon dicek.'],
                ['subject' => 'Reset password email', 'description' => 'Mohon dibantu reset password email perusahaan karena sudah lama tidak login dan lupa passwordnya.'],
            ],
            'Other' => [
                ['subject' => 'Minta install software Zoom', 'description' => 'Mohon dibantu install aplikasi Zoom di laptop untuk keperluan meeting online dengan client. Urgent karena meeting besok pagi.'],
                ['subject' => 'Request training sistem baru', 'description' => 'Mohon diadakan training untuk sistem ERP yang baru karena masih banyak yang belum paham cara menggunakan fitur-fiturnya.'],
                ['subject' => 'Backup data penting', 'description' => 'Mohon dibantu backup data penting di komputer saya karena akan dilakukan format ulang minggu depan.'],
                ['subject' => 'Konsultasi pembelian laptop', 'description' => 'Divisi kami butuh beli laptop baru untuk tim. Mohon konsultasi spesifikasi yang sesuai dengan budget dan kebutuhan pekerjaan.'],
                ['subject' => 'Lapor website down', 'description' => 'Website company tidak bisa diakses dari luar. Sudah dicoba dari beberapa device tetap tidak bisa kebuka. Mohon segera dicek.'],
                ['subject' => 'Request pindah meja dan setup ulang PC', 'description' => 'Saya akan pindah ke ruangan baru minggu depan. Mohon dibantu pindahan komputer dan setup ulang koneksi jaringan di meja baru.'],
            ],
        ];

        $categoryTemplates = $templates[$categoryName] ?? $templates['Other'];
        return $categoryTemplates[array_rand($categoryTemplates)];
    }

    private function generateSolutionText($categoryName)
    {
        $solutions = [
            'Hardware' => [
                'Sudah diganti dengan perangkat baru yang berfungsi normal.',
                'Komponen yang rusak sudah diperbaiki dan ditest berfungsi dengan baik.',
                'Hardware sudah dibersihkan dan dilakukan maintenance, sekarang berjalan normal.',
                'Sudah diganti dengan spare part baru dan perangkat sudah bisa digunakan kembali.',
                'Problem sudah diselesaikan dengan mengganti kabel yang rusak.',
            ],
            'Software' => [
                'Aplikasi sudah direstart dan login berfungsi normal kembali.',
                'Software sudah diupdate ke versi terbaru dan bug sudah teratasi.',
                'Sudah dilakukan reinstall aplikasi dan sekarang berfungsi dengan baik.',
                'Lisensi sudah diaktivasi ulang dan aplikasi bisa digunakan normal.',
                'Cache sudah dibersihkan dan aplikasi sudah stabil.',
            ],
            'Network' => [
                'Konfigurasi network sudah diperbaiki dan koneksi sudah stabil.',
                'Router sudah direstart dan kecepatan internet sudah normal kembali.',
                'IP Address sudah disesuaikan dan bisa akses network dengan lancar.',
                'Kabel network yang longgar sudah diperbaiki dan koneksi stabil.',
                'DNS settings sudah dikonfigurasi ulang dan internet lancar.',
            ],
            'Account & Access' => [
                'Password sudah direset dan dikirim via email. Silakan login dengan password baru.',
                'Akun baru sudah dibuat dan credentials dikirim via email.',
                'Akun sudah dibuka kembali dan bisa login normal.',
                'Permission sudah ditambahkan dan sekarang bisa akses folder yang dimaksud.',
                'Email sudah dikonfigurasi ulang dan bisa menerima email normal.',
            ],
            'Other' => [
                'Request sudah diproses dan diselesaikan sesuai prosedur.',
                'Software yang diminta sudah diinstall dan ditest berfungsi baik.',
                'Sudah diberikan panduan dan training sesuai kebutuhan.',
                'Issue sudah diselesaikan dan user sudah bisa melanjutkan pekerjaan.',
                'Problem sudah diatasi dan sistem berjalan normal kembali.',
            ],
        ];

        $categorySolutions = $solutions[$categoryName] ?? $solutions['Other'];
        return $categorySolutions[array_rand($categorySolutions)];
    }

    private function getRandomChannel()
    {
        $channels = ['web', 'email', 'phone', 'web', 'web'];
        return $channels[array_rand($channels)];
    }
}
