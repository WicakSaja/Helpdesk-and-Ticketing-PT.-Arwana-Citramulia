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

    /**
     * Generate 100 dummy tickets dengan berbagai status
     * Interval: 1 Januari 2026 - 30 Januari 2026
     */
    public function run(): void
    {
        $this->command->info('🎫 Generating 100 dummy tickets...');

        // Load data reference
        $this->loadReferences();

        // Status distribution for 100 tickets
        $statusDistribution = [
            'OPEN' => 15,
            'ASSIGNED' => 20,
            'IN PROGRESS' => 25,
            'RESOLVED' => 25,
            'CLOSED' => 15,
        ];

        $ticketCounter = 1;

        foreach ($statusDistribution as $statusName => $count) {
            $this->command->info("Creating {$count} tickets with status: {$statusName}");
            
            for ($i = 0; $i < $count; $i++) {
                $this->createTicket($statusName, $ticketCounter);
                $ticketCounter++;
            }
        }

        $this->command->info('✅ 100 dummy tickets created successfully!');
        $this->command->info('📅 Date range: 1 Jan 2026 - 30 Jan 2026');
    }

    /**
     * Load reference data (users, categories, statuses)
     */
    private function loadReferences()
    {
        $this->requesters = User::role('requester')->get();
        $this->technicians = User::role('technician')->get();
        $this->helpdeskUsers = User::role('helpdesk')->get();
        $this->categories = Category::all();
        
        // Get statuses (lowercase as per TicketStatusSeeder)
        $this->statuses = [
            'OPEN' => TicketStatus::where('name', 'open')->first(),
            'ASSIGNED' => TicketStatus::where('name', 'assigned')->first(),
            'IN PROGRESS' => TicketStatus::where('name', 'in progress')->first(),
            'RESOLVED' => TicketStatus::where('name', 'resolved')->first(),
            'CLOSED' => TicketStatus::where('name', 'closed')->first(),
        ];

        if ($this->requesters->isEmpty()) {
            $this->command->error('No requesters found! Run DummyUserSeeder first.');
            exit(1);
        }

        if ($this->technicians->isEmpty()) {
            $this->command->error('No technicians found! Run DummyUserSeeder first.');
            exit(1);
        }
    }

    /**
     * Create a single ticket with all relations
     */
    private function createTicket($statusName, $counter)
    {
        // Random date dalam Januari 2026
        $createdAt = Carbon::create(2026, 1, rand(1, 30))
            ->setTime(rand(8, 17), rand(0, 59), rand(0, 59));

        // Generate ticket number format: TKT-2026-000001
        $ticketNumber = 'TKT-2026-' . str_pad($counter, 6, '0', STR_PAD_LEFT);

        // Random requester dan category
        $requester = $this->requesters->random();
        $category = $this->categories->random();

        // Generate realistic subject dan description
        $ticketData = $this->generateTicketContent($category->name);

        // Create ticket
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

        // Create initial log
        TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $requester->id,
            'action' => 'created',
            'description' => 'Ticket dibuat oleh ' . $requester->name,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        // Handle different status workflows
        switch ($statusName) {
            case 'OPEN':
                // No assignment, just initial state
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

    /**
     * Assign ticket to technician
     */
    private function assignTicket($ticket, $previousTime)
    {
        $assignedAt = $previousTime->copy()->addMinutes(rand(10, 120));
        $technician = $this->technicians->random();
        $helpdesk = $this->helpdeskUsers->random();

        TicketAssignment::create([
            'ticket_id' => $ticket->id,
            'assigned_to' => $technician->id,
            'assigned_by' => $helpdesk->id,
            'assigned_at' => $assignedAt,
            'notes' => 'Assigned automatically by system',
        ]);

        TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $helpdesk->id,
            'action' => 'assigned',
            'description' => "Ticket assigned to {$technician->name} by {$helpdesk->name}",
            'created_at' => $assignedAt,
            'updated_at' => $assignedAt,
        ]);

        $ticket->update([
            'status_id' => $this->statuses['ASSIGNED']->id,
            'updated_at' => $assignedAt,
        ]);

        return $assignedAt;
    }

    /**
     * Technician confirms and starts working
     */
    private function confirmTicket($ticket, $previousTime)
    {
        $confirmedAt = $previousTime->copy()->addMinutes(rand(15, 180));
        $technician = $ticket->assignment->technician;

        TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $technician->id,
            'action' => 'confirmed',
            'description' => "Ticket confirmed by {$technician->name}",
            'created_at' => $confirmedAt,
            'updated_at' => $confirmedAt,
        ]);

        $ticket->update([
            'status_id' => $this->statuses['IN PROGRESS']->id,
            'updated_at' => $confirmedAt,
        ]);

        return $confirmedAt;
    }

    /**
     * Technician resolves ticket with solution
     */
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

        // Create technician history
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
            'description' => "Ticket resolved by {$technician->name}",
            'created_at' => $resolvedAt,
            'updated_at' => $resolvedAt,
        ]);

        $ticket->update([
            'status_id' => $this->statuses['RESOLVED']->id,
            'updated_at' => $resolvedAt,
        ]);

        return $resolvedAt;
    }

    /**
     * Close ticket (by helpdesk or requester)
     */
    private function closeTicket($ticket, $previousTime)
    {
        $closedAt = $previousTime->copy()->addHours(rand(1, 24));
        $closer = rand(0, 1) === 0 ? $this->helpdeskUsers->random() : $ticket->requester;

        TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $closer->id,
            'action' => 'closed',
            'description' => "Ticket closed by {$closer->name}",
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

    /**
     * Generate realistic ticket content based on category
     */
    private function generateTicketContent($categoryName)
    {
        $templates = [
            'Hardware' => [
                ['subject' => 'Komputer tidak bisa menyala', 'description' => 'Komputer di ruang produksi tidak mau menyala sejak pagi. Sudah dicoba beberapa kali tetap tidak ada respon. Mohon segera dicek karena menghambat pekerjaan.'],
                ['subject' => 'Printer error terus menerus', 'description' => 'Printer Canon di lantai 2 selalu error saat mencetak. Muncul pesan "Paper Jam" padahal tidak ada kertas yang nyangkut. Sudah dicoba restart tetap sama.'],
                ['subject' => 'Keyboard rusak beberapa tombol', 'description' => 'Keyboard user tidak berfungsi untuk tombol huruf A, S, D. Mengganggu pekerjaan input data. Mohon diganti atau diperbaiki.'],
                ['subject' => 'Monitor mati total', 'description' => 'Monitor komputer di meja saya tiba-tiba mati dan tidak mau hidup lagi. Lampu indikator tidak menyala sama sekali.'],
                ['subject' => 'Mouse wireless tidak connect', 'description' => 'Mouse wireless tidak bisa connect ke komputer. Sudah ganti baterai tetap tidak terdeteksi. USB receiver sudah dipasang dengan benar.'],
            ],
            'Software' => [
                ['subject' => 'Aplikasi ERP tidak bisa login', 'description' => 'Tidak bisa login ke aplikasi ERP sejak update kemarin. Muncul pesan error "Authentication Failed". Username dan password sudah benar.'],
                ['subject' => 'Microsoft Office tidak aktif', 'description' => 'Microsoft Office di komputer saya muncul notifikasi "Product Activation Failed". Mohon dibantu aktivasi ulang agar bisa digunakan.'],
                ['subject' => 'Email tidak bisa kirim attachment', 'description' => 'Saat mengirim email dengan attachment file PDF lebih dari 5MB selalu gagal. Muncul pesan error "Failed to send". Urgent karena harus kirim laporan.'],
                ['subject' => 'Antivirus expired perlu update', 'description' => 'Antivirus di komputer sudah expired dan muncul notifikasi terus menerus. Mohon diupdate agar tetap aman dari virus.'],
                ['subject' => 'Aplikasi inventory crash', 'description' => 'Aplikasi inventory tiba-tiba crash saat input data. Setiap kali buka aplikasi langsung not responding. Mohon segera diperbaiki.'],
            ],
            'Network' => [
                ['subject' => 'Internet sangat lambat', 'description' => 'Koneksi internet di ruangan kami sangat lambat sejak tadi pagi. Loading website lama sekali, bahkan untuk buka email saja lemot. Mohon dicek.'],
                ['subject' => 'WiFi tidak bisa connect', 'description' => 'WiFi kantor tidak bisa diakses dari laptop saya. Muncul pesan "Can\'t connect to this network". WiFi terlihat tapi tidak bisa connect.'],
                ['subject' => 'Tidak bisa akses shared folder', 'description' => 'Tidak bisa membuka shared folder di server. Muncul pesan "Network path not found". Kemarin masih bisa akses normal.'],
                ['subject' => 'VPN tidak konek', 'description' => 'VPN untuk remote access tidak bisa connect. Stuck di "Connecting..." terus. Perlu akses server dari rumah untuk WFH.'],
                ['subject' => 'Jaringan LAN putus-putus', 'description' => 'Koneksi internet via kabel LAN sering putus-putus. Harus cabut pasang berkali-kali baru bisa connect lagi. Sangat mengganggu pekerjaan.'],
            ],
            'Account & Access' => [
                ['subject' => 'Lupa password sistem', 'description' => 'Saya lupa password untuk login ke sistem payroll. Sudah coba beberapa kali tetap salah. Mohon dibantu reset password.'],
                ['subject' => 'Request akun baru karyawan', 'description' => 'Karyawan baru a.n. Dedi Saputra memerlukan akun email dan akses ke sistem ERP. Divisi: Produksi. Mulai kerja Senin depan.'],
                ['subject' => 'Akun terkunci setelah salah password', 'description' => 'Akun saya terkunci karena salah input password 3x. Tidak bisa login ke semua sistem. Mohon segera dibuka kembali aksesnya.'],
                ['subject' => 'Butuh akses tambahan ke folder', 'description' => 'Saya perlu akses ke folder Finance di shared drive untuk keperluan audit. Saat ini tidak punya permission untuk buka folder tersebut.'],
                ['subject' => 'Email tidak bisa menerima', 'description' => 'Email saya tidak bisa menerima email baru sejak kemarin sore. Saat ada yang kirim email, mereka dapat bounce back message. Mohon dicek.'],
            ],
            'Other' => [
                ['subject' => 'Minta install software Zoom', 'description' => 'Mohon dibantu install aplikasi Zoom di laptop untuk keperluan meeting online dengan client. Urgent karena meeting besok pagi.'],
                ['subject' => 'Request training sistem baru', 'description' => 'Mohon diadakan training untuk sistem ERP yang baru karena masih banyak yang belum paham cara menggunakan fitur-fiturnya.'],
                ['subject' => 'Backup data penting', 'description' => 'Mohon dibantu backup data penting di komputer saya karena akan dilakukan format ulang minggu depan.'],
                ['subject' => 'Konsultasi pembelian laptop', 'description' => 'Divisi kami butuh beli laptop baru untuk tim. Mohon konsultasi spesifikasi yang sesuai dengan budget dan kebutuhan pekerjaan.'],
                ['subject' => 'Lapor website down', 'description' => 'Website company tidak bisa diakses dari luar. Sudah dicoba dari beberapa device tetap tidak bisa kebuka. Mohon segera dicek.'],
            ],
        ];

        $categoryTemplates = $templates[$categoryName] ?? $templates['Other'];
        $selected = $categoryTemplates[array_rand($categoryTemplates)];

        return $selected;
    }

    /**
     * Generate realistic solution text
     */
    private function generateSolutionText($categoryName)
    {
        $solutions = [
            'Hardware' => [
                'Sudah diganti dengan perangkat baru yang berfungsi normal.',
                'Komponen yang rusak sudah diperbaiki dan ditest berfungsi dengan baik.',
                'Hardware sudah dibersihkan dan dilakukan maintenance, sekarang berjalan normal.',
                'Sudah diganti dengan spare part baru dan komputer sudah bisa digunakan kembali.',
                'Problem sudah diselesaikan dengan mengganti kabel yang rusak.',
            ],
            'Software' => [
                'Aplikasi sudah direstart dan login berfungsi normal kembali.',
                'Software sudah diupdate ke versi terbaru dan bug sudah teratasi.',
                'Sudah dilakukan reinstall aplikasi dan sekarang berfungsi dengan baik.',
                'Lisensi sudah diaktivasi ulang dan aplikasi bisa digunakan normal.',
                'Bug sudah diperbaiki dan aplikasi sudah stabil.',
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

    /**
     * Get random channel
     */
    private function getRandomChannel()
    {
        $channels = ['web', 'email', 'phone', 'web', 'web']; // web lebih dominan
        return $channels[array_rand($channels)];
    }
}
