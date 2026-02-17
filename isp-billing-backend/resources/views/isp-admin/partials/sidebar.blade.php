<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="/dashboard" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bold ms-2">{{ session('tenant_isp_name', 'ISP Admin') }}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-md d-block align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
            <a href="/dashboard" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-smile"></i>
                <div>Dashboard</div>
            </a>
        </li>

        <!-- Managemen Pelanggan -->
        <li class="menu-item {{ request()->is('customers*') ? 'active' : '' }}">
            <a href="/customers" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div>Managemen Pelanggan</div>
            </a>
        </li>

        <!-- Paket Internet -->
        <li class="menu-item {{ request()->is('packages*') ? 'active' : '' }}">
            <a href="/packages" class="menu-link">
                <i class="menu-icon tf-icons bx bx-package"></i>
                <div>Paket Internet</div>
            </a>
        </li>

        <!-- Mikrotik Management -->
        <li class="menu-item {{ request()->is('mikrotik*') ? 'active' : '' }}">
            <a href="/mikrotik" class="menu-link">
                <i class="menu-icon tf-icons bx bx-server"></i>
                <div>Managemen Mikrotik</div>
            </a>
        </li>

        <!-- Divider -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Operasional</span>
        </li>

        <!-- Tiket -->
        <li class="menu-item {{ request()->is('tickets*') ? 'active' : '' }}">
            <a href="/tickets" class="menu-link">
                <i class="menu-icon tf-icons bx bx-support"></i>
                <div>Tiket Support</div>
            </a>
        </li>

        <!-- Divider -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Keuangan</span>
        </li>

        <!-- Invoice -->
        <li class="menu-item {{ request()->is('invoices*') ? 'active' : '' }}">
            <a href="/invoices" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div>Invoice</div>
            </a>
        </li>

        <!-- Laporan -->
        <li class="menu-item {{ request()->is('reports*') ? 'active' : '' }}">
            <a href="/reports" class="menu-link">
                <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
                <div>Laporan</div>
            </a>
        </li>

        <!-- Divider -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Pengaturan</span>
        </li>

        <!-- Settings -->
        <li class="menu-item {{ request()->is('settings*') ? 'active' : '' }}">
            <a href="/settings" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div>Pengaturan</div>
            </a>
        </li>
    </ul>
</aside>
