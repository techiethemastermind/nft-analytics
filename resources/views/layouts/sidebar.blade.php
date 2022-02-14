<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">{{ config('app.name', 'Laravel') }}</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item -->
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('page.token') }}">
            <i class="fas fa-fw fa-table"></i>
            <span>Token List</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('nft-overview') }}">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>NFT Total Overview</span></a>
    </li>

    <!-- Nav Item -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('floor-overview') }}">
            <i class="fas fa-fw fa-table"></i>
            <span>Floor Daily Overview</span></a>
    </li>

    <!-- Nav Item -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('nft-sniper') }}">
            <i class="fas fa-fw fa-table"></i>
            <span>NFT Sniper Overview</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('config') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Setting</span></a>
    </li>

    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('home') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Action</span></a>
    </li>

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!-- End of Sidebar -->