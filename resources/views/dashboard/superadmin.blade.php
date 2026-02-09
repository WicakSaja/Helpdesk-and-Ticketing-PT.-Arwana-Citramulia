@extends('layouts.superadmin')
@section('title', 'Dashboard Admin')

@section('css')
    {{-- Load Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @vite(['resources/css/dashboard-superadmin.css'])
@endsection

@section('content')
    <div class="welcome-banner">
        <div>
            <h2>Dashboard Overview</h2>
            <p>Selamat Datang, {{ auth()->user() ? auth()->user()->name : 'Administrator' }}! 👋</p>
        </div>
        <div style="background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 8px; font-size: 13px;">
            📅 {{ date('d F Y') }}
        </div>
    </div>

    <div id="dashboardContent">
        <div class="loading">
            <p>Loading dashboard data...</p>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Fetch dashboard data from API
        async function loadDashboard() {
            try {
                const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');

                const response = await fetch('{{ url('/api/dashboard') }}', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                const data = result.data;

                // Build HTML
                let html = `
                    <div class="stats-grid">
                        <div class="stat-card card-blue">
                            <div class="stat-info">
                                <p>Total User</p>
                                <h3>${data.summary.users}</h3>
                            </div>
                            <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                        </div>
                        <div class="stat-card card-green">
                            <div class="stat-info">
                                <p>Teknisi Aktif</p>
                                <h3>${data.summary.technicians}</h3>
                            </div>
                            <div class="stat-icon"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                        </div>
                        <div class="stat-card card-orange">
                            <div class="stat-info">
                                <p>Tiket Bulan Ini</p>
                                <h3>${data.summary.tickets_month}</h3>
                            </div>
                            <div class="stat-icon"><i class="fa-solid fa-ticket"></i></div>
                        </div>
                        <div class="stat-card card-red">
                            <div class="stat-info">
                                <p>Departemen</p>
                                <h3>${data.summary.departments}</h3>
                            </div>
                            <div class="stat-icon"><i class="fa-solid fa-building"></i></div>
                        </div>
                    </div>

                    <div class="charts-wrapper">
                        <div class="chart-card">
                            <div class="chart-header">
                                <div class="chart-title">📈 Tren Tiket (7 Hari Terakhir)</div>
                            </div>
                            <canvas id="trendChart" style="height: 300px; width: 100%;"></canvas>
                        </div>
                        <div class="chart-card">
                            <div class="chart-header">
                                <div class="chart-title">📊 Kategori Masalah</div>
                            </div>
                            <canvas id="categoryChart" style="height: 300px; width: 100%;"></canvas>
                        </div>
                    </div>

                    <div class="recent-wrapper">
                        <div class="chart-header">
                            <div class="chart-title">🕒 5 Tiket Terbaru</div>
                            <a href="/superadmin/reports" style="font-size: 12px; color: #1565c0; font-weight: 600;">Lihat Semua</a>
                        </div>
                        <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                            <table class="simple-table">
                                <thead>
                                    <tr>
                                        <th>ID Tiket</th>
                                        <th>Judul Masalah</th>
                                        <th>User</th>
                                        <th>Kategori</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.latest_tickets.map(ticket => `
                                                <tr>
                                                    <td><strong>${ticket.ticket_number}</strong></td>
                                                    <td>${ticket.subject.substring(0, 30)}${ticket.subject.length > 30 ? '...' : ''}</td>
                                                    <td>${ticket.requester?.name || 'Unknown'}</td>
                                                    <td>${ticket.category}</td>
                                                    <td><span class="badge ${getStatusBadgeClass(ticket.status)}">${ticket.status}</span></td>
                                                    <td>${new Date(ticket.created_at).toLocaleString('id-ID')}</td>
                                                </tr>
                                            `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;

                document.getElementById('dashboardContent').innerHTML = html;

                // Initialize charts after content is rendered
                initCharts(data);

            } catch (error) {
                console.error('Error:', error);
                document.getElementById('dashboardContent').innerHTML = `
                    <div class="error">
                        <strong>Error!</strong> Gagal memuat dashboard data. ${error.message}
                    </div>
                `;
            }
        }

        function getStatusBadgeClass(status) {
            const statusMap = {
                'Open': 'open',
                'Assigned': 'assigned',
                'In Progress': 'in-progress',
                'Resolved': 'resolved',
                'Closed': 'closed'
            };
            return statusMap[status] || 'open';
        }

        function initCharts(data) {
            // 1. CHART TREN TIKET (Line Chart - Modern)
            const ctxTrend = document.getElementById('trendChart');
            if (ctxTrend) {
                new Chart(ctxTrend.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: data.ticket_trend.map(t => `${t.day_name.substring(0, 3)}\n${t.date.substring(5)}`),
                        datasets: [{
                                label: 'Tiket Masuk',
                                data: data.ticket_trend.map(t => t.incoming),
                                borderColor: '#1565c0',
                                backgroundColor: 'rgba(21, 101, 192, 0.15)',
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 5,
                                pointBackgroundColor: '#1565c0',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointHoverRadius: 7
                            },
                            {
                                label: 'Tiket Selesai',
                                data: data.ticket_trend.map(t => t.solved),
                                borderColor: '#2e7d32',
                                backgroundColor: 'transparent',
                                borderWidth: 3,
                                tension: 0.4,
                                borderDash: [8, 4],
                                pointRadius: 5,
                                pointBackgroundColor: '#2e7d32',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointHoverRadius: 7
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    font: {
                                        size: 13,
                                        weight: '600'
                                    },
                                    padding: 20,
                                    usePointStyle: true
                                }
                            },
                            filler: {
                                propagate: true
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // 2. CHART KATEGORI (Doughnut - Modern)
            const ctxCat = document.getElementById('categoryChart');
            if (ctxCat) {
                // Store raw data for tooltip access
                const categoryData = data.category_distribution;

                new Chart(ctxCat.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: categoryData.map(c => c.name),
                        datasets: [{
                            data: categoryData.map(c => c.percentage),
                            backgroundColor: [
                                '#ef4444', // Red
                                '#3b82f6', // Blue
                                '#f59e0b', // Amber
                                '#8b5cf6', // Purple
                                '#10b981' // Green
                            ],
                            borderColor: '#fff',
                            borderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    font: {
                                        size: 13,
                                        weight: '600'
                                    },
                                    padding: 20,
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const index = context.dataIndex;
                                        const count = categoryData[index].count || 0;
                                        const percentage = categoryData[index].percentage || 0;
                                        return `Tiket: ${count} (${percentage.toFixed(1)}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            }
        }

        // Load dashboard on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboard();
        });
    </script>
@endsection
