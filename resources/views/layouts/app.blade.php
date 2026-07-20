<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SPX Rider Attendance & Payroll Management System">
    <title>@yield('title', 'Dashboard') — SPX Attendance</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-primary:   #0f1117;
            --bg-secondary: #161b27;
            --bg-card:      #1c2236;
            --bg-hover:     #232b3e;
            --border:       #2a3450;
            --accent:       #f97316;
            --accent-dark:  #c2510a;
            --accent-light: #fdba74;
            --text-primary: #f1f5f9;
            --text-secondary:#94a3b8;
            --text-muted:   #64748b;
            --success:      #22c55e;
            --danger:       #ef4444;
            --warning:      #f59e0b;
            --info:         #38bdf8;
            --sidebar-w:    260px;
            --radius:       12px;
            --shadow:       0 4px 24px rgba(0,0,0,.4);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        .sidebar {
            width: var(--sidebar-w);
            background: var(--bg-secondary);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); box-shadow: none; }
            .sidebar.active { transform: translateX(0); box-shadow: 8px 0 40px rgba(0,0,0,.7); }
        }
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,.6);
            z-index: 900;
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
        }
        .sidebar-overlay.active { display: block; }

        .sidebar-logo {
            padding: 24px 20px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-logo .logo-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            box-shadow: 0 4px 12px rgba(249,115,22,.4);
        }

        .sidebar-logo .logo-text { font-size: 17px; font-weight: 700; letter-spacing: -.3px; }
        .sidebar-logo .logo-text span { color: var(--accent); }
        .sidebar-logo .logo-sub { font-size: 10px; color: var(--text-muted); letter-spacing: .5px; text-transform: uppercase; }

        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }

        .nav-section-label {
            font-size: 10px; font-weight: 600; letter-spacing: 1px;
            text-transform: uppercase; color: var(--text-muted);
            padding: 12px 8px 6px;
        }

        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 12px; border-radius: 8px;
            color: var(--text-secondary); text-decoration: none;
            font-size: 14px; font-weight: 500;
            transition: all .2s ease; margin-bottom: 2px;
        }

        .nav-item:hover { background: var(--bg-hover); color: var(--text-primary); }

        .nav-item.active {
            background: rgba(249,115,22,.15);
            color: var(--accent);
            border-left: 3px solid var(--accent);
        }

        .nav-item .nav-icon { width: 18px; text-align: center; font-size: 15px; }

        .sidebar-footer { padding: 16px 12px; border-top: 1px solid var(--border); }

        .user-card {
            display: flex; align-items: center; gap: 10px;
            padding: 10px; border-radius: 8px;
            background: var(--bg-card); margin-bottom: 10px;
        }

        .user-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; flex-shrink: 0;
        }

        .user-name { font-size: 13px; font-weight: 600; }
        .user-role { font-size: 11px; color: var(--text-muted); text-transform: capitalize; }

        .btn-logout {
            display: flex; align-items: center; gap: 8px;
            width: 100%; padding: 9px 12px; border-radius: 8px;
            background: transparent; border: 1px solid var(--border);
            color: var(--text-secondary); font-size: 13px; font-weight: 500;
            cursor: pointer; transition: all .2s; text-decoration: none;
        }

        .btn-logout:hover { background: rgba(239,68,68,.1); border-color: var(--danger); color: var(--danger); }

        .main { transition: margin-left 0.3s ease; }
        @media (min-width: 993px) { .main { margin-left: var(--sidebar-w); } }

        .topbar {
            height: 64px; background: var(--bg-secondary);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 20px; position: sticky; top: 0; z-index: 500;
        }

        .hamburger {
            display: none; background: transparent; border: 1px solid var(--border);
            color: var(--text-primary); padding: 8px 12px; border-radius: 6px; cursor: pointer;
        }
        @media (max-width: 992px) { .hamburger { display: block; } }

        .page-title { font-size: 18px; font-weight: 700; }
        .page-title .breadcrumb { font-size: 12px; color: var(--text-muted); font-weight: 400; margin-top: 2px; }
        .topbar-right { display: flex; align-items: center; gap: 12px; }

        .content { padding: 20px; }
        @media (min-width: 768px) { .content { padding: 28px; } }

        .card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius); padding: 24px; }
        .card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
        .card-title { font-size: 16px; font-weight: 600; }

        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 9px 18px; border-radius: 8px;
            font-size: 13.5px; font-weight: 600;
            cursor: pointer; border: none; text-decoration: none;
            transition: all .2s ease; white-space: nowrap;
        }

        .btn-primary { background: var(--accent); color: #fff; }
        .btn-primary:hover { background: var(--accent-dark); box-shadow: 0 4px 12px rgba(249,115,22,.4); transform: translateY(-1px); }

        .btn-secondary { background: var(--bg-hover); color: var(--text-primary); border: 1px solid var(--border); }
        .btn-secondary:hover { background: var(--border); }

        .btn-danger { background: rgba(239,68,68,.15); color: var(--danger); border: 1px solid rgba(239,68,68,.3); }
        .btn-danger:hover { background: var(--danger); color: #fff; }

        .btn-info { background: rgba(56,189,248,.15); color: var(--info); border: 1px solid rgba(56,189,248,.3); }
        .btn-info:hover { background: var(--info); color: var(--bg-primary); }

        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .btn-icon { padding: 8px 10px; }

        .table-wrap { overflow-x: auto; border-radius: 10px; border: 1px solid var(--border); }

        table { width: 100%; border-collapse: collapse; font-size: 14px; }

        th {
            background: var(--bg-hover); padding: 12px 16px;
            text-align: left; font-size: 11px; font-weight: 600;
            letter-spacing: .7px; text-transform: uppercase;
            color: var(--text-muted); border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        td { padding: 13px 16px; border-bottom: 1px solid var(--border); color: var(--text-primary); vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tbody tr { transition: background .15s; }
        tbody tr:hover { background: var(--bg-hover); }

        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 20px;
            font-size: 11.5px; font-weight: 600;
        }

        .badge-success  { background: rgba(34,197,94,.15);  color: var(--success); }
        .badge-danger   { background: rgba(239,68,68,.15);  color: var(--danger); }
        .badge-warning  { background: rgba(245,158,11,.15); color: var(--warning); }
        .badge-info     { background: rgba(56,189,248,.15); color: var(--info); }
        .badge-muted    { background: rgba(100,116,139,.15);color: var(--text-muted); }
        .badge-accent   { background: rgba(249,115,22,.15); color: var(--accent); }

        .form-group { margin-bottom: 18px; }

        label { display: block; font-size: 13px; font-weight: 500; color: var(--text-secondary); margin-bottom: 6px; }

        input[type="text"], input[type="email"], input[type="password"],
        input[type="number"], input[type="date"], input[type="tel"],
        select, textarea {
            width: 100%; background: var(--bg-primary); border: 1px solid var(--border);
            border-radius: 8px; padding: 10px 14px; color: var(--text-primary);
            font-size: 14px; font-family: 'Inter', sans-serif;
            transition: border-color .2s, box-shadow .2s;
        }

        input:focus, select:focus, textarea:focus {
            outline: none; border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(249,115,22,.15);
        }

        .date-picker { position: relative; display: inline-flex; align-items: center; }
        .date-picker input[type="date"] {
            width: auto; padding: 9px 14px 9px 40px; border: 1.5px solid var(--border);
            border-radius: 10px; background: var(--bg-card); color: var(--text-primary);
            font-size: 14px; font-weight: 500; cursor: pointer;
        }

        .date-picker .dp-icon { position: absolute; left: 12px; color: var(--accent); }

        .form-check { display: flex; align-items: center; gap: 10px; cursor: pointer; }

        input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--accent); cursor: pointer; }

        .form-error { color: var(--danger); font-size: 12px; margin-top: 4px; }

        .form-row { display: grid; gap: 16px; }
        .form-row-2 { grid-template-columns: 1fr 1fr; }
        .form-row-3 { grid-template-columns: 1fr 1fr 1fr; }

        .alert { display: flex; align-items: flex-start; gap: 12px; padding: 14px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; }
        .alert-success { background: rgba(34,197,94,.1);  border: 1px solid rgba(34,197,94,.3);  color: var(--success); }
        .alert-danger  { background: rgba(239,68,68,.1);  border: 1px solid rgba(239,68,68,.3);  color: var(--danger); }
        .alert-warning { background: rgba(245,158,11,.1); border: 1px solid rgba(245,158,11,.3); color: var(--warning); }
        .alert-info    { background: rgba(56,189,248,.1); border: 1px solid rgba(56,189,248,.3); color: var(--info); }

        .pagination { display: flex; align-items: center; gap: 6px; padding: 16px 0 4px; justify-content: center; flex-wrap: wrap; }
        .pagination a, .pagination span { padding: 7px 13px; border-radius: 6px; font-size: 13px; font-weight: 500; color: var(--text-secondary); background: var(--bg-card); border: 1px solid var(--border); text-decoration: none; transition: all .2s; }
        .pagination a:hover { background: var(--bg-hover); color: var(--text-primary); }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 28px; }
        .stat-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius); padding: 20px; display: flex; align-items: flex-start; gap: 16px; transition: transform .2s, box-shadow .2s; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow); }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
        .stat-icon-orange { background: rgba(249,115,22,.15); color: var(--accent); }
        .stat-icon-green  { background: rgba(34,197,94,.15);  color: var(--success); }
        .stat-icon-red    { background: rgba(239,68,68,.15);  color: var(--danger); }
        .stat-icon-blue   { background: rgba(56,189,248,.15); color: var(--info); }
        .stat-icon-yellow { background: rgba(245,158,11,.15); color: var(--warning); }
        .stat-value { font-size: 28px; font-weight: 800; line-height: 1; }
        .stat-label { font-size: 12px; color: var(--text-muted); margin-top: 4px; }

        .filter-bar { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 20px; }
        .filter-bar .form-group { margin-bottom: 0; }
        .filter-bar select, .filter-bar input { min-width: 140px; }

        .divider { height: 1px; background: var(--border); margin: 20px 0; }

        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
        .page-header h1 { font-size: 22px; font-weight: 700; }
        .page-header p { font-size: 13px; color: var(--text-muted); margin-top: 2px; }

        .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 5px; }
        .dot-green  { background: var(--success); box-shadow: 0 0 6px var(--success); }
        .dot-red    { background: var(--danger);  box-shadow: 0 0 6px var(--danger); }
        .dot-yellow { background: var(--warning); box-shadow: 0 0 6px var(--warning); }
        .dot-gray   { background: var(--text-muted); }

        .empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); }
        .empty-state i { font-size: 48px; margin-bottom: 16px; display: block; opacity: .5; }
        .empty-state p { font-size: 14px; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .content > * { animation: fadeIn .3s ease; }

        /* ── Tablet (≤ 1024px) ─────────────────── */
        @media (max-width: 1024px) {
            .topbar { padding: 0 20px; }
        }

        /* ── Mobile (≤ 768px) ──────────────────── */
        @media (max-width: 768px) {
            .form-row-2, .form-row-3 { grid-template-columns: 1fr; }
            .topbar-right span { display: none; }
            .content { padding: 16px; }
            .card { padding: 16px; }
            .page-header h1 { font-size: 18px; }
            .page-header { margin-bottom: 16px; }
            .stats-grid { grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 18px; }
            .stat-card { padding: 14px; gap: 10px; }
            .stat-icon { width: 40px; height: 40px; font-size: 17px; border-radius: 10px; }
            .stat-value { font-size: 22px; }
            .filter-bar select, .filter-bar input { min-width: 0; }
            th, td { padding: 10px 12px; font-size: 13px; }
            .table-wrap { -webkit-overflow-scrolling: touch; }
            .date-picker { display: flex; width: 100%; }
            .date-picker input[type="date"] { width: 100%; min-width: 0; }
            .btn { font-size: 13px; padding: 8px 14px; }
        }

        /* ── Small mobile (≤ 480px) ────────────── */
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
            .hide-xs { display: none !important; }
            .page-header { flex-direction: column; align-items: flex-start; }
            .page-header .btn { width: 100%; justify-content: center; }
            .nav-item { padding: 12px 14px; font-size: 15px; }
            .btn-sm { padding: 5px 10px; font-size: 11.5px; }
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="sidebar-overlay" onclick="toggleSidebar()"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">🚴</div>
        <div>
            <div class="logo-text"><span>SPX</span> Riders</div>
            <div class="logo-sub">Attendance System</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        @if(auth()->user()->isAdmin())
        <div class="nav-section-label">Main</div>
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-gauge-high"></i></span> Dashboard
        </a>

        <div class="nav-section-label">Management</div>
        <a href="{{ route('riders.index') }}" class="nav-item {{ request()->routeIs('riders.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-user-group"></i></span> Riders
        </a>
        <a href="{{ route('spx-accounts.index') }}" class="nav-item {{ request()->routeIs('spx-accounts.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-boxes-stacked"></i></span> SPX Accounts
        </a>
        <a href="{{ route('admin-users.index') }}" class="nav-item {{ request()->routeIs('admin-users.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-user-shield"></i></span> Admin Users
        </a>

        <div class="nav-section-label">Payroll</div>
        <a href="{{ route('attendance.index') }}" class="nav-item {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-calendar-check"></i></span> Attendance
        </a>
        <a href="{{ route('cash-advances.index') }}" class="nav-item {{ request()->routeIs('cash-advances.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-money-bill-wave"></i></span> Cash Advances
        </a>
        <a href="{{ route('payslips.index') }}" class="nav-item {{ request()->routeIs('payslips.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-file-invoice-dollar"></i></span> Payslips
        </a>
        <a href="{{ route('financials.index') }}" class="nav-item {{ request()->routeIs('financials.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-wallet"></i></span> Finance
        </a>

        @else
        <div class="nav-section-label">My Account</div>
        @if(auth()->user()->rider)
        <a href="{{ route('rider.dashboard', auth()->user()->rider) }}" class="nav-item {{ request()->routeIs('rider.dashboard') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-gauge-high"></i></span> My Dashboard
        </a>
        @endif
        <a href="{{ route('payslips.index') }}" class="nav-item {{ request()->routeIs('payslips.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-file-invoice-dollar"></i></span> My Payslips
        </a>
        @endif

        <div class="nav-section-label">Account</div>
        <a href="{{ route('profile.edit') }}" class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-circle-user"></i></span> Profile
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ auth()->user()->role }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="fa-solid fa-right-from-bracket"></i> Sign out
            </button>
        </form>
    </div>
