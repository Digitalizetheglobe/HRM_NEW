@php
    $logo = \App\Models\Utility::get_file('uploads/logo/');
    $company_favicon = \App\Models\Utility::getValByName('company_favicon');
    $company_logo = \App\Models\Utility::GetLogo();
    $SITE_RTL = \App\Models\Utility::getValByName('SITE_RTL');
    $setting = \App\Models\Utility::colorset();
    $color = !empty($setting['theme_color']) ? $setting['theme_color'] : 'theme-3';
    $pusher_setting = \App\Models\Utility::settings();
    $getseo = App\Models\Utility::getSeoSetting();
    $metatitle = isset($getseo['meta_title']) ? $getseo['meta_title'] : '';
    $metadesc = isset($getseo['meta_description']) ? $getseo['meta_description'] : '';
    $meta_image = \App\Models\Utility::get_file('uploads/meta/');
    $meta_logo = isset($getseo['meta_image']) ? $getseo['meta_image'] : '';
    $enable_cookie = \App\Models\Utility::getCookieSetting('enable_cookie');

    if (isset($setting['color_flag']) && $setting['color_flag'] == 'true') {
        $themeColor = 'custom-color';
    } else {
        $themeColor = $color;
    }
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $SITE_RTL == 'on' ? 'rtl' : '' }}">

<head>

    <title>
        {{ \App\Models\Utility::getValByName('title_text') ? \App\Models\Utility::getValByName('title_text') : config('app.name', 'HRMGo SaaS') }}
        - @yield('page-title')</title>

    <!-- SEO META -->
    <meta name="title" content="{{ $metatitle }}">
    <meta name="description" content="{{ $metadesc }}">
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:title" content="{{ $metatitle }}">
    <meta property="og:description" content="{{ $metadesc }}">
    <meta property="og:image"
        content="{{ isset($meta_logo) && !empty(asset('storage/uploads/meta/' . $meta_logo)) ? asset('storage/uploads/meta/' . $meta_logo) : 'hrmgo.png' }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta property="twitter:title" content="{{ $metatitle }}">
    <meta property="twitter:description" content="{{ $metadesc }}">
    <meta property="twitter:image"
        content="{{ isset($meta_logo) && !empty(asset('storage/uploads/meta/' . $meta_logo)) ? asset('storage/uploads/meta/' . $meta_logo) : 'hrmgo.png' }}">


    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Dashboard Template Description" />
    <meta name="keywords" content="Dashboard Template" />
    <meta name="author" content="Rajodiya Infotech" />


    <!-- Favicon icon -->
    <link rel="icon"
        href="{{ $logo . '/' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon . '?' . time() : 'favicon.png' . '?' . time()) }}"
        type="image/x-icon" />
    <!-- for calender-->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/main.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/plugins/datepicker-bs5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/style.css') }}">
    <!-- font css -->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/bootstrap-switch-button.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">



    <!-- Datepicker CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">

    <!-- Mobile Date Input Fix -->
    <style>
        /* Ensure native date inputs always display properly on all screen sizes */
        .form-control[type="date"],
        .form-control[type="datetime-local"],
        .form-control[type="month"],
        .form-control[type="time"] {
            min-height: 30px;
            visibility: visible !important;
            opacity: 1 !important;
            position: relative !important;
        }

        /* Force calendar icon to show or add custom one */
        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            padding: 5px;
            opacity: 0; /* Hide native icon but keep it clickable over the custom background */
            position: absolute;
            right: 0;
            top: 0;
            width: 35px;
            height: 100%;
            z-index: 2;
        }

        /* Add a custom calendar icon as background so it's always visible */
        .form-control[type="date"] {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%236c757d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'%3E%3C/rect%3E%3Cline x1='16' y1='2' x2='16' y2='6'%3E%3C/line%3E%3Cline x1='8' y1='2' x2='8' y2='6'%3E%3C/line%3E%3Cline x1='3' y1='10' x2='21' y2='10'%3E%3C/line%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 18px;
            padding-right: 35px !important;
        }

        @media (max-width: 768px) {
            .form-control[type="date"] {
                font-size: 16px !important; /* Prevents auto-zoom on iOS */
            }
        }
    </style>

