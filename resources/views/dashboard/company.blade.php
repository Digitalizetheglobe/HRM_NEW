@extends('layouts.admin')

@php
    $setting = App\Models\Utility::settings();
@endphp

@section('content')
<style>
    /* =========================================================
       MODERN SAAS DASHBOARD STYLES (HRM)
       ========================================================= */
    :root {
        --dash-primary: #018ac8;
        --dash-primary-dark: #006b9d;
        --dash-primary-light: #e6f4fa;
        --dash-dark: #0f172a;
        --dash-slate: #334155;
        --dash-muted: #64748b;
        --dash-border: #edf2f7;
        --dash-bg: #f8fafc;
        --dash-card-bg: #ffffff;
        --dash-radius: 18px;
        --dash-radius-sm: 12px;
        --dash-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05), 0 2px 6px -2px rgba(15, 23, 42, 0.02);
        --dash-shadow-hover: 0 16px 32px -4px rgba(15, 23, 42, 0.1), 0 4px 12px -2px rgba(15, 23, 42, 0.05);
    }

    /* Overall Layout Polish */
    .dashboard-container {
        padding-bottom: 30px;
    }

    /* Modern Hero Greeting Header */
    .modern-welcome-hero {
        background: #ffffff;
        border: 1px solid var(--dash-border);
        border-radius: var(--dash-radius);
        padding: 22px 26px;
        margin-bottom: 24px;
        box-shadow: var(--dash-shadow);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        position: relative;
        overflow: hidden;
    }
    .modern-welcome-hero::after {
        content: "";
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 180px;
        background: radial-gradient(circle at top right, rgba(1, 138, 200, 0.08), transparent 70%);
        pointer-events: none;
    }
    .welcome-pill-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(1, 138, 200, 0.08);
        color: var(--dash-primary);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        padding: 4px 10px;
        border-radius: 20px;
        margin-bottom: 6px;
    }
    .welcome-title {
        font-size: 24px;
        font-weight: 800;
        color: var(--dash-dark);
        letter-spacing: -0.4px;
        margin: 0;
        line-height: 1.25;
    }
    .welcome-subtitle {
        font-size: 13px;
        color: var(--dash-muted);
        margin: 4px 0 0 0;
        font-weight: 500;
    }

    /* Filter Controls */
    .filter-controls-group {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
    }
    .live-date-pill {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 8px 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: var(--dash-radius-sm);
        font-size: 12px;
        font-weight: 600;
        color: var(--dash-slate);
    }
    .live-date-pill i {
        color: var(--dash-primary);
        font-size: 14px;
    }
    .btn-modern-filter {
        background: linear-gradient(135deg, #018ac8 0%, #026b9a 100%) !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        padding: 8px 16px !important;
        border-radius: var(--dash-radius-sm) !important;
        border: none !important;
        box-shadow: 0 4px 14px rgba(1, 138, 200, 0.28) !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 7px !important;
        transition: all 0.25s ease !important;
    }
    .btn-modern-filter:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(1, 138, 200, 0.38) !important;
    }
    .modern-dropdown-menu {
        border-radius: 14px !important;
        border: 1px solid #edf2f7 !important;
        box-shadow: 0 10px 25px -3px rgba(15, 23, 42, 0.1) !important;
        padding: 8px !important;
        min-width: 160px;
    }
    .modern-dropdown-menu .dropdown-item {
        border-radius: 8px !important;
        padding: 8px 12px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        color: var(--dash-slate) !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        transition: all 0.15s ease !important;
    }
    .modern-dropdown-menu .dropdown-item:hover {
        background: #f1f5f9 !important;
        color: var(--dash-dark) !important;
    }
    #customDatePicker {
        height: 38px !important;
        padding: 6px 12px !important;
        font-size: 13px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: var(--dash-radius-sm) !important;
        background-color: #ffffff !important;
        color: var(--dash-slate) !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }

    /* =========================================================
       STAT CARDS (KPIs)
       ========================================================= */
    .stat-card-col {
        margin-bottom: 20px;
    }
    .modern-stat-card {
        background: #ffffff;
        border: 1px solid var(--dash-border);
        border-radius: var(--dash-radius) !important;
        padding: 22px 22px;
        box-shadow: var(--dash-shadow);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .modern-stat-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .modern-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--dash-shadow-hover);
        border-color: #e2e8f0;
    }
    .modern-stat-card:hover::before {
        opacity: 1;
    }

    /* Themes for Cards */
    .card-theme-purple::before { background: linear-gradient(90deg, #9333ea, #c084fc); }
    .card-theme-blue::before { background: linear-gradient(90deg, #0284c7, #38bdf8); }
    .card-theme-green::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .card-theme-indigo::before { background: linear-gradient(90deg, #6366f1, #818cf8); }
    .card-theme-orange::before { background: linear-gradient(90deg, #f97316, #fb923c); }
    .card-theme-pink::before { background: linear-gradient(90deg, #ec4899, #f472b6); }

    .stat-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }
    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 22px;
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .modern-stat-card:hover .stat-icon-wrapper {
        transform: scale(1.08) rotate(3deg);
    }

    .icon-purple { background: linear-gradient(135deg, #a855f7 0%, #7e22ce 100%); box-shadow: 0 6px 16px rgba(168, 85, 247, 0.3); }
    .icon-blue   { background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); box-shadow: 0 6px 16px rgba(2, 132, 199, 0.3); }
    .icon-green  { background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3); }
    .icon-indigo { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); box-shadow: 0 6px 16px rgba(99, 102, 241, 0.3); }
    .icon-orange { background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); box-shadow: 0 6px 16px rgba(249, 115, 22, 0.3); }
    .icon-pink   { background: linear-gradient(135deg, #ec4899 0%, #be185d 100%); box-shadow: 0 6px 16px rgba(236, 72, 153, 0.3); }

    .stat-arrow-btn {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 13px;
        transition: all 0.25s ease;
    }
    .modern-stat-card:hover .stat-arrow-btn {
        background: var(--dash-dark);
        color: #ffffff;
        border-color: var(--dash-dark);
        transform: translate(2px, -2px);
    }

    .stat-card-mid {
        margin-bottom: 12px;
    }
    .stat-val-text {
        font-size: 32px;
        font-weight: 800;
        color: var(--dash-dark);
        line-height: 1.1;
        letter-spacing: -0.5px;
        margin: 0 0 4px 0;
    }
    .stat-label-text {
        font-size: 13px;
        font-weight: 600;
        color: var(--dash-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }
    .stat-card-bottom {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 600;
        color: #94a3b8;
        padding-top: 10px;
        border-top: 1px solid #f1f5f9;
    }

    /* =========================================================
       BOX CARDS & SECTION HEADERS
       ========================================================= */
    .modern-box-card {
        background: #ffffff;
        border: 1px solid var(--dash-border);
        border-radius: var(--dash-radius) !important;
        box-shadow: var(--dash-shadow);
        overflow: hidden;
        margin-bottom: 24px;
        transition: box-shadow 0.25s ease;
    }
    .modern-box-card:hover {
        box-shadow: var(--dash-shadow-hover);
    }
    .modern-box-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-radius: var(--dash-radius) var(--dash-radius) 0 0 !important;
    }
    .modern-box-header .header-left-title {
        font-size: 14px;
        font-weight: 700;
        color: #ffffff;
        letter-spacing: 0.2px;
        display: flex;
        align-items: center;
        gap: 9px;
        margin: 0;
    }
    .modern-box-header .header-left-title i {
        font-size: 18px;
        color: #38bdf8;
    }
    .modern-box-header .header-count-pill {
        background: rgba(255, 255, 255, 0.16);
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 30px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        line-height: 1.5;
    }
    .modern-box-header .btn-header-add {
        background: rgba(255, 255, 255, 0.16);
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.25);
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .modern-box-header .btn-header-add:hover {
        background: rgba(255, 255, 255, 0.28);
        border-color: rgba(255, 255, 255, 0.4);
    }

    /* Custom Modern Table */
    .modern-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    .modern-table thead th {
        background: #f8fafc;
        color: #64748b;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 12px 18px;
        border: none;
        border-bottom: 1px solid #edf2f7;
        white-space: nowrap;
    }
    .modern-table tbody td {
        padding: 13px 18px;
        font-size: 13px;
        color: var(--dash-dark);
        border: none;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-weight: 500;
    }
    .modern-table tbody tr {
        transition: background 0.15s ease;
    }
    .modern-table tbody tr:hover {
        background: #f8fafc;
    }
    .modern-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Status Pills */
    .pill-absent {
        background: #fef2f2;
        color: #ef4444;
        border: 1px solid #fecaca;
        font-weight: 600;
        font-size: 11px;
        padding: 3px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .pill-absent::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #ef4444;
    }
    .time-badge {
        font-weight: 600;
        color: #0f172a;
        background: #f1f5f9;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 12px;
        display: inline-block;
    }

    /* List Groups (Projects, Notices) */
    .modern-list-item {
        display: flex;
        align-items: center;
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        text-decoration: none !important;
        transition: all 0.2s ease;
        position: relative;
    }
    .modern-list-item:last-child {
        border-bottom: none;
    }
    .modern-list-item:hover {
        background: #f8fafc;
        padding-left: 22px;
    }
    .modern-list-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: var(--dash-primary);
        flex-shrink: 0;
        margin-right: 14px;
        transition: transform 0.2s ease;
    }
    .modern-list-item:hover .modern-list-icon {
        transform: scale(1.08);
        background: rgba(1, 138, 200, 0.1);
    }
    .modern-list-chevron {
        color: #cbd5e1;
        font-size: 16px;
        transition: transform 0.2s, color 0.2s;
        flex-shrink: 0;
    }
    .modern-list-item:hover .modern-list-chevron {
        color: var(--dash-primary);
        transform: translateX(3px);
    }

    /* Status Badges */
    .badge-modern-success {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
        padding: 3px 9px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-modern-warning {
        background: #fffbeb;
        color: #d97706;
        border: 1px solid #fde68a;
        padding: 3px 9px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-modern-primary {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
        padding: 3px 9px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    /* =========================================================
       RIGHT SIDEBAR (EVENTS & CALENDAR)
       ========================================================= */
    .events-card {
        border: none;
        border-radius: var(--dash-radius) !important;
        box-shadow: var(--dash-shadow) !important;
        overflow: hidden;
        background: #ffffff;
    }
    .events-scroll-body::-webkit-scrollbar { width: 4px; }
    .events-scroll-body::-webkit-scrollbar-track { background: #f8fafc; }
    .events-scroll-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

    .ev-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 16px;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        transition: background 0.15s ease;
    }
    .ev-row:last-child { border-bottom: none; }
    .ev-row:hover { background: #f8fafc; }
    .ev-row:hover .ev-icon { transform: scale(1.08); box-shadow: 0 5px 16px rgba(0,0,0,0.18) !important; }
    .ev-row:hover .ev-arrow { transform: translateX(3px); color: var(--dash-primary) !important; }

    .ev-icon {
        width: 38px;
        height: 38px;
        min-width: 38px;
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .ev-icon i { color: #fff; font-size: 17px; }

    .ev-info { flex: 1; overflow: hidden; min-width: 0; }
    .ev-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--dash-dark);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .ev-today-badge {
        font-size: 9px;
        font-weight: 700;
        background: linear-gradient(135deg, #f6d365, #fda085);
        color: #fff;
        padding: 1px 6px;
        border-radius: 20px;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .ev-meta { display: flex; align-items: center; gap: 7px; margin-top: 3px; }
    .ev-tag {
        font-size: 10px;
        font-weight: 600;
        padding: 1px 7px;
        border-radius: 20px;
        white-space: nowrap;
    }
    .ev-date { font-size: 10px; color: #94a3b8; white-space: nowrap; }
    .ev-arrow { color: #cbd5e1; font-size: 13px; flex-shrink: 0; transition: transform 0.18s, color 0.18s; }

    .ev-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 34px 16px;
        gap: 10px;
    }
    .ev-empty-icon {
        width: 52px; height: 52px;
        background: linear-gradient(135deg, #e0f2fe, #bae6fd);
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
    }
    .ev-empty-icon i { font-size: 26px; color: #0284c7; }
    .ev-empty p { color: #94a3b8; font-size: 12px; font-weight: 600; margin: 0; }

    .ev-avatar-wrap {
        position: relative;
        width: 40px;
        height: 40px;
        min-width: 40px;
        border-radius: 50%;
        padding: 2px;
        flex-shrink: 0;
        transition: transform 0.2s ease;
    }
    .ev-row:hover .ev-avatar-wrap { transform: scale(1.08); }
    .ev-avatar-img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #fff;
        display: block;
    }
    .ev-avatar-badge {
        position: absolute;
        bottom: -1px;
        right: -2px;
        width: 17px;
        height: 17px;
        border-radius: 50%;
        background: #fff;
        border: 1.5px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 1px 4px rgba(0,0,0,0.15);
    }
    .ev-avatar-badge i { font-size: 9px; color: #555; }

    /* Calendar Styling */
    .fc-prev-button, .fc-next-button {
        width: 32px !important;
        height: 32px !important;
        padding: 0 !important;
        font-size: 13px !important;
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 50% !important;
        color: var(--dash-slate) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04) !important;
        transition: all 0.2s ease !important;
    }
    .fc-prev-button:hover, .fc-next-button:hover {
        background: var(--dash-primary) !important;
        color: #ffffff !important;
        border-color: var(--dash-primary) !important;
        transform: scale(1.06);
    }
    .fc .fc-toolbar {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 14px 16px 10px !important;
        margin-bottom: 0.5em !important;
    }
    .fc .fc-toolbar-title {
        font-size: 14px !important;
        margin: 0 !important;
        font-weight: 800 !important;
        color: var(--dash-dark) !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
    }
    .fc .fc-col-header-cell {
        background: #f8fafc !important;
        padding: 6px 0 !important;
        border-color: #f1f5f9 !important;
    }
    .fc .fc-col-header-cell-cushion {
        font-size: 11px !important;
        font-weight: 700 !important;
        color: #64748b !important;
        text-transform: uppercase !important;
    }
    .fc-daygrid-day-number {
        font-size: 12px !important;
        font-weight: 600 !important;
        color: var(--dash-slate) !important;
        padding: 6px !important;
    }
    .fc-day-today {
        background: rgba(1, 138, 200, 0.06) !important;
    }
    .fc-day-today .fc-daygrid-day-number {
        background: var(--dash-primary) !important;
        color: #ffffff !important;
        border-radius: 50% !important;
        width: 24px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 2px;
    }

    /* Loading Overlay */
    .loading {
        position: relative;
        pointer-events: none;
        opacity: 0.7;
    }
    .loading:after {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255, 255, 255, 0.7) url('{{ asset("assets/img/loading.gif") }}') no-repeat center;
        background-size: 44px 44px;
        z-index: 1000;
        border-radius: var(--dash-radius);
    }

    /* Responsive */
    @media (max-width: 991px) {
        .modern-welcome-hero {
            flex-direction: column;
            align-items: flex-start;
            gap: 14px;
        }
        .filter-controls-group {
            width: 100%;
            justify-content: flex-start;
            flex-wrap: wrap;
        }
    }
    @media (max-width: 576px) {
        .stat-val-text { font-size: 26px; }
        .modern-welcome-hero { padding: 16px 18px; }
        .welcome-title { font-size: 20px; }
    }
</style>

<div class="dashboard-container">
    <div class="row">
        @if (session('status'))
            <div class="col-12">
                <div class="alert alert-success border-0 shadow-sm" style="border-radius: 12px;" role="alert">
                    <i class="ti ti-check me-2"></i> {{ session('status') }}
                </div>
            </div>
        @endif

        @if (Auth::user()->type == 'company' || Auth::user()->type == 'hr' || Auth::user()->type == 'Director')

        {{-- =========================================================
             MODERN HERO GREETING & FILTER BAR
             ========================================================= --}}
        <div class="col-12">
            <div class="modern-welcome-hero" id="dashboardHeader">
                <div>
                    <div class="welcome-pill-badge">
                        <i class="ti ti-shield-check"></i> HRM Company Dashboard
                    </div>
                    <h2 class="welcome-title" id="dashboardGreeting">
                        @if(Auth::user()->type == 'employee')
                            {{ __('Welcome') }}, {{ Auth::user()->name }} 👋
                        @else
                            {{ __('Welcome Admin') }} 👋
                        @endif
                    </h2>
                    <p class="welcome-subtitle">
                        Here is the live real-time pulse of your workforce, projects and operations today.
                    </p>
                </div>

                <div class="filter-controls-group">
                    <div class="live-date-pill d-none d-md-flex">
                        <i class="ti ti-calendar"></i>
                        <span>{{ date('l, d M Y') }}</span>
                    </div>

                    <div class="btn-group" style="z-index: 1;">
                        <button type="button" class="btn btn-modern-filter dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" id="dateFilterButton">
                            <i class="ti ti-filter"></i> <span>Today</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end modern-dropdown-menu">
                            <li><a class="dropdown-item" href="#" data-value="today"><i class="ti ti-calendar text-primary"></i> Today</a></li>
                            <li><a class="dropdown-item" href="#" data-value="yesterday"><i class="ti ti-history text-muted"></i> Yesterday</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li><a class="dropdown-item" href="#" data-value="custom"><i class="ti ti-calendar-plus text-info"></i> Select Date</a></li>
                        </ul>
                    </div>

                    <div class="position-relative" id="customDatePickerWrapper" style="display: none;">
                        <input type="date" class="form-control" id="customDatePicker">
                    </div>
                </div>
            </div>
        </div>

        {{-- =========================================================
             MAIN CONTENT: 9 COLS LEFT, 3 COLS RIGHT
             ========================================================= --}}
        <div class="col-xxl-9">
            <div class="row">
                <!-- 6 KPI Stat Cards -->
                <div class="col-12">
                    <div class="row">
                        <!-- 1. Total Employees -->
                        <div class="col-6 col-lg-4 col-md-4 stat-card-col">
                            <div class="modern-stat-card card-theme-purple" onclick="window.location.href='employee'">
                                <div class="stat-card-top">
                                    <div class="stat-icon-wrapper icon-purple">
                                        <i class="fa-solid fa-user-tie"></i>
                                    </div>
                                    <div class="stat-arrow-btn">
                                        <i class="ti ti-arrow-up-right"></i>
                                    </div>
                                </div>
                                <div class="stat-card-mid">
                                    <h3 class="stat-val-text">{{ $countEmployee }}</h3>
                                    <p class="stat-label-text">Total Employees</p>
                                </div>
                                <div class="stat-card-bottom">
                                    <i class="ti ti-circle-check text-success"></i>
                                    <span>Active company staff</span>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Total Department -->
                        <div class="col-6 col-lg-4 col-md-4 stat-card-col">
                            <div class="modern-stat-card card-theme-blue" onclick="window.location.href='{{ url('department') }}'">
                                <div class="stat-card-top">
                                    <div class="stat-icon-wrapper icon-blue">
                                        <i class="fa-solid fa-sitemap"></i>
                                    </div>
                                    <div class="stat-arrow-btn">
                                        <i class="ti ti-arrow-up-right"></i>
                                    </div>
                                </div>
                                <div class="stat-card-mid">
                                    <h3 class="stat-val-text">{{ $totalDepartment }}</h3>
                                    <p class="stat-label-text">Total Department</p>
                                </div>
                                <div class="stat-card-bottom">
                                    <i class="ti ti-building text-info"></i>
                                    <span>Organizational units</span>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Total Leaves -->
                        <div class="col-6 col-lg-4 col-md-4 stat-card-col">
                            <div class="modern-stat-card card-theme-green" onclick="window.location.href='leave'">
                                <div class="stat-card-top">
                                    <div class="stat-icon-wrapper icon-green">
                                        <i class="fa-solid fa-calendar-check"></i>
                                    </div>
                                    <div class="stat-arrow-btn">
                                        <i class="ti ti-arrow-up-right"></i>
                                    </div>
                                </div>
                                <div class="stat-card-mid">
                                    <h3 class="stat-val-text" id="totalLeavesCount">{{ $totalleaves }}</h3>
                                    <p class="stat-label-text">Total Leaves</p>
                                </div>
                                <div class="stat-card-bottom">
                                    <i class="ti ti-clock-check text-success"></i>
                                    <span>Applications recorded</span>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Total Holidays -->
                        <div class="col-6 col-lg-4 col-md-4 stat-card-col">
                            <div class="modern-stat-card card-theme-indigo" onclick="window.location.href='holiday'">
                                <div class="stat-card-top">
                                    <div class="stat-icon-wrapper icon-indigo">
                                        <i class="fa-solid fa-calendar-days"></i>
                                    </div>
                                    <div class="stat-arrow-btn">
                                        <i class="ti ti-arrow-up-right"></i>
                                    </div>
                                </div>
                                <div class="stat-card-mid">
                                    <h3 class="stat-val-text">{{ $totalHolidays }}</h3>
                                    <p class="stat-label-text">Total Holidays</p>
                                </div>
                                <div class="stat-card-bottom">
                                    <i class="ti ti-confetti text-primary"></i>
                                    <span>Scheduled year holidays</span>
                                </div>
                            </div>
                        </div>

                        <!-- 5. Total Projects -->
                        <div class="col-6 col-lg-4 col-md-4 stat-card-col">
                            <div class="modern-stat-card card-theme-orange" onclick="window.location.href='projects'">
                                <div class="stat-card-top">
                                    <div class="stat-icon-wrapper icon-orange">
                                        <i class="fa-solid fa-diagram-project"></i>
                                    </div>
                                    <div class="stat-arrow-btn">
                                        <i class="ti ti-arrow-up-right"></i>
                                    </div>
                                </div>
                                <div class="stat-card-mid">
                                    <h3 class="stat-val-text">{{ $totalProjects }}</h3>
                                    <p class="stat-label-text">Total Projects</p>
                                </div>
                                <div class="stat-card-bottom">
                                    <i class="ti ti-rocket text-warning"></i>
                                    <span>Pipeline & ongoing</span>
                                </div>
                            </div>
                        </div>

                        <!-- 6. Total Ticket -->
                        <div class="col-6 col-lg-4 col-md-4 stat-card-col">
                            <div class="modern-stat-card card-theme-pink" onclick="window.location.href='ticket'">
                                <div class="stat-card-top">
                                    <div class="stat-icon-wrapper icon-pink">
                                        <i class="fa-solid fa-ticket"></i>
                                    </div>
                                    <div class="stat-arrow-btn">
                                        <i class="ti ti-arrow-up-right"></i>
                                    </div>
                                </div>
                                <div class="stat-card-mid">
                                    <h3 class="stat-val-text">{{ $countTicket }}</h3>
                                    <p class="stat-label-text">Total Ticket</p>
                                </div>
                                <div class="stat-card-bottom">
                                    <i class="ti ti-headphones text-danger"></i>
                                    <span>Support & inquiries</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today's Attendance & Yet To Arrive -->
                <div class="col-12 col-md-8 mb-4">
                    <div class="modern-box-card">
                        <div class="modern-box-header">
                            <div class="header-left-title">
                                <i class="ti ti-clock-play"></i>
                                <span id="attendanceFilterTitle">{{ __("Today's Attendance") }}</span>
                            </div>
                            <span class="header-count-pill" id="attendanceCount">{{ count($presentEmployeesWithClockIn) }}</span>
                        </div>
                        <div class="card-body p-0" style="height: 310px; overflow-y: auto;">
                            <div class="table-responsive">
                                <table class="table modern-table" id="attendanceTable">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Employee Name') }}</th>
                                            <th>{{ __('Punch-In') }}</th>
                                            <th>{{ __('Punch-Out') }}</th>
                                            <th>{{ __('Total Working Hours') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($presentEmployeesWithClockIn as $data)
                                            @php
                                                $timeline = '--:--';
                                                if (!empty($data['clock_in']) && $data['clock_in'] !== '--:--') {
                                                    try {
                                                        $clockIn = \Carbon\Carbon::parse($data['clock_in']);
                                                        if (!empty($data['clock_out']) && $data['clock_out'] !== '--:--') {
                                                            $clockOut = \Carbon\Carbon::parse($data['clock_out']);
                                                            $diff = $clockIn->diff($clockOut);
                                                        } else {
                                                            $diff = $clockIn->diff(\Carbon\Carbon::now());
                                                        }
                                                        $timeline = $diff->h . 'h ' . $diff->i . 'm';
                                                    } catch (\Exception $e) {
                                                        $timeline = '--:--';
                                                    }
                                                }
                                            @endphp
                                            <tr>
                                                <td class="fw-bold">{{ $data['employee']->name ?? 'N/A' }}</td>
                                                <td><span class="time-badge text-success">{{ $data['clock_in'] ?? '--:--' }}</span></td>
                                                <td><span class="time-badge">{{ $data['clock_out'] ?? '--:--' }}</span></td>
                                                <td><span class="badge rounded-pill bg-light text-dark fw-bold px-3 py-1">{{ $timeline }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5 text-muted">
                                                    <i class="ti ti-calendar-off d-block fs-2 mb-2 opacity-50"></i>
                                                    {{ __('No attendance records found for today.') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Yet To Arrive Card -->
                <div class="col-12 col-md-4 mb-4">
                    <div class="modern-box-card">
                        <div class="modern-box-header">
                            <div class="header-left-title">
                                <i class="ti ti-user-x"></i>
                                <span id="notClockInFilterTitle">{{ __('Yet To Arrive') }}</span>
                            </div>
                            <span class="header-count-pill" id="notClockInCount">{{ count($notClockIns) }}</span>
                        </div>
                        <div class="card-body p-0" style="height: 310px; overflow-y: auto;">
                            <div class="table-responsive">
                                <table class="table modern-table" id="notClockInTable">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Employee Name') }}</th>
                                            <th>{{ __('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($notClockIns as $employee)
                                            <tr>
                                                <td class="fw-semibold">{{ $employee->full_name ?? 'N/A' }}</td>
                                                <td><span class="pill-absent">Absent</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center py-5 text-success">
                                                    <i class="ti ti-check-circle fs-2 d-block mb-2"></i>
                                                    All employees are present!
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Project Details -->
                <div class="col-12 mb-4">
                    <div class="modern-box-card">
                        <div class="modern-box-header">
                            <div class="header-left-title">
                                <i class="ti ti-briefcase"></i>
                                <span>{{ __('Project Details') }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="#" data-url="{{ route('projects.create') }}" data-ajax-popup="true" data-title="{{ __('Create Project') }}" data-size="lg" class="btn-header-add">
                                    <i class="ti ti-plus"></i> <span>{{ __('Add') }}</span>
                                </a>
                                <span class="header-count-pill">{{ count($projects) }}</span>
                            </div>
                        </div>
                        <div class="card-body p-0" style="height: 325px; overflow-y: auto;">
                            <div class="list-group list-group-flush">
                                @forelse ($projects as $project)
                                    @php
                                        if($project->status == 'completed') {
                                            $statusBadge = 'badge-modern-success';
                                            $statusText = 'Completed';
                                        } elseif($project->status == 'on_hold') {
                                            $statusBadge = 'badge-modern-warning';
                                            $statusText = 'On Hold';
                                        } else {
                                            $statusBadge = 'badge-modern-primary';
                                            $statusText = 'In Progress';
                                        }

                                        $assignedNames = [];
                                        if(is_array($project->assigned_data)) {
                                            foreach($project->assigned_data as $assignment) {
                                                foreach($assignment['employee_ids'] ?? [] as $employeeId) {
                                                    if(isset($employees[$employeeId])) {
                                                        $assignedNames[] = $employees[$employeeId]->user->name ?? __('Unknown');
                                                    }
                                                }
                                            }
                                        }
                                        $assignedText = count($assignedNames) > 0 ? implode(', ', $assignedNames) : __('Unassigned');
                                    @endphp
                                    <a href="{{ route('projects.show', $project->id) }}" class="modern-list-item">
                                        <div class="modern-list-icon">
                                            <i class="ti ti-folders"></i>
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden pe-3">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <h6 class="mb-0 text-dark fw-bold text-truncate" style="font-size: 14px;">{{ $project->project_name }}</h6>
                                                <span class="{{ $statusBadge }}">{{ $statusText }}</span>
                                            </div>
                                            <p class="text-muted mb-0 text-truncate" style="font-size: 12px;">
                                                <i class="ti ti-users me-1 text-primary"></i>{{ $assignedText }}
                                            </p>
                                        </div>
                                        <i class="ti ti-chevron-right modern-list-chevron"></i>
                                    </a>
                                @empty
                                    <div class="p-5 text-center text-muted d-flex flex-column align-items-center justify-content-center h-100">
                                        <i class="ti ti-folder-off mb-2" style="font-size: 40px; opacity: 0.4;"></i>
                                        <p class="mb-0 fw-semibold">{{ __('No projects found.') }}</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today's Schedules -->
                <div class="col-12 mb-4">
                    <div class="modern-box-card">
                        <div class="modern-box-header">
                            <div class="header-left-title">
                                <i class="ti ti-calendar-time"></i>
                                <span id="scheduleFilterTitle">{{ __('Today\'s Schedules') }}</span>
                            </div>
                            <button type="button" class="btn-header-add" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                                <i class="ti ti-plus"></i> <span>{{ __('Add') }}</span>
                            </button>
                        </div>
                        <div class="card-body p-3">
                            <div id="schedulesContainer">
                                @include('dashboard.schedules_list')
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notices -->
                <div class="col-12 mb-4">
                    <div class="modern-box-card">
                        <div class="modern-box-header">
                            <div class="header-left-title">
                                <i class="ti ti-bell"></i>
                                <span>{{ __('Notices') }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="header-count-pill">{{ count($notices) }}</span>
                                @if(\Auth::user()->type == 'company' || \Auth::user()->type == 'hr')
                                <a href="#" data-url="{{ route('notices.create') }}" data-ajax-popup="true" data-size="lg" data-title="{{ __('Create New Notice') }}" class="btn-header-add">
                                    <i class="ti ti-plus"></i> <span>{{ __('Add') }}</span>
                                </a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body p-0" style="height: 290px; overflow-y: auto;">
                            <div class="list-group list-group-flush">
                                @forelse($notices as $notice)
                                <a href="#" class="modern-list-item"
                                   data-url="{{ route('notices.show', $notice->id) }}" 
                                   data-ajax-popup="true" 
                                   data-size="lg" 
                                   data-title="{{ __('Notice Details') }}">
                                    <div class="modern-list-icon">
                                        <i class="ti ti-speakerphone"></i>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden pe-3">
                                        <h6 class="mb-1 text-dark fw-bold text-truncate" style="font-size: 14px;">{{ $notice->title }}</h6>
                                        <p class="text-muted mb-0 text-truncate" style="font-size: 12px;">{{ strip_tags($notice->description) }}</p>
                                    </div>
                                    <i class="ti ti-chevron-right modern-list-chevron"></i>
                                </a>
                                @empty
                                <div class="p-5 text-center text-muted d-flex flex-column align-items-center justify-content-center h-100">
                                    <i class="ti ti-bell-off mb-2" style="font-size: 40px; opacity: 0.4;"></i>
                                    <p class="mb-0 fw-semibold">{{ __('No recent notices.') }}</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- =========================================================
             RIGHT SIDEBAR: UPCOMING EVENTS & CALENDAR
             ========================================================= --}}
        <div class="col-xxl-3" style="z-index: 0;">
            <div class="d-flex flex-column gap-3 sticky-top" style="top: 20px;">
                <!-- Upcoming Events Card -->
                <div class="modern-box-card mb-0">
                    <div class="modern-box-header">
                        <div class="header-left-title">
                            <i class="ti ti-calendar-event"></i>
                            <span>{{ __("Upcoming Events") }}</span>
                        </div>
                        @if(count($allEvents) > 0)
                        <span class="header-count-pill">{{ count($allEvents) }}</span>
                        @endif
                    </div>
                    <div style="max-height: 260px; overflow-y: auto; overflow-x: hidden;" class="events-scroll-body">
                        @if(count($allEvents) > 0)
                            @foreach($allEvents as $event)
                                @php
                                    $isToday = $event['is_today'];
                                    $type = $event['type'];
                                    $empName = $event['employee']->name ?? 'Employee';
                                    $empAvatar = $event['employee']->user->avatar ?? 'avatar.png';
                                    $avatarPath = !empty($empAvatar) && $empAvatar != 'avatar.png'
                                        ? asset('storage/uploads/avatar/' . $empAvatar)
                                        : asset('storage/uploads/avatar/avatar.png');

                                    if ($type === 'birthday') {
                                        $iconGrad = 'linear-gradient(135deg, #f093fb, #f5576c)';
                                        $badgeBg  = '#fff0f3'; $badgeColor = '#e11d48'; $tagLabel = 'Birthday';
                                        $badgeIcon = 'ti-cake';
                                    } elseif ($type === 'work_anniversary') {
                                        $iconGrad = 'linear-gradient(135deg, #4facfe, #00f2fe)';
                                        $badgeBg  = '#f0fdf4'; $badgeColor = '#16a34a'; $tagLabel = $event['years_completed'].' Yr Anniversary';
                                        $badgeIcon = 'ti-award';
                                    } else {
                                        $iconGrad = 'linear-gradient(135deg, #43e97b, #38f9d7)';
                                        $badgeBg  = '#f0fdfa'; $badgeColor = '#0d9488'; $tagLabel = 'Event';
                                        $badgeIcon = 'ti-calendar';
                                    }
                                @endphp
                                <div class="ev-row"
                                     onclick="openEventModal('{{ $type }}', '{{ addslashes($empName) }}', '{{ $event['date'] }}', {{ $isToday ? 'true' : 'false' }}, '{{ $event['years_completed'] ?? 0 }}', '{{ $event['employee_id'] ?? '' }}')">
                                    <div class="ev-avatar-wrap" style="background: {{ $iconGrad }};">
                                        <img src="{{ $avatarPath }}" class="ev-avatar-img" alt="{{ $empName }}" onerror="this.src='{{ asset('storage/uploads/avatar/avatar.png') }}'">
                                        <div class="ev-avatar-badge"><i class="ti {{ $badgeIcon }}"></i></div>
                                    </div>
                                    <div class="ev-info">
                                        <div class="ev-title">
                                            <span>{{ $empName }}</span>
                                            @if($isToday)<span class="ev-today-badge">TODAY</span>@endif
                                        </div>
                                        <div class="ev-meta">
                                            <span class="ev-tag" style="background:{{ $badgeBg }};color:{{ $badgeColor }};">{{ $tagLabel }}</span>
                                            <span class="ev-date">{{ $event['date'] }}</span>
                                        </div>
                                    </div>
                                    <i class="ti ti-chevron-right ev-arrow"></i>
                                </div>
                            @endforeach
                        @else
                            <div class="ev-empty">
                                <div class="ev-empty-icon"><i class="ti ti-calendar-off"></i></div>
                                <p>No upcoming events this month</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Calendar Card -->
                <div class="modern-box-card mb-0">
                    <div class="modern-box-header">
                        <div class="header-left-title">
                            <i class="ti ti-calendar"></i>
                            <span>{{ __('Calendar') }}</span>
                            <input type="hidden" id="path_admin" value="{{ url('/') }}">
                        </div>
                        <div>
                            @if (isset($setting['is_enabled']) && $setting['is_enabled'] == 'on')
                                <select class="form-control" name="calender_type" id="calender_type"
                                    style="float: right; width: 1px;" onchange="get_data()">
                                    <option value="local_calender" selected="true">{{ __('Local Calendar') }}</option>
                                </select>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-2" style="background: #ffffff;">
                        <div id='calendar' class='calendar'></div>
                    </div>
                </div>
            </div>
        </div>

        @endif
    </div>
</div>

{{-- Modern Event Detail Popup Modal --}}
<div class="modal fade" id="eventDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px; width: 92%; margin: 1.75rem auto;">
        <div class="modal-content" style="border:none; border-radius:22px; overflow:hidden; box-shadow:0 25px 60px rgba(0,0,0,0.2);">
            {{-- Gradient Header --}}
            <div id="eventModalHeader" style="padding: 32px 28px 22px; text-align:center; position:relative;">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="position:absolute; top:14px; right:18px; opacity:0.6;"></button>
                <div id="eventModalEmoji" style="font-size:52px; line-height:1; margin-bottom:10px;"></div>
                <div id="eventModalBadge" style="display:inline-block; padding:4px 16px; border-radius:30px; font-size:12px; font-weight:700; letter-spacing:1px; margin-bottom:12px;"></div>
                <h4 id="eventModalGreeting" style="color:#fff; font-weight:800; font-size:22px; margin:0; line-height:1.3;"></h4>
            </div>
            {{-- Body --}}
            <div class="modal-body" style="padding: 24px 28px 28px; background:#fff;">
                <div style="display:flex; flex-direction:column; gap:14px;">
                    <div style="display:flex; align-items:center; gap:14px; padding:14px 16px; background:#f8fafc; border: 1px solid #edf2f7; border-radius:14px;">
                        <div style="width:42px; height:42px; border-radius:50%; background:var(--dash-primary); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="ti ti-user" style="color:#fff; font-size:18px;"></i>
                        </div>
                        <div>
                            <div style="font-size:11px; color:#888; font-weight:600; letter-spacing:0.5px; text-transform:uppercase;">Employee</div>
                            <div id="eventModalName" style="font-size:16px; font-weight:700; color:#222;"></div>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:14px; padding:14px 16px; background:#f8fafc; border: 1px solid #edf2f7; border-radius:14px;">
                        <div style="width:42px; height:42px; border-radius:50%; background:var(--dash-primary); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="ti ti-calendar" style="color:#fff; font-size:18px;"></i>
                        </div>
                        <div>
                            <div style="font-size:11px; color:#888; font-weight:600; letter-spacing:0.5px; text-transform:uppercase;">Date</div>
                            <div id="eventModalDate" style="font-size:16px; font-weight:700; color:#222;"></div>
                        </div>
                    </div>
                    <div id="eventModalYearsRow" style="display:none; align-items:center; gap:14px; padding:14px 16px; background:#f8fafc; border: 1px solid #edf2f7; border-radius:14px;">
                        <div style="width:42px; height:42px; border-radius:50%; background:var(--dash-primary); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="ti ti-award" style="color:#fff; font-size:18px;"></i>
                        </div>
                        <div>
                            <div style="font-size:11px; color:#888; font-weight:600; letter-spacing:0.5px; text-transform:uppercase;">Years Completed</div>
                            <div id="eventModalYears" style="font-size:16px; font-weight:700; color:#222;"></div>
                        </div>
                    </div>
                    
                    {{-- Action Buttons --}}
                    <div id="wishesActionContainer" style="display:none; gap:10px; margin-top:10px; flex-direction:column;">
                        <button type="button" class="btn w-100" id="btnSendAutoWishes" style="background:var(--dash-primary); color:#fff; border-radius:12px; padding:11px; font-weight:600; border:none;" onclick="sendWishes('auto')">
                            <i class="ti ti-send me-2"></i> Send Wishes
                        </button>
                        <button type="button" class="btn w-100 btn-outline-primary" id="btnOpenCustomWishes" style="border-radius:12px; padding:11px; font-weight:600;" onclick="openCustomWishes()">
                            <i class="ti ti-edit me-2"></i> Send Custom Wishes
                        </button>
                    </div>

                    {{-- Custom Message Form --}}
                    <div id="customWishesForm" style="display:none; margin-top:10px; flex-direction:column;">
                        <label style="font-size:12px; font-weight:600; color:#555; margin-bottom:6px;">Your Custom Message:</label>
                        <textarea id="customWishesMessage" class="form-control" rows="3" placeholder="Write your personalized wishes here..." style="border-radius:12px; resize:none; margin-bottom:12px; box-shadow:none; border:1px solid #cbd5e1;"></textarea>
                        <div style="display:flex; gap:10px;">
                            <button type="button" class="btn w-50 btn-light" style="border-radius:12px; font-weight:600;" onclick="closeCustomWishes()">Cancel</button>
                            <button type="button" class="btn w-50" id="btnSendCustomWishes" style="background:var(--dash-primary); color:#fff; border-radius:12px; font-weight:600; border:none;" onclick="sendWishes('custom')">
                                <i class="ti ti-send me-2"></i> Send
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Schedule -->
<div class="modal fade" id="addScheduleModal" tabindex="-1" aria-labelledby="addScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 18px; border:none; overflow:hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 16px 20px;">
                <h5 class="modal-title text-white d-flex align-items-center" id="addScheduleModalLabel" style="font-size: 15px; font-weight:700;">
                    <i class="ti ti-calendar-plus me-2 text-info"></i> {{ __('Add New Schedule') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('todolist.store') }}" method="POST" id="addScheduleForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="task" class="form-label fw-bold text-dark" style="font-size: 12px; text-transform:uppercase;">{{ __('Task Title') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="task" name="task" required placeholder="{{ __('Enter task title...') }}" style="border-radius: 10px; border-color: #cbd5e1;">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label fw-bold text-dark" style="font-size: 12px; text-transform:uppercase;">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="start_date" name="start_date" required style="border-radius: 10px; border-color: #cbd5e1;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label fw-bold text-dark" style="font-size: 12px; text-transform:uppercase;">{{ __('End Date') }} <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="end_date" name="end_date" required style="border-radius: 10px; border-color: #cbd5e1;">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label for="description" class="form-label fw-bold text-dark" style="font-size: 12px; text-transform:uppercase;">{{ __('Description') }}</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="{{ __('Enter task description (optional)...') }}" style="border-radius: 10px; border-color: #cbd5e1;"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top" style="border-color:#edf2f7 !important;">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" style="background:var(--dash-primary); border:none;">{{ __('Save Schedule') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script-page')
    <script src="{{ asset('assets/js/plugins/main.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>

    @if (Auth::user()->type == 'company' || Auth::user()->type == 'hr' || Auth::user()->type == 'Director')
    <script type="text/javascript">
        $(document).ready(function() {
            get_data();
        });

        function get_data() {
            var calender_type = $('#calender_type :selected').val();

            $('#calendar').removeClass('local_calender google_calender');
            if (!calender_type) {
                calender_type = 'local_calender';
            }
            $('#calendar').addClass(calender_type);

            $.ajax({
                url: '{{ route('event.get_event_data') }}',
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    'calender_type': calender_type
                },
                success: function(data) {
                    var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                        headerToolbar: {
                            left: 'prev', // Only navigation arrows
                            center: 'title',
                            right: 'next'
                        },
                        themeSystem: 'bootstrap',
                        slotDuration: '00:10:00',
                        allDaySlot: true,
                        navLinks: false,
                        droppable: true,
                        selectable: true,
                        selectMirror: true,
                        editable: true,
                        dayMaxEvents: true,
                        handleWindowResize: true,
                        height: '360px',
                        showNonCurrentDates: false,
                        events: data,
                    });
                    calendar.render();
                }
            });
        }
    </script>

    @else
        <script>
            $(document).ready(function() {
                get_data();
            });

            function get_data() {
                var calender_type = $('#calender_type :selected').val();

                $('#event_calendar').removeClass('local_calender');
                $('#event_calendar').removeClass('google_calender');
                if (calender_type == undefined) {
                    calender_type = 'local_calender';
                }
                $('#event_calendar').addClass(calender_type);

                $.ajax({
                    url: $("#path_admin").val() + "/event/get_event_data",
                    method: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'calender_type': calender_type
                    },
                    success: function(data) {
                        var etitle;
                        var etype;
                        var etypeclass;
                        var calendar = new FullCalendar.Calendar(document.getElementById('event_calendar'), {
                            headerToolbar: {
                                left: 'prev,next today',
                                center: 'title',
                                right: 'dayGridMonth,timeGridWeek,timeGridDay'
                            },
                            buttonText: {
                                timeGridDay: "{{ __('Day') }}",
                                timeGridWeek: "{{ __('Week') }}",
                                // dayGridMonth: "{{ __('Month') }}"
                            },
                            // slotLabelFormat: {
                            //     hour: '2-digit',
                            //     minute: '2-digit',
                            //     hour12: false,
                            // },
                            themeSystem: 'tailwind',
                            slotDuration: '00:10:00',
                            allDaySlot: true,
                            navLinks: true,
                            droppable: true,
                            selectable: true,
                            selectMirror: true,
                            editable: true,
                            dayMaxEvents: true,
                            handleWindowResize: true,
                            events: data,
                            height: '400px',
                            // timeFormat: 'H(:mm)',

                        });

                        calendar.render();
                    }
                });
            };
        </script>
    @endif

    @if (Auth::user()->type == 'company' || Auth::user()->type == 'hr' || Auth::user()->type == 'Director')
        <script>
            (function() {
                var totalEmployees = {{ $totalEmployees }};
                var presentEmployees = {{ count($presentEmployeesWithClockIn) }};
                var attendancePercentage = {{ round($attendancePercentage, 2) }};
                
                var options = {
                    series: [attendancePercentage],
                    chart: {
                        height: 380,
                        type: 'radialBar',
                        offsetY: -20,
                        sparkline: {
                            enabled: true
                        }
                    },
                    plotOptions: {
                        radialBar: {
                            startAngle: -90,
                            endAngle: 90,
                            track: {
                                background: "#eef5ff",
                                strokeWidth: '98%',
                                margin: 5,
                            
                            },
                            dataLabels: {
                                name: {
                                    show: true
                                },
                                value: {
                                    offsetY: -50,
                                    fontSize: '20px'
                                }
                            }
                        }
                    },
                    grid: {
                        padding: {
                            top: -10
                        }
                    },
                    colors: ["#68A288"],
                    labels: [''],
                    tooltip: {
                        enabled: true,
                        y: {
                            formatter: function(val) {
                                return `Out of ${totalEmployees} employees, ${presentEmployees} are present.`;
                            }
                        }
                    }
                };

                var chart = new ApexCharts(document.querySelector("#attendance-chart"), options);
                chart.render();
            })();
        </script>

        <style>
            .apexcharts-tooltip {
                background: #000 !important;
                color: #fff !important;
                border-radius: 8px;
                font-size: 14px;
            }
        </style>
    @endif

<!-- Add Schedule Modal -->
<div class="modal fade" id="addScheduleModal" tabindex="-1" aria-labelledby="addScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg, #0b0b39ff 0%, #393970ff 100%); border-bottom: none; padding: 20px 25px;">
                <h5 class="modal-title text-white d-flex align-items-center" id="addScheduleModalLabel">
                    <i class="ti ti-calendar-plus me-2" style="font-size: 22px;"></i>{{ __('Add Today\'s Task') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <form action="{{ route('todolist.store') }}" method="POST" id="addScheduleForm">
                    @csrf
                    <input type="hidden" name="is_completed" value="0">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label font-weight-bold" style="color: #495057; font-size: 13px; font-weight: 600;">{{ __('Task Title') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control shadow-sm" name="task" required placeholder="{{ __('Enter task title...') }}" style="border-radius: 8px; border: 1px solid #ced4da; padding: 10px 15px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold" style="color: #495057; font-size: 13px; font-weight: 600;">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control shadow-sm" name="start_date" required style="border-radius: 8px; border: 1px solid #ced4da; padding: 10px 15px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold" style="color: #495057; font-size: 13px; font-weight: 600;">{{ __('End Date') }} <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control shadow-sm" name="end_date" required style="border-radius: 8px; border: 1px solid #ced4da; padding: 10px 15px;">
                        </div>
                        <div class="col-12">
                            <label class="form-label font-weight-bold" style="color: #495057; font-size: 13px; font-weight: 600;">{{ __('Priority') }} <span class="text-danger">*</span></label>
                            <select class="form-select shadow-sm" name="priority" required style="border-radius: 8px; border: 1px solid #ced4da; padding: 10px 15px;">
                                <option value="" disabled selected>{{ __('Select Priority') }}</option>
                                <option value="high">{{ __('High') }}</option>
                                <option value="medium">{{ __('Medium') }}</option>
                                <option value="low">{{ __('Low') }}</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label font-weight-bold" style="color: #495057; font-size: 13px; font-weight: 600;">{{ __('Description') }} <span class="text-muted fw-normal">({{ __('Optional') }})</span></label>
                            <textarea class="form-control shadow-sm" name="description" rows="3" placeholder="{{ __('Enter task details here...') }}" style="border-radius: 8px; border: 1px solid #ced4da; padding: 12px 15px; resize: none;"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-white border-top px-4 py-3">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600; padding: 8px 20px;">{{ __('Cancel') }}</button>
                <button type="submit" form="addScheduleForm" class="btn btn-primary" style="border-radius: 8px; background: #0b0b39ff; border-color: #0b0b39ff; font-weight: 600; padding: 8px 20px; box-shadow: 0 4px 10px rgba(11, 11, 57, 0.2);">{{ __('Save Task') }}</button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('script-page')
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
    <script>
        (function() {
            var options = {
                chart: {
                    height: 265,
                    type: 'bar',
                    toolbar: {
                        show: false,
                    },
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '50%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 4,
                    curve: 'smooth'
                },
                series: {!! json_encode($chartData['data']) !!},
                xaxis: {
                    categories: {!! json_encode($chartData['labels']) !!},
                },
                colors: ['#b4d1c4', '#68a288'],
                fill: {
                    type: 'solid',
                },
                grid: {
                    strokeDashArray: 4,
                },
                legend: {
                    show: true,
                    position: 'top',
                    horizontalAlign: 'right',
                },
                markers: {
                    size: 4,
                    colors: ['#000', '#FF3A6E'],
                    opacity: 2.5,
                    strokeWidth: 4,
                    hover: {
                        size: 8,
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#income-expense-chart"), options);
            chart.render();
        })();
    </script>

    <script>
        $(document).ready(function() {

            // Initialize custom date picker with today's date
            const today = new Date().toISOString().split('T')[0];
            const $customPicker = $('#customDatePicker');
            $customPicker.val(today);

            // Trigger the date picker when the input is clicked
            $customPicker.on('click', function() {
                if (this.showPicker) {
                    try {
                        this.showPicker();
                    } catch (e) {
                        $(this).focus();
                    }
                } else {
                    $(this).focus();
                }
            });

            // Handle date filter dropdown selection
            $('#dateFilterButton').closest('.btn-group').find('.dropdown-item').on('click', function(e) {
                e.preventDefault();
                const filterType = $(this).data('value');
                
                if (!filterType) return;
                
                const filterText = $(this).text();
                
                if (filterType === 'custom') {
                    $('#customDatePickerWrapper').show();
                    $('#dashboardHeader').addClass('compact');
                    $customPicker.focus();
                    const selectedDate = $customPicker.val();
                    if (selectedDate) {
                        const dateObj = new Date(selectedDate);
                        const formattedDate = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                        $('#dateFilterButton').text(formattedDate);
                        loadDashboardData('custom', selectedDate);
                        if ($customPicker[0].showPicker) {
                            try { $customPicker[0].showPicker(); } catch(e) {}
                        }
                    } else {
                        $('#dateFilterButton').text(filterText);
                    }
                } else if (filterType === 'today' || filterType === 'yesterday') {
                    $('#dateFilterButton').text(filterText);
                    $('#customDatePickerWrapper').hide();
                    $('#dashboardHeader').removeClass('compact');
                    loadDashboardData(filterType);
                }
            });
            
            // Handle custom date selection
            $('#customDatePicker').on('change', function() {
                const selectedDate = $(this).val();
                if (selectedDate) {
                    const dateObj = new Date(selectedDate);
                    const formattedDate = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    $('#dateFilterButton').text(formattedDate);
                    loadDashboardData('custom', selectedDate);
                }
            });

            // ── Event Detail Popup ──
            $(document).on('click', '.event-popup-trigger', function() {
                const type     = $(this).data('type');
                const name     = $(this).data('name');
                const dateStr  = $(this).data('date');
                const years    = $(this).data('years');
                const title    = $(this).data('title');
                const id       = $(this).data('id');

                let formatted = dateStr;
                try {
                    const dateObj  = new Date(dateStr + 'T00:00:00');
                    formatted = dateObj.toLocaleDateString('en-US', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
                } catch(e) {}

                let emoji, greeting, bgGrad, badgeText, badgeBg;
            if (type === 'birthday') {
                emoji     = '🎂';
                greeting  = name ? 'Happy Birthday, ' + name + '!' : title;
                bgGrad    = 'linear-gradient(135deg, #e47477ff 0%, #ff9ee1ff 99%, #f8bee6ff 100%)';
                badgeText = '🎉 Birthday';
                badgeBg   = 'rgba(255,255,255,0.3)';
            } else if (type === 'anniversary') {
                emoji     = '🎉';
                greeting  = name ? 'Work Anniversary, ' + name + '!' : title;
                bgGrad    = 'linear-gradient(135deg, #836addff 0%, #e09ccdff 100%)';
                badgeText = '🎊 Work Anniversary';
                badgeBg   = 'rgba(255,255,255,0.3)';
            } else {
                emoji     = '📅';
                greeting  = title;
                bgGrad    = 'linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%)';
                badgeText = 'COMPANY EVENT';
                badgeBg   = 'rgba(255,255,255,0.3)';
            }

                $('#eventModalHeader').css('background', bgGrad);
                $('#eventModalEmoji').text(emoji);
                $('#eventModalBadge').text(badgeText).css({'background': badgeBg, 'color':'#fff', 'border':'1px solid rgba(255,255,255,0.4)'});
                $('#eventModalGreeting').text(greeting);
                $('#eventModalName').text(name || '—');
                $('#eventModalDate').text(formatted);

                if (type === 'anniversary' && years) {
                    $('#eventModalYears').text(years + ' Year' + (years > 1 ? 's' : ''));
                    $('#eventModalYearsRow').css('display','flex');
                } else {
                    $('#eventModalYearsRow').css('display','none');
                }

                const todayStr = new Date().toLocaleDateString('en-CA');
                if ((type === 'birthday' || type === 'anniversary') && dateStr === todayStr && id) {
                    $('#wishesActionContainer').css('display', 'flex');
                    window.currentEventData = {
                        employee_id: id,
                        event_type: type,
                        years: years,
                        event_date: dateStr
                    };
                } else {
                    $('#wishesActionContainer').css('display', 'none');
                }
                
                $('#customWishesForm').css('display', 'none');

                const modal = new bootstrap.Modal(document.getElementById('eventDetailModal'));
                modal.show();
            });

            // ── Wishes Functions ──
            window.openCustomWishes = function() {
                $('#wishesActionContainer').css('display', 'none');
                $('#customWishesForm').css('display', 'flex');
                $('#customWishesMessage').val('');
                $('#customWishesMessage').focus();
            };

            window.closeCustomWishes = function() {
                $('#customWishesForm').css('display', 'none');
                $('#wishesActionContainer').css('display', 'flex');
            };

            window.sendWishes = function(mode) {
                if (!window.currentEventData) return;
                
                let message = null;
                if (mode === 'custom') {
                    message = $('#customWishesMessage').val().trim();
                    if (!message) return;
                }

                const data = {
                    _token: '{{ csrf_token() }}',
                    employee_id: window.currentEventData.employee_id,
                    event_type: window.currentEventData.event_type,
                    years: window.currentEventData.years,
                    event_date: window.currentEventData.event_date,
                    custom_message: message
                };

                const btnId = mode === 'custom' ? '#btnSendCustomWishes' : '#btnSendAutoWishes';
                const originalHtml = $(btnId).html();
                $(btnId).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...').prop('disabled', true);

                $.ajax({
                    url: '{{ route("dashboard.sendWishes") }}',
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        window.location.href = window.location.href;
                    },
                    error: function(xhr) {
                        window.location.href = window.location.href;
                    }
                });
            };

            function loadDashboardData(filterType, customDate = null) {
                let url = '{{ route("dashboard.filter") }}';
                let data = {
                    _token: '{{ csrf_token() }}',
                    filter_type: filterType
                };
                
                if (filterType === 'custom' && customDate) {
                    data.custom_date = customDate;
                }
                
                $('.card-body').addClass('loading');
                
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            $('#todayEnquiryCount').text(response.todayEnquiryCount);
                            if (typeof response.totalLeaves !== 'undefined') {
                                $('#totalLeavesCount').text(response.totalLeaves);
                            }
                            $('#attendanceCount').text(response.presentEmployeesWithClockIn.length);
                            $('#notClockInCount').text(response.notClockIns.length);
                            updateTable('#attendanceTable tbody', response.presentEmployeesWithClockIn, 'attendance');
                            updateTable('#notClockInTable tbody', response.notClockIns, 'notClockIn');
                            
                            // Update Schedules HTML
                            if (response.schedulesHtml !== undefined) {
                                $('#schedulesContainer').html(response.schedulesHtml);
                            }
                            
                            // Update Titles
                            let scheduleTitle = "Today's Schedules";
                            let attendanceTitle = "Today's Attendance";
                            let notClockInTitle = "Yet To Arrive";
                            
                            if (filterType === 'yesterday') {
                                scheduleTitle = 'Yesterday Schedules';
                                attendanceTitle = 'Yesterday Attendance';
                                notClockInTitle = 'Yet To Arrive Yesterday';
                            } else if (filterType === 'custom' && customDate) {
                                scheduleTitle = customDate + ' Schedules';
                                attendanceTitle = customDate + ' Attendance';
                                notClockInTitle = 'Yet To Arrive ' + customDate;
                            }
                            
                            $('#scheduleFilterTitle').text(scheduleTitle);
                            $('#attendanceFilterTitle').text(attendanceTitle);
                            $('#notClockInFilterTitle').text(notClockInTitle);
                        }
                    },
                    error: function(xhr) {
                        console.error('Dashboard filter error:', xhr);
                    },
                    complete: function() {
                        $('.card-body').removeClass('loading');
                    }
                });
            }

            function updateTable(tableSelector, data, tableType) {
                const $table = $(tableSelector);
                $table.empty();
                
                if (data.length === 0) {
                    let noDataText = 'No data available';
                    let colspan = 3;
                    if (tableType === 'attendance') {
                        noDataText = 'No attendance records found.';
                        colspan = 4;
                    } else if (tableType === 'notClockIn') {
                        noDataText = 'All employees are present';
                        colspan = 2;
                    }
                    $table.append('<tr><td colspan="' + colspan + '">' + noDataText + '</td></tr>');
                    return;
                }
                
                data.forEach(function(item) {
                    let row = '';
                    if (tableType === 'attendance') {
                        row = `<tr>
                            <td>${item.employee ? item.employee.name : 'N/A'}</td>
                            <td>${item.clock_in || '--:--'}</td>
                            <td>${item.clock_out || '--:--'}</td>
                            <td>${item.total_working_hours || '--:--'}</td>
                        </tr>`;
                    } else if (tableType === 'notClockIn') {
                        row = `<tr>
                            <td>${item.name || 'N/A'}</td>
                            <td style="color: red;">Absent</td>
                        </tr>`;
                    }
                    $table.append(row);
                });
            }

            function formatLocationWithTimestamp(location, lat, lng, capturedAt, punchTime) {
                if (!location || location === 'Location not captured yet' || location === 'Location not available') {
                    return '<div>Location pending...</div><small style="font-size: 10px; color: #6c757d;">Location being captured in background</small>';
                }
                
                let locationHtml = '<div>';
                if (lat && lng) {
                    locationHtml += '<a href="https://www.google.com/maps?q='+lat+','+lng+'" target="_blank" title="View on map">';
                    locationHtml += (location.length > 50 ? location.substring(0, 50) + '...' : location);
                    locationHtml += '</a>';
                } else {
                    locationHtml += location;
                }
                locationHtml += '</div>';
                
                if (capturedAt && punchTime) {
                    try {
                        const captured = new Date(capturedAt);
                        const punch = new Date('2000-01-01 ' + punchTime);
                        const delay = Math.floor((captured - punch) / 1000);
                        
                        const capturedTime = captured.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                        locationHtml += '<small style="font-size: 10px; color: #6c757d;">Captured: ' + capturedTime;
                        
                        if (delay > 30) {
                            const delayText = delay > 60 ? Math.round(delay / 60) + ' min later' : delay + ' sec later';
                            locationHtml += ' <span style="color: #ff9800;">(' + delayText + ')</span>';
                        }
                        locationHtml += '</small>';
                    } catch (e) {}
                }
                return locationHtml;
            }

            function formatLocation(location, lat, lng) {
                if (!location || location === 'Location not available' || location === 'Location not captured yet') {
                    if (lat && lng) {
                        return '<a href="https://www.google.com/maps?q='+lat+','+lng+'" target="_blank">View on Map</a>';
                    }
                    return 'Location pending...';
                }
                const truncated = location.length > 50 ? location.substring(0, 50) + '...' : location;
                return '<a href="https://www.google.com/maps?q='+lat+','+lng+'" target="_blank">'+truncated+'</a>';
            }

            function formatDateRange(start, end) {
                try {
                    const startDate = new Date(start);
                    const endDate = new Date(end);
                    return `${startDate.toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' })} - ${endDate.toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' })}`;
                } catch (e) {
                    console.error('Error formatting date:', e);
                    return '--';
                }
            }
        });
    </script>
@endpush
