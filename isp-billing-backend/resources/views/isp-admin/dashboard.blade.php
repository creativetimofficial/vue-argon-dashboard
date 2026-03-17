<!DOCTYPE html>
<html lang="id" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="/isp-admin/assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta http-equiv="Content-Security-Policy" content="script-src 'self' 'unsafe-eval' 'unsafe-inline' https://fonts.googleapis.com; object-src 'none';" />
    <title>Dashboard - {{ session('tenant_isp_name', 'ISP Admin') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/isp-admin/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="/isp-admin/assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="/isp-admin/assets/vendor/css/core.css" />
    <link rel="stylesheet" href="/isp-admin/assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="/isp-admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Helpers -->
    <script src="/isp-admin/assets/vendor/js/helpers.js"></script>
    <script src="/isp-admin/assets/js/config.js"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            @include('isp-admin.partials.sidebar')

            <!-- Layout page -->
            <div class="layout-page">
                <!-- Navbar -->
                @include('isp-admin.partials.navbar')

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <h4 class="py-3 mb-4">
                            <span class="text-muted fw-light">ISP Admin /</span> Dashboard
                        </h4>

                        <!-- Statistics Cards -->
                        <div class="row">
                            <div class="col-lg-3 col-md-6 col-6 mb-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                            <div class="avatar flex-shrink-0">
                                                <img src="/isp-admin/assets/img/icons/unicons/chart-success.png" alt="chart success" class="rounded" />
                                            </div>
                                        </div>
                                        <p class="mb-1">Total Pelanggan</p>
                                        <h4 class="card-title mb-3" id="total-customers">0</h4>
                                        <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> 0%</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-6 mb-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                            <div class="avatar flex-shrink-0">
                                                <img src="/isp-admin/assets/img/icons/unicons/wallet-info.png" alt="wallet info" class="rounded" />
                                            </div>
                                        </div>
                                        <p class="mb-1">Pelanggan Aktif</p>
                                        <h4 class="card-title mb-3" id="active-customers">0</h4>
                                        <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> 0%</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-6 mb-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                            <div class="avatar flex-shrink-0">
                                                <img src="/isp-admin/assets/img/icons/unicons/paypal.png" alt="paypal" class="rounded" />
                                            </div>
                                        </div>
                                        <p class="mb-1">Total Pendapatan</p>
                                        <h4 class="card-title mb-3" id="total-revenue">Rp 0</h4>
                                        <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> Bulan ini</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-6 mb-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                            <div class="avatar flex-shrink-0">
                                                <img src="/isp-admin/assets/img/icons/unicons/cc-success.png" alt="cc success" class="rounded" />
                                            </div>
                                        </div>
                                        <p class="mb-1">Tagihan Terbayar</p>
                                        <h4 class="card-title mb-3" id="paid-invoices">0</h4>
                                        <small class="text-muted">Dari <span id="total-invoices">0</span> tagihan</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Welcome Message -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Selamat Datang di {{ session('tenant_isp_name', 'ISP Admin') }}!</h5>
                                        <p class="card-text">Panel administrasi untuk mengelola pelanggan, billing, dan perangkat jaringan Anda.</p>
                                        <p class="text-muted">Mulai dengan menambahkan pelanggan atau mengonfigurasi perangkat Mikrotik Anda.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                © {{ date('Y') }}, {{ session('tenant_isp_name', 'ISP Admin') }}
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->
                </div>
            </div>
        </div>
    </div>

    <!-- Core JS -->
    <script src="/isp-admin/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="/isp-admin/assets/vendor/libs/popper/popper.js"></script>
    <script src="/isp-admin/assets/vendor/js/bootstrap.js"></script>
    <script src="/isp-admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="/isp-admin/assets/vendor/js/menu.js"></script>

    <!-- Main JS -->
    <script src="/isp-admin/assets/js/main.js"></script>

    <!-- Dashboard Script -->
    <script>
        // Check authentication
        const token = localStorage.getItem('isp_admin_token');
        if (!token) {
            window.location.href = '/';
        }

        // Fetch dashboard stats
        async function loadDashboardStats() {
            try {
                const response = await fetch('/api/isp-admin/dashboard-stats', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    document.getElementById('total-customers').textContent = data.total_customers || 0;
                    document.getElementById('active-customers').textContent = data.active_customers || 0;
                    document.getElementById('total-revenue').textContent = 'Rp ' + (data.total_revenue || 0).toLocaleString('id-ID');
                    document.getElementById('paid-invoices').textContent = data.paid_invoices || 0;
                    document.getElementById('total-invoices').textContent = data.total_invoices || 0;
                }
            } catch (error) {
                console.error('Error loading dashboard stats:', error);
            }
        }

        // Load stats on page load
        loadDashboardStats();
    </script>
</body>
</html>