<!-- jQuery and jQuery UI -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>



    <!-- vendor css -->

    <link rel="stylesheet" href="{{ asset('assets/css/customizer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">


    @if ($SITE_RTL == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}">
    @endif

    @if ($setting['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
    @endif

    <meta name="url" content="{{ url('') . '/' . config('chatify.routes.prefix') }}"
        data-user="{{ Auth::user()->id }}">

    <link rel='stylesheet' href='https://unpkg.com/nprogress@0.2.0/nprogress.css' />

    @if ($setting['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/custom-dark.css') }}">
    @endif

    <style>
        :root {
            --color-customColor: <?=$color ?>;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/custom-color.css') }}">

    <style>
        /* Global Top Spacing Fix for Mobile */
        @media (max-width: 1024px) {
            .dash-header {
                min-height: 5px !important;
                height: auto !important; /* Allow it to grow if needed but start small */
            }
            .dash-header .header-wrapper {
                min-height: 0px !important;
            }
            .dash-container {
                top: 10px !important; /* Content starts at top */
            }
            .dash-container .dash-content {
                padding-top: 2px !important;
            }
            .page-header {
                padding-top: 0px !important;
                padding-bottom: 0px !important;
                margin-top: 0px !important;
                margin-bottom: 2px !important;
                padding-right: 2px !important;
                padding-left: 2px !important;
            }
            .page-header .row {
                flex-wrap: wrap;
                row-gap: 8px;
            }
            .page-header .col-auto {
                width: auto;
            }
            /* Tighten marquee on mobile */
            .quote-container marquee {
                font-size: 14px !important;
                margin-top: 2px !important;
            }
            .dash-header .header-avtar {
                width: 32px !important;
                height: 32px !important;
            }
            .action-btn-container {
                display: flex;
                align-items: center;
                gap: 5px;
            }
            .dash-sidebar {
                overflow: hidden !important;
            }
            .dash-sidebar .navbar-wrapper {
                height: 100% !important;
                display: flex !important;
                flex-direction: column !important;
                overflow: hidden !important;
            }
            .dash-sidebar .m-header {
                flex-shrink: 0 !important;
                z-index: 10 !important;
            }
            .dash-sidebar .navbar-content {
                flex: 1 1 auto !important;
                min-height: 0 !important;
                overflow-y: auto !important;
                overflow-x: hidden !important;
                -webkit-overflow-scrolling: touch !important;
                /* Remove any SimpleBar wrappers that may still exist from desktop */
                overscroll-behavior: contain !important;
            }
            /* Disable SimpleBar decorations on mobile */
            .dash-sidebar .simplebar-track,
            .ps__rail-x, .ps__rail-y {
                display: none !important;
            }
            /* Give the last items some breathing room */
            .dash-navbar {
                padding-bottom: 60px !important;
            }
        }

        /* ====== Global Premium Back Button ====== */
        .btn-back {
            display: inline-flex;
            align-items: center;
            background: #ffffff;
            border: 1px solid #e0e6ed;
            color: #51545d;
            padding: 6px 6px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            margin-bottom: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        .btn-back:hover {
            background: #f8f9fd;
            border-color: #d1d9e6;
            color: #000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transform: translateX(-4px);
        }

        .btn-back i {
            font-size: 18px;
            transition: transform 0.3s ease;
        }

        .btn-back:hover i {
            transform: translateX(-2px);
        }

        /* ====== Global Table Card Padding Cleanup ====== */
        /* Remove extra padding from card-body when it contains a table */
        body:not(.is-dashboard) .card > .card-body:has(.table),
        body:not(.is-dashboard) .card > .card-body:has(.table-responsive),
        body:not(.is-dashboard) .card > .card-body:has(.table-border-style),
        body:not(.is-dashboard) .card > .card-body:has(.dataTable-wrapper),
        body:not(.is-dashboard) .card > .card-body:has(.datatable-wrapper) {
            padding: 10 !important;
        }

        body:not(.is-dashboard) .card > .card-body:has(.table-border-style-tab) {
            padding: 0 !important;
        }



        /* Ensure tab navs inside zero-padded cards still have left padding */
        body:not(.is-dashboard) .card > .card-body:has(.table) > .nav-tabs,
        body:not(.is-dashboard) .card > .card-body:has(.table) > ul.nav {
            padding: 15px 15px 0 15px !important;
        }

        /* Tab content inside zero-padded cards */
        body:not(.is-dashboard) .card > .card-body:has(.table) > .tab-content {
            padding: 10 !important;
        }

        /* Also handle card-header card-body combo (attendance page pattern) */
        body:not(.is-dashboard) .card-header.card-body.table-border-style {
            padding: 10 !important;
        }

        /* Sticky DataTable Controls */
        /* Global DataTable Controls Styling */
        .dataTable-wrapper, .datatable-wrapper {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .dataTable-top,
        .dataTable-bottom,
        .datatable-top,
        .datatable-bottom {
            background: #fff;
            padding: 15px 15px !important;
            margin: 0 !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            flex-wrap: wrap;
            gap: 15px !important;
            width: 100% !important;
            position: relative;
            border-radius: 8px;
        }

        .dataTable-top, .datatable-top {
            margin-bottom: 5px !important;
        }

        .dataTable-dropdown, .datatable-dropdown {
            margin-right: auto !important;
            display: flex;
            align-items: center;
        }

        /* Hide "entries per page" text — keep only the select dropdown */
        .dataTable-dropdown label,
        .datatable-dropdown label {
            font-size: 0 !important;
        }
        .dataTable-dropdown label .dataTable-selector,
        .datatable-dropdown label .datatable-selector {
            font-size: 0.875rem !important;
        }

        /* jQuery DataTables: hide "Show X entries" text, keep select */
        .dataTables_length label {
            font-size: 0 !important;
        }
        .dataTables_length label select {
            font-size: 0.875rem !important;
        }

        .dataTable-search, .datatable-search {
            margin-left: auto !important;
            display: flex;
            align-items: center;
        }

        /* Hide "Showing X to Y of Z entries" info text */
        .dataTable-info, .datatable-info,
        .dataTables_info {
            display: none !important;
        }

        .dataTable-pagination, .datatable-pagination {
            margin-left: auto !important;
        }

        .dataTable-pagination ul, .datatable-pagination ul {
            margin: 0 !important;
            display: flex !important;
            list-style: none !important;
            padding: 0 !important;
            gap: 4px !important;
        }

        .dataTable-pagination li, .datatable-pagination li {
            list-style: none;
        }

        .dataTable-pagination li a, .datatable-pagination li a {
            padding: 6px 12px !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 6px !important;
            color: #64748b !important;
            background: #fff !important;
            text-decoration: none !important;
            font-size: 13px !important;
            transition: all 0.2s ease;
            display: block;
        }

        .dataTable-pagination li.active a, .datatable-pagination li.active a {
            background: var(--color-customColor) !important;
            color: #fff !important;
            border-color: var(--color-customColor) !important;
        }

        .dataTable-pagination li:not(.active):hover a, .datatable-pagination li:not(.active):hover a {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
        }

        /* Sticky DataTable Controls for Mobile Horizontal Scrolling */
        @media (max-width: 768px) {
            .dataTable-top,
            .dataTable-bottom,
            .datatable-top,
            .datatable-bottom {
                position: sticky;
                left: 0;
                z-index: 10;
            }
        }

        [data-theme="dark"] .dataTable-top,
        [data-theme="dark"] .dataTable-bottom,
        [data-theme="dark"] .datatable-top,
        [data-theme="dark"] .datatable-bottom {
            background: #1b2330;
        }

        .dataTable-container, .datatable-container {
            overflow-x: auto;
            width: 100%;
            display: block;
            border-radius: 8px;
        }

        .dataTable-input, .datatable-input {
            margin-bottom: 0 !important;
            padding: 0.5rem 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            width: 100%;
        }

        .dataTable-selector, .datatable-selector {
            padding: 0.5rem 2.5rem 0.5rem 0.75rem !important;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 14px;
            background-color: #fff;
            min-width: 80px;
            cursor: pointer;
        }

        .dataTable-dropdown label, .datatable-dropdown label {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 0;
            font-weight: 400;
        }

        @media (max-width: 575px) {
            .dataTable-top, .datatable-top {
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                align-items: center !important;
                padding: 10px 12px !important;
                gap: 8px !important;
            }
            .dataTable-bottom, .datatable-bottom {
                flex-direction: row !important;
                flex-wrap: wrap !important;
                align-items: center !important;
                padding: 10px 12px !important;
                gap: 8px !important;
                justify-content: center !important;
            }
            .dataTable-dropdown, .datatable-dropdown {
                flex: 0 0 auto !important;
                width: auto !important;
                margin: 0 !important;
            }
            .dataTable-selector, .datatable-selector {
                min-width: 60px !important;
                width: 60px !important;
            }
            .dataTable-search, .datatable-search {
                flex: 1 1 auto !important;
                width: auto !important;
                margin: 0 !important;
            }
            .dataTable-input, .datatable-input {
                width: 100% !important;
            }
            .dataTable-pagination, .datatable-pagination {
                width: 100% !important;
                margin: 0 !important;
                text-align: center;
                justify-content: center;
                display: flex;
            }
            /* jQuery DataTables mobile same-line */
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_length {
                float: none !important;
                display: inline-flex !important;
                align-items: center !important;
            }
            .dataTables_wrapper .dataTables_filter input {
                width: 100% !important;
            }
        }


        /* Global Datatable Sorting Icons Fix */
        .dataTable-sorter, .datatable-sorter {
            position: relative;
            display: inline-block;
            vertical-align: middle;
            padding-right: 20px !important;
            width: auto !important;
        }

        .dataTable-sorter::before,
        .dataTable-sorter::after,
        .datatable-sorter::before,
        .datatable-sorter::after {
            position: absolute !important;
            right: 0 !important;
            left: auto !important;
        }

        .dataTable-table th, .datatable-table th {
            white-space: nowrap;
        }
    </style>
    @stack('css-page')
</head>



<body class="{{ $themeColor }} {{ (Request::segment(1) == 'dashboard' || Request::segment(1) == '') ? 'is-dashboard' : '' }}">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    <!-- [ navigation menu ] start -->
    @include('partial.Admin.menu')
    <!-- [ navigation menu ] end -->
    <!-- [ Header ] start -->

    @include('partial.Admin.header')

    <!-- Modal -->
    <div class="modal notification-modal fade" id="notification-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="btn-close float-end" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                    <h6 class="mt-2">
                        <i data-feather="monitor" class="me-2"></i>Desktop settings
                    </h6>
                    <hr />
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting1" checked />
                        <label class="form-check-label f-w-600 pl-1" for="pcsetting1">Allow desktop
                            notification</label>
                    </div>
                    <p class="text-muted ms-5">
                        you get lettest content at a time when data will updated
                    </p>
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting2" />
                        <label class="form-check-label f-w-600 pl-1" for="pcsetting2">Store Cookie</label>
                    </div>
                    <h6 class="mb-0 mt-5">
                        <i data-feather="save" class="me-2"></i>Application settings
                    </h6>
                    <hr />
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting3" />
                        <label class="form-check-label f-w-600 pl-1" for="pcsetting3">Backup Storage</label>
                    </div>
                    <p class="text-muted mb-4 ms-5">
                        Automaticaly take backup as par schedule
                    </p>
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting4" />
                        <label class="form-check-label f-w-600 pl-1" for="pcsetting4">Allow guest to print
                            file</label>
                    </div>
                    <h6 class="mb-0 mt-5">
                        <i data-feather="cpu" class="me-2"></i>System settings
                    </h6>
                    <hr />
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting5" checked />
                        <label class="form-check-label f-w-600 pl-1" for="pcsetting5">View other user chat</label>
                    </div>
                    <p class="text-muted ms-5">Allow to show public user message</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-danger btn-sm" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-light-primary btn-sm">
                        Save changes
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Header ] end -->


    <!-- [ Main Content ] start -->
    <section class="dash-container">
        <div class="dash-content">
            <!-- [ breadcrumb ] start -->
            @php
                $hasTitle = trim($__env->yieldContent('page-title'));
                $hasBreadcrumb = trim($__env->yieldContent('breadcrumb'));
                $hasAction = trim($__env->yieldContent('action-button'));
            @endphp
            @if($hasTitle || $hasBreadcrumb || $hasAction)
            <div class="page-header">
                <div class="page-block">
                    <!-- Control Row: Back Button & Action Buttons -->
                    <div class="row align-items-center justify-content-between mb-3">
                        <div class="col-auto d-flex gap-2">
                            @if(Request::route() && Request::route()->getName() != 'dashboard')
                                <a href="javascript:history.back()" class="btn-back" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Back') }}">
                                    <i class="ti ti-arrow-narrow-left"></i>
                                </a>
                                <a href="javascript:window.location.reload(true)" class="btn-back" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Refresh') }}">
                                    <i class="ti ti-refresh"></i>
                                </a>
                            @endif
                        </div>
                        <div class="col-auto">
                            <div class="action-btn-container gap-2 d-flex align-items-center"
                                @if ($SITE_RTL == 'on') style="flex-direction: row-reverse;" @endif>
                                @yield('action-button')
                            </div>
                        </div>
                    </div>

                    <!-- Title & Breadcrumb Row -->
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto">
                            <div class="page-header-title">
                                <h4 class="m-b-10">
                                    @yield('page-title')
                                </h4>
                            </div>
                            <ul class="breadcrumb">
                                @yield('breadcrumb')
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->
            <!-- [ basic-table ] start -->
            @yield('content')
            <!-- [ basic-table ] end -->
            <!-- [ Main Content ] end -->
        </div>
    </section>
    <div class="modal fade" id="commonModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="body">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="commonModalOver" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>

    <div class="position-fixed top-0 end-0 p-3" style="z-index: 99999">
        <div id="liveToast" class="toast text-white  fade" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
    <footer class="dash-footer">
        <div class="footer-wrapper">
            <div class="py-1">
                <span class="text-muted">
                    @if (empty(App\Models\Utility::getValByName('footer_text')))
                        &copy;{{ date(' Y') }}
                    @endif
                    {{ App\Models\Utility::getValByName('footer_text') ? App\Models\Utility::getValByName('footer_text') : config('app.name', 'HRMGo SaaS') }}
                    | Powered By Connect360

                    {{-- {{ \App\Models\Utility::getValByName('footer_text') ? \App\Models\Utility::getValByName('footer_text') : '©Copyright HRMGo SaaS' . date(' Y') }} --}}

                </span>
            </div>

        </div>
    </footer>
    <!-- Warning Section start -->
    <!-- Older IE warning message -->
    <!--[if lt IE 11]>
 
<![endif]-->
    <!-- Warning Section Ends -->
    <!-- Required Js -->
    <script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/jquery.form.js') }}"></script>

    <script src="{{ asset('js/letter.avatar.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/datepicker-full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap-switch-button.min.js') }}"></script>
    <script src="{{ asset('assets/js/dash.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/simple-datatables.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/flatpickr.min.js') }}"></script>

    <script src="{{ asset('js/custom.js') }}"></script>

    <script src="{{ asset('js/chatify/autosize.js') }}"></script>
    <script src='https://unpkg.com/nprogress@0.2.0/nprogress.js'></script>


    {{-- <script>
        if($("#pc-dt-simple").lenght > 0) {
            const dataTable = new simpleDatatables.DataTable("#pc-dt-simple");
        }
    </script> --}}

    <script>
        // Initialize simple-datatables for the default tables if present.
        if (document.querySelector("#pc-dt-simple")) {
            const table = document.querySelector("#pc-dt-simple");
            const sortCol = table.hasAttribute('data-default-sort-col') ? parseInt(table.getAttribute('data-default-sort-col')) : null;
            const sortOrder = table.hasAttribute('data-default-sort-order') ? table.getAttribute('data-default-sort-order') : 'asc';
            const options = {};
            if (sortCol !== null) {
                options.columns = [
                    { select: sortCol, sort: sortOrder }
                ];
            }
            window.pcDtSimple = new simpleDatatables.DataTable("#pc-dt-simple", options);
        }
        if (document.querySelector("#pc-dt-simple2")) {
            const table = document.querySelector("#pc-dt-simple2");
            const sortCol = table.hasAttribute('data-default-sort-col') ? parseInt(table.getAttribute('data-default-sort-col')) : null;
            const sortOrder = table.hasAttribute('data-default-sort-order') ? table.getAttribute('data-default-sort-order') : 'asc';
            const options = {};
            if (sortCol !== null) {
                options.columns = [
                    { select: sortCol, sort: sortOrder }
                ];
            }
            window.pcDtSimple2 = new simpleDatatables.DataTable("#pc-dt-simple2", options);
        }
    </script>

    <script>
    // ====== UNIVERSAL Compact Pagination: Show 1, 2, 3, …, last, › ======
    // Works for BOTH simple-datatables and jQuery DataTables by watching the DOM.
    (function() {
        var MAX_VISIBLE = 3; // show first 3 pages, then …, then last

        // ── simple-datatables pagination (uses <ul> with <li> items) ──
        function trimSimpleDT(paginationUl) {
            var items = paginationUl.querySelectorAll('li');
            if (!items.length) return;

            // Collect only numbered page <li> (skip prev/next arrows & existing ellipsis)
            var pageItems = [];
            var prevBtn = null, nextBtn = null;
            items.forEach(function(li) {
                var link = li.querySelector('a, button');
                var text = (link || li).textContent.trim();
                // Detect prev/next by class or by ‹/› characters
                if (li.classList.contains('dataTable-pagination-list-item--prev') ||
                    li.classList.contains('datatable-pagination-list-item--prev') ||
                    li.classList.contains('pager--prev') ||
                    text === '‹' || text === '«') {
                    prevBtn = li;
                } else if (li.classList.contains('dataTable-pagination-list-item--next') ||
                           li.classList.contains('datatable-pagination-list-item--next') ||
                           li.classList.contains('pager--next') ||
                           text === '›' || text === '»') {
                    nextBtn = li;
                } else if (li.classList.contains('ellipsis') || text === '…') {
                    li.remove(); // remove old ellipsis, we'll re-add
                } else {
                    var num = parseInt(text, 10);
                    if (!isNaN(num)) {
                        pageItems.push({ el: li, num: num });
                    }
                }
            });

            if (pageItems.length <= MAX_VISIBLE + 1) return; // already compact enough

            var lastPage = pageItems[pageItems.length - 1].num;

            // Decide which pages to show
            var activePage = 1;
            pageItems.forEach(function(p) {
                if (p.el.classList.contains('active') || p.el.classList.contains('dataTable-active') || p.el.classList.contains('datatable-active')) {
                    activePage = p.num;
                }
            });

            var showSet = new Set();
            // Show 3 pages around current
            for (var i = activePage; i <= Math.min(activePage + MAX_VISIBLE - 1, lastPage); i++) {
                showSet.add(i);
            }
            // If near end, also keep context
            if (activePage > lastPage - MAX_VISIBLE) {
                for (var k = Math.max(1, lastPage - MAX_VISIBLE); k <= lastPage; k++) {
                    showSet.add(k);
                }
            }
            // Always show first 3 pages when on page 1-3
            if (activePage <= MAX_VISIBLE) {
                for (var m = 1; m <= Math.min(MAX_VISIBLE, lastPage); m++) {
                    showSet.add(m);
                }
            }
            // Always show last page
            showSet.add(lastPage);

            var needsEllipsis = !showSet.has(lastPage - 1) && lastPage > MAX_VISIBLE + 1;

            pageItems.forEach(function(p) {
                if (showSet.has(p.num)) {
                    p.el.style.display = '';
                } else {
                    p.el.style.display = 'none';
                }
            });

            // Insert ellipsis before last page if gap exists
            if (needsEllipsis) {
                var lastEl = pageItems[pageItems.length - 1].el;
                // Check if ellipsis already exists right before
                var prevSib = lastEl.previousElementSibling;
                if (!prevSib || prevSib.textContent.trim() !== '…') {
                    var ellipsis = document.createElement('li');
                    ellipsis.className = 'ellipsis';
                    ellipsis.style.cssText = 'cursor:default; padding:6px 10px; display:list-item; opacity:0.6; font-weight:bold;';
                    ellipsis.textContent = '…';
                    lastEl.parentNode.insertBefore(ellipsis, lastEl);
                }
            }
        }

        // ── jQuery DataTables pagination (uses <span> with <a> buttons) ──
        function trimJQueryDT(wrapper) {
            var container = wrapper.querySelector('.dataTables_paginate');
            if (!container) return;

            var allBtns = container.querySelectorAll('.paginate_button:not(.previous):not(.next)');
            if (!allBtns.length) return;

            // Remove old custom ellipsis
            container.querySelectorAll('.custom-ellipsis').forEach(function(el) { el.remove(); });

            var pageItems = [];
            allBtns.forEach(function(btn) {
                var num = parseInt(btn.textContent.trim(), 10);
                if (!isNaN(num)) {
                    pageItems.push({ el: btn, num: num });
                }
            });

            if (pageItems.length <= MAX_VISIBLE + 1) return;

            var lastPage = pageItems[pageItems.length - 1].num;
            var activePage = 1;
            pageItems.forEach(function(p) {
                if (p.el.classList.contains('current')) activePage = p.num;
            });

            var showSet = new Set();
            for (var i = activePage; i <= Math.min(activePage + MAX_VISIBLE - 1, lastPage); i++) {
                showSet.add(i);
            }
            if (activePage > lastPage - MAX_VISIBLE) {
                for (var k = Math.max(1, lastPage - MAX_VISIBLE); k <= lastPage; k++) {
                    showSet.add(k);
                }
            }
            if (activePage <= MAX_VISIBLE) {
                for (var m = 1; m <= Math.min(MAX_VISIBLE, lastPage); m++) {
                    showSet.add(m);
                }
            }
            showSet.add(lastPage);

            var needsEllipsis = !showSet.has(lastPage - 1) && lastPage > MAX_VISIBLE + 1;

            pageItems.forEach(function(p) {
                p.el.style.display = showSet.has(p.num) ? '' : 'none';
            });

            if (needsEllipsis) {
                var lastEl = pageItems[pageItems.length - 1].el;
                var span = document.createElement('span');
                span.className = 'paginate_button custom-ellipsis disabled';
                span.style.cssText = 'cursor:default; padding:5px 10px; display:inline-block; opacity:0.6; font-weight:bold;';
                span.textContent = '…';
                lastEl.parentNode.insertBefore(span, lastEl);
            }
        }

        // ── Run on page load & watch for DOM changes ──
        function trimAll() {
            // simple-datatables
            document.querySelectorAll('.dataTable-pagination ul, .datatable-pagination ul').forEach(trimSimpleDT);
            // jQuery DataTables
            document.querySelectorAll('.dataTables_wrapper').forEach(trimJQueryDT);
        }

        // Run after everything is initialized
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() { setTimeout(trimAll, 200); });
        } else {
            setTimeout(trimAll, 200);
        }

        // Watch for pagination changes via MutationObserver
        var observer = new MutationObserver(function(mutations) {
            var shouldTrim = false;
            mutations.forEach(function(m) {
                var target = m.target;
                if (target && (
                    target.classList.contains('dataTable-pagination') ||
                    target.classList.contains('datatable-pagination') ||
                    target.classList.contains('dataTables_paginate') ||
                    (target.parentNode && (
                        target.parentNode.classList.contains('dataTable-pagination') ||
                        target.parentNode.classList.contains('datatable-pagination')
                    ))
                )) {
                    shouldTrim = true;
                }
            });
            if (shouldTrim) {
                setTimeout(trimAll, 10);
            }
        });
        observer.observe(document.body, { childList: true, subtree: true });

        // Also hook jQuery DataTables draw event
        $(document).on('draw.dt init.dt', function() {
            setTimeout(trimAll, 50);
        });
    })();
    </script>

    <script>
        feather.replace();
        var pctoggle = document.querySelector("#pct-toggler");
        if (pctoggle) {
            pctoggle.addEventListener("click", function() {
                if (
                    !document.querySelector(".pct-customizer").classList.contains("active")
                ) {
                    document.querySelector(".pct-customizer").classList.add("active");
                } else {
                    document.querySelector(".pct-customizer").classList.remove("active");
                }
            });
        }
        var themescolors = document.querySelectorAll(".themes-color > a");
        for (var h = 0; h < themescolors.length; h++) {
            var c = themescolors[h];
            c.addEventListener("click", function(event) {
                var targetElement = event.target;
                if (targetElement.tagName == "SPAN") {
                    targetElement = targetElement.parentNode;
                }
                var temp = targetElement.getAttribute("data-value");
                removeClassByPrefix(document.querySelector("body"), "theme-");
                document.querySelector("body").classList.add(temp);
            });
        }
        var custthemebg = document.querySelector("#cust_theme_bg");
        custthemebg.addEventListener("click", function() {
            if (custthemebg.checked) {
                document.querySelector(".dash-sidebar").classList.add("transprent-bg");
                document
                    .querySelector(".dash-header:not(.dash-mob-header)")
                    .classList.add("transprent-bg");
            } else {
                document.querySelector(".dash-sidebar").classList.remove("transprent-bg");
                document
                    .querySelector(".dash-header:not(.dash-mob-header)")
                    .classList.remove("transprent-bg");
            }
        });
        var custdarklayout = document.querySelector("#cust_darklayout");
        custdarklayout.addEventListener("click", function() {
            if (custdarklayout.checked) {
                document
                    .querySelector("#main-style-link")
                    .setAttribute("href", "{{ asset('assets/css/style-dark.css') }}");
                document
                    .querySelector(".m-header > .b-brand > .logo-lg")
                    .setAttribute("src", "{{ asset('/storage/uploads/logo/logo-light.png') }}");
            } else {
                document
                    .querySelector("#main-style-link")
                    .setAttribute("href", "{{ asset('assets/css/style.css') }}");
                document
                    .querySelector(".m-header > .b-brand > .logo-lg")
                    .setAttribute("src", "{{ asset('/storage/uploads/logo/logo-dark.png') }}");
            }
        });

        function removeClassByPrefix(node, prefix) {
            for (let i = 0; i < node.classList.length; i++) {
                let value = node.classList[i];
                if (value.startsWith(prefix)) {
                    node.classList.remove(value);
                }
            }
        }
    </script>

    <script>
        $(document).on('click', '.local_calender .fc-daygrid-event', function(e) {
            // if (!$(this).hasClass('project')) {
            e.preventDefault();
            var event = $(this);
            var title = $(this).find('.fc-event-title').html();

            var size = 'md';
            var url = $(this).attr('href');
            $("#commonModal .modal-title ").html(title);
            $("#commonModal .modal-dialog").addClass('modal-' + size);
            $.ajax({
                url: url,
                success: function(data) {
                    $('#commonModal .body').html(data);
                    $("#commonModal").modal('show');
                    if ($(".d_week").length > 0) {
                        $($(".d_week")).each(function(index, element) {
                            var id = $(element).attr('id');

                            // (function() {
                            //     const d_week = new Datepicker(document.querySelector('#' +
                            //         id), {
                            //         buttonClass: 'btn',
                            //         format: 'yyyy-mm-dd',
                            //     });
                            // })();

                        });
                    }

                },
                error: function(data) {
                    data = data.responseJSON;
                    toastrs('Error', data.error, 'error')
                }
            });
            // }
        });
    </script>

    <script src="https://js.pusher.com/5.0/pusher.min.js"></script>

    @if (\App\Models\Utility::getValByName('gdpr_cookie') == 'on')
        <script type="text/javascript">
            var defaults = {
                'messageLocales': {
                    /*'en': 'We use cookies to make sure you can have the best experience on our website. If you continue to use this site we assume that you will be happy with it.'*/
                    'en': "{{ \App\Models\Utility::getValByName('cookie_text') }}"
                },
                'buttonLocales': {
                    'en': 'Ok'
                },
                'cookieNoticePosition': 'bottom',
                'learnMoreLinkEnabled': false,
                'learnMoreLinkHref': '/cookie-banner-information.html',
                'learnMoreLinkText': {
                    'it': 'Saperne di più',
                    'en': 'Learn more',
                    'de': 'Mehr erfahren',
                    'fr': 'En savoir plus'
                },
                'buttonLocales': {
                    'en': 'Ok'
                },
                'expiresIn': 30,
                'buttonBgColor': '#d35400',
                'buttonTextColor': '#fff',
                'noticeBgColor': '#000',
                'noticeTextColor': '#fff',
                'linkColor': '#009fdd'
            };
        </script>
        <script src="{{ asset('js/cookie.notice.js') }}"></script>
    @endif

    @if (\Auth::user()->type != 'super admin')
        <script>
            $(document).ready(function() {
                pushNotification('{{ Auth::id() }}');
            });

            function pushNotification(id) {

                // ajax setup form csrf token
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Enable pusher logging - don't include this in production
                Pusher.logToConsole = false;

                var pusher = new Pusher('{{ $pusher_setting['pusher_app_key'] }}', {
                    cluster: '{{ $pusher_setting['pusher_app_cluster'] }}',
                    forceTLS: true
                });

                // Pusher Notification
                var channel = pusher.subscribe('send_notification');
                channel.bind('notification', function(data) {
                    if (id == data.user_id) {
                        $(".notification-toggle").addClass('beep');
                        $(".notification-dropdown #notification-list").prepend(data.html);
                    }
                });

                // Pusher Message
                var msgChannel = pusher.subscribe('my-channel');
                msgChannel.bind('my-chat', function(data) {

                    if (id == data.to) {
                        getChat();
                    }
                });
            }

            // Get chat for top ox
        </script>
    @endif


    @if ($message = Session::get('success'))
        <script>
            show_toastr('Success', '{!! $message !!}', 'success');
        </script>
    @endif
    @if ($message = Session::get('error'))
        <script>
            show_toastr('Error', '{!! $message !!}', 'error');
        </script>
    @endif


    @stack('script-page')

    @stack('scripts')
    @include('Chatify::layouts.footerLinks')

    @stack('custom-scripts')
    @if ($enable_cookie['enable_cookie'] == 'on')
        @include('layouts.cookie_consent')
    @endif

</body>

</html>
