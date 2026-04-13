<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Gala Tabaski Act 3</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

    {{-- Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

    <style>
        :root {
            --noir:   #0a0a0a;
            --noir2:  #111111;
            --noir3:  #1a1a1a;
            --or:     #c9a84c;
            --or2:    #e8c76a;
            --or3:    #f5dfa0;
            --blanc:  #f5f4ef;
            --gris1:  #2a2a2a;
            --gris2:  #3a3a3a;
            --gris3:  #888;
            --rouge:  #c0392b;
            --vert:   #27ae60;
            --sidebar-w: 260px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--noir);
            color: var(--blanc);
            display: flex;
            min-height: 100vh;
            font-size: 14px;
        }

        /* ---- SIDEBAR ---- */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--noir2);
            border-right: 1px solid var(--gris1);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 28px 24px 20px;
            border-bottom: 1px solid var(--gris1);
        }
        .sidebar-brand .title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 20px;
            font-weight: 600;
            color: var(--or);
            letter-spacing: .5px;
            line-height: 1.2;
        }
        .sidebar-brand .sub {
            font-size: 11px;
            color: var(--gris3);
            margin-top: 4px;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .sidebar-nav { padding: 16px 0; flex: 1; }

        .nav-section {
            padding: 8px 24px 4px;
            font-size: 10px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--gris3);
            font-weight: 500;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 24px;
            color: #aaa;
            text-decoration: none;
            transition: all .18s ease;
            border-left: 2px solid transparent;
            font-size: 13.5px;
            font-weight: 400;
        }
        .nav-item:hover {
            color: var(--blanc);
            background: var(--gris1);
        }
        .nav-item.active {
            color: var(--or);
            border-left-color: var(--or);
            background: rgba(201, 168, 76, .08);
            font-weight: 500;
        }
        .nav-item .icon {
            width: 18px;
            text-align: center;
            font-size: 15px;
            opacity: .8;
        }

        .sidebar-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--gris1);
        }
        .sidebar-footer .admin-name {
            font-size: 13px;
            color: var(--blanc);
            font-weight: 500;
        }
        .sidebar-footer .admin-role {
            font-size: 11px;
            color: var(--gris3);
            margin-top: 2px;
        }
        .logout-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 10px;
            padding: 7px 14px;
            background: transparent;
            border: 1px solid var(--gris2);
            color: var(--gris3);
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            transition: all .18s;
            text-decoration: none;
        }
        .logout-btn:hover { border-color: var(--rouge); color: var(--rouge); }

        /* ---- MAIN CONTENT ---- */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            height: 56px;
            background: var(--noir2);
            border-bottom: 1px solid var(--gris1);
            display: flex;
            align-items: center;
            padding: 0 32px;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .topbar .page-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 18px;
            font-weight: 500;
            color: var(--blanc);
        }
        .topbar .breadcrumb {
            font-size: 12px;
            color: var(--gris3);
        }

        .content { padding: 32px; flex: 1; }

        /* ---- ALERTS ---- */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success { background: rgba(39,174,96,.12); border: 1px solid rgba(39,174,96,.3); color: #5dbb7a; }
        .alert-error   { background: rgba(192,57,43,.12); border: 1px solid rgba(192,57,43,.3); color: #e05a4b; }

        /* ---- CARDS ---- */
        .card {
            background: var(--noir2);
            border: 1px solid var(--gris1);
            border-radius: 12px;
            padding: 24px;
        }
        .card-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 16px;
            font-weight: 500;
            color: var(--or);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* ---- KPI CARDS ---- */
        .kpi {
            background: var(--noir2);
            border: 1px solid var(--gris1);
            border-radius: 12px;
            padding: 20px;
            transition: border-color .2s;
        }
        .kpi:hover { border-color: var(--gris2); }
        .kpi .label { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: var(--gris3); margin-bottom: 8px; }
        .kpi .value { font-family: 'Cormorant Garamond', serif; font-size: 32px; font-weight: 600; color: var(--or); line-height: 1; }
        .kpi .sub   { font-size: 12px; color: var(--gris3); margin-top: 4px; }

        /* ---- TABLE ---- */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        thead th {
            padding: 10px 14px;
            text-align: left;
            font-size: 10px;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--gris3);
            border-bottom: 1px solid var(--gris1);
            font-weight: 500;
        }
        tbody tr { border-bottom: 1px solid rgba(255,255,255,.04); transition: background .15s; }
        tbody tr:hover { background: rgba(255,255,255,.03); }
        tbody td { padding: 11px 14px; color: #ccc; vertical-align: middle; }

        /* ---- BADGES ---- */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: .3px;
        }
        .badge-success { background: rgba(39,174,96,.15); color: #5dbb7a; border: 1px solid rgba(39,174,96,.25); }
        .badge-pending { background: rgba(201,168,76,.15); color: var(--or); border: 1px solid rgba(201,168,76,.25); }
        .badge-failed  { background: rgba(192,57,43,.15); color: #e05a4b; border: 1px solid rgba(192,57,43,.25); }
        .badge-active  { background: rgba(39,174,96,.15); color: #5dbb7a; border: 1px solid rgba(39,174,96,.25); }
        .badge-inactive{ background: rgba(136,136,136,.15); color: var(--gris3); border: 1px solid rgba(136,136,136,.25); }

        /* ---- BOUTONS ---- */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 500;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            text-decoration: none;
            border: none;
            transition: all .18s;
        }
        .btn-or {
            background: var(--or);
            color: var(--noir);
        }
        .btn-or:hover { background: var(--or2); }
        .btn-outline {
            background: transparent;
            border: 1px solid var(--gris2);
            color: #aaa;
        }
        .btn-outline:hover { border-color: var(--or); color: var(--or); }
        .btn-danger {
            background: transparent;
            border: 1px solid rgba(192,57,43,.4);
            color: #e05a4b;
        }
        .btn-danger:hover { background: rgba(192,57,43,.12); }
        .btn-sm { padding: 5px 12px; font-size: 12px; }

        /* ---- FORMS ---- */
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 12px; color: var(--gris3); margin-bottom: 6px; letter-spacing: .3px; }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            background: var(--noir3);
            border: 1px solid var(--gris2);
            border-radius: 8px;
            color: var(--blanc);
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            transition: border-color .18s;
            outline: none;
        }
        .form-control:focus { border-color: var(--or); }
        .form-control::placeholder { color: var(--gris3); }
        textarea.form-control { resize: vertical; min-height: 90px; }
        select.form-control option { background: var(--noir2); }

        .form-hint { font-size: 11px; color: var(--gris3); margin-top: 4px; }
        .form-error { font-size: 11px; color: #e05a4b; margin-top: 4px; }

        /* ---- TOGGLE SWITCH ---- */
        .toggle-label { display: flex; align-items: center; gap: 12px; cursor: pointer; user-select: none; }
        .toggle-label input { display: none; }
        .toggle-track {
            width: 44px; height: 24px;
            background: var(--gris2);
            border-radius: 12px;
            position: relative;
            transition: background .2s;
        }
        .toggle-track::after {
            content: '';
            position: absolute;
            width: 18px; height: 18px;
            background: #fff;
            border-radius: 50%;
            top: 3px; left: 3px;
            transition: transform .2s;
        }
        .toggle-label input:checked ~ .toggle-track { background: var(--or); }
        .toggle-label input:checked ~ .toggle-track::after { transform: translateX(20px); }

        /* ---- GRID UTILS ---- */
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
        @media(max-width:1100px){ .grid-4 { grid-template-columns: repeat(2,1fr); } }
        @media(max-width:800px) { .grid-4,.grid-3,.grid-2 { grid-template-columns: 1fr; } }

        /* ---- PROGRESS BAR ---- */
        .progress { background: var(--gris1); border-radius: 4px; height: 6px; overflow: hidden; }
        .progress-bar { height: 100%; background: linear-gradient(90deg, var(--or), var(--or2)); border-radius: 4px; transition: width .4s ease; }

        /* ---- PAGINATION ---- */
        .pagination { display: flex; gap: 6px; margin-top: 20px; }
        .pagination a, .pagination span {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            border: 1px solid var(--gris1);
            color: #aaa;
            text-decoration: none;
            transition: all .15s;
        }
        .pagination a:hover { border-color: var(--or); color: var(--or); }
        .pagination .active span { background: var(--or); color: var(--noir); border-color: var(--or); }

        /* ---- MISC ---- */
        .text-or    { color: var(--or); }
        .text-gris  { color: var(--gris3); }
        .text-vert  { color: #5dbb7a; }
        .text-rouge { color: #e05a4b; }
        .mt-4 { margin-top: 16px; }
        .mt-6 { margin-top: 24px; }
        .mb-4 { margin-bottom: 16px; }
        .mb-6 { margin-bottom: 24px; }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-2 { gap: 8px; }
        .gap-3 { gap: 12px; }
        .candidate-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid var(--gris1);
        }
        .rank-medal { font-size: 18px; }
        .divider { border: none; border-top: 1px solid var(--gris1); margin: 20px 0; }
        .or-accent { color: var(--or); font-family: 'Cormorant Garamond', serif; }
    </style>
    @stack('styles')
</head>
<body>

{{-- SIDEBAR --}}
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="title">Gala Tabaski<br>Act 3</div>
        <div class="sub">Administration</div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Principal</div>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="icon">◈</span> Tableau de bord
        </a>

        <div class="nav-section" style="margin-top:12px">Gestion</div>

        <a href="{{ route('admin.candidates.index') }}"
           class="nav-item {{ request()->routeIs('admin.candidates.*') ? 'active' : '' }}">
            <span class="icon">♛</span> Candidates
        </a>

        <a href="{{ route('admin.votes.index') }}"
           class="nav-item {{ request()->routeIs('admin.votes.*') ? 'active' : '' }}">
            <span class="icon">◉</span> Votes
        </a>

        <a href="{{ route('admin.payments.index') }}"
           class="nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
            <span class="icon">◈</span> Paiements
        </a>

        <a href="{{ route('admin.packs.index') }}"
           class="nav-item {{ request()->routeIs('admin.packs.*') ? 'active' : '' }}">
            <span class="icon">◇</span> Packs de vote
        </a>

        <div class="nav-section" style="margin-top:12px">Configuration</div>

        <a href="{{ route('admin.results.index') }}"
           class="nav-item {{ request()->routeIs('admin.results.*') ? 'active' : '' }}">
            <span class="icon">★</span> Résultats
        </a>

        <a href="{{ route('admin.settings.index') }}"
           class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <span class="icon">⚙</span> Paramètres
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="admin-name">{{ Auth::guard('admin')->user()->name }}</div>
        <div class="admin-role">Administrateur</div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="logout-btn">⏻ Déconnexion</button>
        </form>
    </div>
</aside>

{{-- MAIN --}}
<div class="main">
    <div class="topbar">
        <div>
            <div class="page-title">@yield('page-title', 'Dashboard')</div>
            @hasSection('breadcrumb')
            <div class="breadcrumb">@yield('breadcrumb')</div>
            @endif
        </div>
        <div style="font-size:12px;color:var(--gris3);">
            {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
        </div>
    </div>

    <div class="content">
        @if(session('success'))
            <div class="alert alert-success">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">✕ {{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</div>

@stack('scripts')
</body>
</html>