</aside>

<div class="main">
    <header class="topbar">
        <button class="hamburger" onclick="toggleSidebar()">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="page-title">
            @yield('page-title', 'Dashboard')
            <div class="breadcrumb">@yield('breadcrumb', 'SPX Attendance System')</div>
        </div>
        <div class="topbar-right">
            <span style="font-size:13px; color:var(--text-muted);">
                <i class="fa-regular fa-clock"></i> {{ now()->format('D, M d Y') }}
            </span>
        </div>
    </header>

    <div class="content">
        @if(session('success'))
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div>
                    <strong>Please fix the following errors:</strong>
                    <ul style="margin-top:6px; padding-left:16px;">
                        @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                    </ul>
                </div>
            </div>
        @endif
        @yield('content')
    </div>
</div>

<script>
    const sidebar   = document.getElementById('sidebar');
    const overlay   = document.querySelector('.sidebar-overlay');

    function openSidebar() {
        sidebar.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    function toggleSidebar() {
        sidebar.classList.contains('active') ? closeSidebar() : openSidebar();
    }

    // Close on overlay click
    overlay.addEventListener('click', closeSidebar);

    // Close on Escape key
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });

    // Auto-close sidebar when a nav link is tapped (mobile)
    sidebar.querySelectorAll('.nav-item').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 992) closeSidebar();
        });
    });

    // Reset sidebar state on desktop resize
    window.addEventListener('resize', () => {
        if (window.innerWidth > 992) {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
</script>
@stack('scripts')
</body>
</html>
