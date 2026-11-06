<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Event Management System</title>
    <meta name="description" content="Professional Event Management System - Manage events, orders, payments and more.">

    <!-- Google Fonts (Inter & Plus Jakarta Sans) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">

    <!-- Select2 & Select2 Bootstrap 5 Theme -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <style>
        :root {
            --primary: #484646;
            --primary-dark: #3a3838;
            --primary-light: #5f5c5c;
            --secondary: #666363;
            --success: #16a34a;
            --warning: #d97706;
            --danger: #dc2626;
            --info: #0284c7;
            --dark: #27272a;
            --sidebar-bg: #484646;
            --sidebar-width: 250px;
            --header-height: 60px;
            --card-border-radius: 12px;
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f4f6f9;
            color: #334155;
            margin: 0;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* === SIDEBAR === */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            z-index: 1000;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #5c5959 transparent;
            transition: var(--transition);
        }

        /* Desktop Slide Left / Collapse */
        body.sidebar-collapsed .sidebar {
            transform: translateX(-100%);
        }

        body.sidebar-collapsed .topbar {
            left: 0;
        }

        body.sidebar-collapsed .main-content {
            margin-left: 0;
        }

        .sidebar-brand {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            background: #484646;
        }

        .sidebar-brand .brand-icon {
            width: 36px;
            height: 36px;
            background: #ffffff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #484646;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            flex-shrink: 0;
        }

        .sidebar-brand .brand-text h5 {
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
            letter-spacing: -0.2px;
        }

        .sidebar-brand .brand-text span {
            color: #d1cfcf;
            font-size: 11px;
            font-weight: 400;
        }

        .sidebar-nav {
            padding: 10px 0;
        }

        .nav-section-title {
            padding: 10px 18px 4px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #d1cfcf;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8.5px 18px;
            color: #e4e4e7;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: var(--transition);
            border-radius: 0;
            position: relative;
        }

        .sidebar-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.12);
        }

        .sidebar-link.active {
            color: #ffffff;
            background: #3a3838;
        }

        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #ffffff;
            border-radius: 0 2px 2px 0;
        }

        .sidebar-link i {
            font-size: 15px;
            min-width: 18px;
        }

        /* === SIDEBAR DROPDOWNS === */
        .sidebar-dropdown {
            margin-bottom: 2px;
        }

        .sidebar-dropdown-toggle {
            cursor: pointer;
            user-select: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-dropdown-toggle .dropdown-chevron {
            font-size: 11px;
            color: #d1cfcf;
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-dropdown-toggle[aria-expanded="true"] {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
        }

        .sidebar-dropdown-toggle[aria-expanded="true"] .dropdown-chevron {
            transform: rotate(180deg);
            color: #ffffff;
        }

        .sidebar-submenu {
            background: rgba(0, 0, 0, 0.16);
            border-left: 2px solid rgba(255, 255, 255, 0.18);
            margin: 2px 14px 6px 22px;
            padding: 4px 0;
            border-radius: 0 8px 8px 0;
        }

        .sidebar-submenu .sidebar-link {
            font-size: 12.5px;
            padding: 7px 12px;
            color: #d1cfcf;
            gap: 8px;
            border-radius: 6px;
            margin: 1px 6px;
        }

        .sidebar-submenu .sidebar-link i {
            font-size: 13px;
            min-width: 16px;
            opacity: 0.85;
        }

        .sidebar-submenu .sidebar-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.12);
        }

        .sidebar-submenu .sidebar-link.active {
            color: #ffffff;
            background: #3a3838;
            font-weight: 600;
        }

        .sidebar-submenu .sidebar-link.active::before {
            display: none;
        }

        /* === TOPBAR === */
        .topbar {
            position: fixed;
            left: var(--sidebar-width);
            right: 0;
            top: 0;
            height: var(--header-height);
            background: linear-gradient(90deg, #3a3838 0%, #484646 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.12);
            transition: var(--transition);
        }

        .topbar .page-title {
            font-size: 16px;
            font-weight: 700;
            color: #ffffff;
            margin: 0;
            letter-spacing: -0.2px;
        }

        .topbar .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .topbar-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #f4f4f5;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            position: relative;
            flex-shrink: 0;
        }

        .topbar-btn:hover {
            background: rgba(255, 255, 255, 0.22);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
        }

        .notification-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            width: 18px;
            height: 18px;
            background: #ef4444;
            border-radius: 50%;
            font-size: 10px;
            font-weight: 700;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #3a3838;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            background: #ffffff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3a3838;
            font-weight: 800;
            font-size: 15px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: var(--transition);
        }

        .user-avatar:hover {
            transform: scale(1.05);
        }

        /* === TRITCAL PROJECT MANAGEMENT STYLE BUTTONS (HEIGHT: 35px, #3a3838) === */
        .btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            padding: 5px 14px !important;
            height: 35px !important;
            min-height: 35px !important;
            max-height: 35px !important;
            font-size: 12.5px !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
            cursor: pointer !important;
            transition: var(--transition) !important;
            text-decoration: none !important;
            white-space: nowrap !important;
            box-sizing: border-box !important;
        }

        .btn-primary,
        .btn-dark,
        .dash-action-btn,
        .btn-hero-primary,
        .fc-button-primary,
        button[type="submit"]:not(.btn-link):not(.btn-close):not(.btn-action):not(.btn-secondary):not(.btn-cancel):not(.btn-light-danger),
        .btn.btn-primary,
        .btn.btn-dark {
            background: linear-gradient(135deg, #484646 0%, #3a3838 100%) !important;
            border: 1px solid #3a3838 !important;
            color: #ffffff !important;
            box-shadow: 0 3px 8px rgba(58, 56, 56, 0.2) !important;
        }

        .btn-primary:hover, .btn-primary:focus,
        .btn-dark:hover, .btn-dark:focus,
        .dash-action-btn:hover, .dash-action-btn:focus,
        .btn-hero-primary:hover, .btn-hero-primary:focus,
        .fc-button-primary:hover, .fc-button-primary:focus,
        button[type="submit"]:not(.btn-link):not(.btn-close):not(.btn-action):not(.btn-secondary):not(.btn-cancel):not(.btn-light-danger):hover,
        button[type="submit"]:not(.btn-link):not(.btn-close):not(.btn-action):not(.btn-secondary):not(.btn-cancel):not(.btn-light-danger):focus {
            background: linear-gradient(135deg, #3a3838 0%, #252424 100%) !important;
            border-color: #252424 !important;
            color: #ffffff !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 5px 12px rgba(58, 56, 56, 0.28) !important;
        }

        .btn-primary:active,
        .btn-dark:active,
        button[type="submit"]:active {
            transform: translateY(0) !important;
            box-shadow: 0 2px 4px rgba(58, 56, 56, 0.2) !important;
        }

        /* Secondary & Outline Buttons (Tritcal style: clean white surface with border) */
        .btn-secondary,
        .btn-outline-secondary,
        .btn-outline-dark,
        .btn-outline-primary {
            background: #ffffff !important;
            color: #4b5563 !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
            height: 35px !important;
            border-radius: 6px !important;
        }

        .btn-secondary:hover, .btn-secondary:focus,
        .btn-outline-secondary:hover, .btn-outline-secondary:focus,
        .btn-outline-dark:hover, .btn-outline-dark:focus,
        .btn-outline-primary:hover, .btn-outline-primary:focus {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #1e293b !important;
            transform: translateY(-1px) !important;
        }

        /* Success Button */
        .btn-success,
        .btn-outline-success {
            background: linear-gradient(135deg, #28a745, #34c759) !important;
            color: #ffffff !important;
            border: 1px solid #28a745 !important;
            box-shadow: 0 3px 8px rgba(40, 167, 69, 0.2) !important;
            height: 35px !important;
            border-radius: 6px !important;
        }

        .btn-success:hover, .btn-success:focus,
        .btn-outline-success:hover, .btn-outline-success:focus {
            background: linear-gradient(135deg, #218838, #28a745) !important;
            color: #ffffff !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 5px 12px rgba(40, 167, 69, 0.28) !important;
        }

        /* Cancel & Light Danger (Tritcal style) */
        .btn-light-danger,
        .btn-cancel,
        .btn-outline-danger {
            background: #fef2f2 !important;
            color: #dc2626 !important;
            border: 1px solid #fecaca !important;
            box-shadow: 0 1px 2px rgba(239, 68, 68, 0.06) !important;
            height: 35px !important;
            border-radius: 6px !important;
        }

        .btn-light-danger:hover, .btn-cancel:hover, .btn-outline-danger:hover {
            background: #fee2e2 !important;
            border-color: #fca5a5 !important;
            color: #b91c1c !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 3px 8px rgba(239, 68, 68, 0.14) !important;
        }

        .btn-light {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            color: #4b5563 !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
            height: 35px !important;
        }

        .btn-light:hover {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #1e293b !important;
        }

        .btn-light-gray {
            background: #cbd5e1 !important;
            border: 1px solid #94a3b8 !important;
            color: #0f172a !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
        }

        .btn-light-gray:hover {
            background: #94a3b8 !important;
            border-color: #64748b !important;
            color: #0f172a !important;
        }

        /* Small Button in Tritcal (Height: 28px) */
        .btn-sm,
        .btn-sm.btn-primary,
        .btn-sm.btn-dark,
        .btn-sm.btn-success,
        .btn-sm.btn-secondary,
        .btn-group-sm > .btn {
            height: 28px !important;
            min-height: 28px !important;
            max-height: 28px !important;
            padding: 3px 10px !important;
            font-size: 11.5px !important;
            border-radius: 5px !important;
        }

        .text-primary {
            color: #3a3838 !important;
        }

        .bg-primary {
            background-color: #3a3838 !important;
        }

        .bg-primary-subtle {
            background-color: #ebebea !important;
            color: #3a3838 !important;
        }

        .border-primary-subtle {
            border-color: #d1cfcf !important;
        }

        .page-item.active .page-link {
            background: linear-gradient(135deg, #484646 0%, #3a3838 100%) !important;
            border-color: #3a3838 !important;
            color: #ffffff !important;
        }

        .form-control:focus, .form-select:focus {
            border-color: #3a3838 !important;
            box-shadow: 0 0 0 3px rgba(58, 56, 56, 0.15) !important;
        }

        .nav-pills .nav-link.active,
        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #484646 0%, #3a3838 100%) !important;
            border-color: #3a3838 !important;
            color: #ffffff !important;
        }

        .summary-box {
            background: linear-gradient(135deg, #484646 0%, #3a3838 100%) !important;
            border-radius: var(--card-border-radius);
            padding: 24px;
            color: white;
            box-shadow: 0 4px 16px rgba(72, 70, 70, 0.25);
        }

        /* === MAIN CONTENT === */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 24px;
            min-height: calc(100vh - var(--header-height));
            transition: var(--transition);
        }

        /* === CARDS (100% FULL WIDTH ACROSS ENTIRE PROJECT) === */
        .card {
            width: 100% !important;
            border: 1px solid #edf2f7 !important;
            border-radius: var(--card-border-radius);
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.03) !important;
            background: #ffffff !important;
            transition: var(--transition);
            margin-bottom: 22px;
        }

        .card:hover {
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.07), 0 2px 6px rgba(0, 0, 0, 0.03) !important;
        }

        .main-content > .row.justify-content-center > [class*="col-"],
        .main-content .row.justify-content-center > [class*="col-"] {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 100% !important;
        }

        .card-header {
            border-bottom: 1px solid #f1f5f9;
            background: transparent;
            padding: 16px 20px;
            border-radius: var(--card-border-radius) var(--card-border-radius) 0 0 !important;
        }

        .card-header h6,
        .card-header h5,
        .card-header h4 {
            font-weight: 700;
            margin: 0;
            color: #0f172a;
        }

        .card-body {
            padding: 20px;
        }

        /* === STAT CARDS === */
        .stat-card {
            background: white;
            border-radius: var(--card-border-radius);
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            transition: var(--transition);
            text-decoration: none;
            color: inherit;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            color: inherit;
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .stat-icon.purple {
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary);
        }

        .stat-icon.green {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .stat-icon.amber {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .stat-icon.blue {
            background: rgba(6, 182, 212, 0.1);
            color: var(--info);
        }

        .stat-icon.red {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .stat-label {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
            margin: 0;
        }

        .stat-value {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            line-height: 1.1;
        }

        .stat-sub {
            font-size: 11px;
            color: #94a3b8;
            margin: 0;
            margin-top: 2px;
        }

        /* === BADGES === */
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-pending {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
        }

        .badge-confirmed {
            background: rgba(59, 130, 246, 0.1);
            color: #2563eb;
        }

        .badge-completed {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .badge-cancelled {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        .badge-partial {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
        }

        .badge-paid {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .badge-draft {
            background: rgba(100, 116, 139, 0.1);
            color: #64748b;
        }

        .badge-sent {
            background: rgba(59, 130, 246, 0.1);
            color: #2563eb;
        }

        .badge-accepted {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .badge-rejected {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        .badge-direct {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        .badge-normal {
            background: rgba(100, 116, 139, 0.1);
            color: #64748b;
        }

        /* === CARDS === */
        .card {
            background: #ffffff !important;
            border-radius: 16px !important;
            border: 1px solid rgba(0, 0, 0, 0.06) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03) !important;
            overflow: hidden;
            transition: var(--transition);
        }

        .card-header {
            background: #ffffff !important;
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 16px 20px !important;
            font-weight: 700 !important;
            color: #1e293b !important;
            font-size: 14.5px !important;
        }

        .card-body {
            padding: 20px !important;
        }

        .card-footer {
            background: #ffffff !important;
            border-top: 1px solid #f1f5f9 !important;
            padding: 14px 20px !important;
        }

        /* === UNIFIED BUTTONS SYSTEM === */
        .btn {
            font-family: inherit;
            font-weight: 600;
            font-size: 13.5px;
            border-radius: 10px;
            padding: 8px 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            cursor: pointer;
            line-height: 1.4;
        }

        .btn:hover {
            transform: translateY(-1.5px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-sm {
            padding: 5px 12px;
            font-size: 12px;
            border-radius: 8px;
        }

        .btn-lg {
            padding: 12px 24px;
            font-size: 15px;
            border-radius: 12px;
            gap: 8px;
        }

        /* ==============================================================
           TRITCAL UNIFIED BUTTON SYSTEM (#3a3838 Charcoal Theme)
           ============================================================== */

        /* 1. PRIMARY BUTTON: Seamless Charcoal Gradient & Matching Hover */
        .btn-primary,
        .btn-dark {
            background: linear-gradient(135deg, #484646 0%, #3a3838 100%) !important;
            border: 1px solid #3a3838 !important;
            color: #ffffff !important;
            box-shadow: 0 3px 8px rgba(58, 56, 56, 0.2) !important;
        }

        .btn-primary:hover,
        .btn-primary:focus,
        .btn-dark:hover,
        .btn-dark:focus {
            background: linear-gradient(135deg, #3d3b3b 0%, #302e2e 100%) !important;
            border-color: #302e2e !important;
            color: #ffffff !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 5px 12px rgba(58, 56, 56, 0.3) !important;
        }

        .btn-primary:active,
        .btn-dark:active {
            background: #2e2c2c !important;
            border-color: #2e2c2c !important;
            transform: translateY(0) !important;
            box-shadow: 0 2px 4px rgba(58, 56, 56, 0.2) !important;
        }

        /* 2. SUCCESS BUTTON */
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #34c759 100%) !important;
            border: 1px solid #28a745 !important;
            color: #ffffff !important;
            box-shadow: 0 3px 8px rgba(40, 167, 69, 0.2) !important;
        }

        .btn-success:hover,
        .btn-success:focus {
            background: linear-gradient(135deg, #218838 0%, #28a745 100%) !important;
            border-color: #218838 !important;
            color: #ffffff !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 5px 12px rgba(40, 167, 69, 0.28) !important;
        }

        /* 3. DANGER & CANCEL BUTTONS */
        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
            border: 1px solid #dc2626 !important;
            color: #ffffff !important;
            box-shadow: 0 3px 8px rgba(239, 68, 68, 0.2) !important;
        }

        .btn-danger:hover,
        .btn-danger:focus {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
            border-color: #b91c1c !important;
            color: #ffffff !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 5px 12px rgba(239, 68, 68, 0.28) !important;
        }

        /* 4. SECONDARY & OUTLINE SECONDARY (Clean White with Subtle Hover) */
        .btn-secondary,
        .btn-outline-secondary,
        .btn-outline-dark {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            color: #4b5563 !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
        }

        .btn-secondary:hover,
        .btn-secondary:focus,
        .btn-outline-secondary:hover,
        .btn-outline-secondary:focus,
        .btn-outline-dark:hover,
        .btn-outline-dark:focus {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #1e293b !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.06) !important;
        }

        /* 5. LIGHT CANCEL (Tritcal Style) */
        .btn-light-danger,
        .btn-cancel,
        .btn-outline-danger {
            background: #fef2f2 !important;
            color: #dc2626 !important;
            border: 1px solid #fecaca !important;
            box-shadow: 0 1px 2px rgba(239, 68, 68, 0.06) !important;
        }

        .btn-light-danger:hover,
        .btn-cancel:hover,
        .btn-outline-danger:hover {
            background: #fee2e2 !important;
            border-color: #fca5a5 !important;
            color: #b91c1c !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 3px 8px rgba(239, 68, 68, 0.14) !important;
        }

        /* 6. LIGHT BUTTON */
        .btn-light {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            color: #4b5563 !important;
        }

        .btn-light:hover,
        .btn-light:focus {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #1e293b !important;
            transform: translateY(-1px) !important;
        }

        .btn:disabled,
        .btn.disabled {
            opacity: 0.65 !important;
            pointer-events: none !important;
            transform: none !important;
            box-shadow: none !important;
        }

        /* Square Icon Buttons */
        .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 13px;
        }

        .btn-icon:hover {
            transform: translateY(-1.5px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        /* === MODERN SAAS TABLE ACTION BUTTONS === */
        .action-btns-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: 6px;
        }

        /* === TRITCAL ACTION BUTTONS (HIGHLY READABLE & INTUITIVE) === */
        .action-btns-wrapper {
            display: inline-flex !important;
            align-items: center !important;
            gap: 5px !important;
            flex-wrap: nowrap !important;
        }

        .btn-action {
            height: 28px !important;
            min-height: 28px !important;
            padding: 3px 8px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 4px !important;
            border-radius: 6px !important;
            font-size: 11.5px !important;
            font-weight: 600 !important;
            text-decoration: none !important;
            border: 1px solid transparent !important;
            transition: all 0.15s ease-in-out !important;
            background: #ffffff !important;
            cursor: pointer !important;
            white-space: nowrap !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
            line-height: 1 !important;
        }

        .btn-action i {
            font-size: 12px !important;
            line-height: 1 !important;
        }

        .btn-action span {
            display: inline-block !important;
            line-height: 1 !important;
        }

        .btn-action:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08) !important;
        }

        .btn-action:active {
            transform: translateY(0) !important;
        }

        /* 1. View Action (Soft Blue / Info) */
        .btn-action-view {
            background: #eff6ff !important;
            color: #1d4ed8 !important;
            border-color: #bfdbfe !important;
        }
        .btn-action-view:hover {
            background: #1d4ed8 !important;
            color: #ffffff !important;
            border-color: #1d4ed8 !important;
        }

        /* 2. Edit Action (Soft Amber / Warning) */
        .btn-action-edit {
            background: #fefce8 !important;
            color: #b45309 !important;
            border-color: #fde047 !important;
        }
        .btn-action-edit:hover {
            background: #d97706 !important;
            color: #ffffff !important;
            border-color: #d97706 !important;
        }

        /* 3. Delete Action (Soft Red / Danger) */
        .btn-action-delete {
            background: #fef2f2 !important;
            color: #b91c1c !important;
            border-color: #fecaca !important;
        }
        .btn-action-delete:hover {
            background: #dc2626 !important;
            color: #ffffff !important;
            border-color: #dc2626 !important;
        }

        /* 4. Order / Success Action (Soft Emerald / Green) */
        .btn-action-order,
        .btn-action-success {
            background: #ecfdf5 !important;
            color: #047857 !important;
            border-color: #a7f3d0 !important;
        }
        .btn-action-order:hover,
        .btn-action-success:hover {
            background: #059669 !important;
            color: #ffffff !important;
            border-color: #059669 !important;
        }

        /* 5. Warning / Special Action (Soft Orange) */
        .btn-action-warning {
            background: #fffbeb !important;
            color: #c2410c !important;
            border-color: #fed7aa !important;
        }
        .btn-action-warning:hover {
            background: #ea580c !important;
            color: #ffffff !important;
            border-color: #ea580c !important;
        }

        /* === RESPONSIVE BEHAVIOR FOR ACTION BUTTONS ACROSS DIFFERENT SCREEN SIZES === */
        /* Large Desktop (>= 1200px): Generous spacing with full icon & text */
        @media (min-width: 1200px) {
            .btn-action {
                height: 28px !important;
                padding: 3px 9px !important;
                gap: 4.5px !important;
            }
            .btn-action span {
                display: inline-block !important;
                font-size: 11.5px !important;
            }
        }

        /* Laptop / Standard Desktop (992px to 1199.98px): Compact icon & text */
        @media (min-width: 992px) and (max-width: 1199.98px) {
            .btn-action {
                height: 28px !important;
                padding: 3px 7px !important;
                gap: 3.5px !important;
            }
            .btn-action span {
                display: inline-block !important;
                font-size: 11px !important;
            }
            .action-btns-wrapper {
                gap: 4px !important;
            }
        }

        /* Tablets & Mobile Landscape (< 992px): Smoothly collapse to sleek square icon buttons */
        @media (max-width: 991.98px) {
            .btn-action {
                width: 30px !important;
                min-width: 30px !important;
                max-width: 30px !important;
                height: 30px !important;
                min-height: 30px !important;
                padding: 0 !important;
                gap: 0 !important;
                justify-content: center !important;
                border-radius: 6px !important;
            }
            .btn-action span {
                display: none !important;
            }
            .btn-action i {
                font-size: 13.5px !important;
                margin: 0 !important;
            }
            .action-btns-wrapper {
                gap: 4px !important;
            }
        }

        /* Mobile Phones (< 576px): Ultra-compact touch buttons */
        @media (max-width: 575.98px) {
            .btn-action {
                width: 27px !important;
                min-width: 27px !important;
                max-width: 27px !important;
                height: 27px !important;
                min-height: 27px !important;
            }
            .btn-action i {
                font-size: 12px !important;
            }
            .action-btns-wrapper {
                gap: 3px !important;
            }
        }

        /* === UNIFIED TABLES SYSTEM === */
        .table-responsive {
            border-radius: 16px;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            margin-bottom: 0 !important;
            vertical-align: middle;
            border-collapse: collapse;
        }

        .table thead th {
            background: #f8fafc !important;
            color: #64748b !important;
            font-size: 11.5px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            border-top: none !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 12px 18px !important;
            white-space: nowrap !important;
        }

        .table tbody td {
            padding: 13px 18px !important;
            color: #334155 !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            vertical-align: middle !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }

        .table tbody tr:hover td {
            background: #fafbfc !important;
        }

        .table tbody tr:last-child td {
            border-bottom: none !important;
        }

        .table a:not(.btn):not(.btn-action):not(.badge) {
            color: #0f172a;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.18s ease;
        }

        .table a:not(.btn):not(.btn-action):not(.badge):hover {
            color: #2563eb;
            text-decoration: underline;
        }

        /* === TRITCAL STYLE UNIFIED FORMS & CONTROLS (HEIGHT: 35px) === */
        .form-control:not(textarea),
        .form-select,
        .form-control-sm:not(textarea),
        .form-select-sm,
        input[type="text"]:not(.form-check-input),
        input[type="number"]:not(.form-check-input),
        input[type="email"]:not(.form-check-input),
        input[type="password"]:not(.form-check-input),
        input[type="date"]:not(.form-check-input),
        input[type="time"]:not(.form-check-input),
        input[type="datetime-local"]:not(.form-check-input),
        input[type="search"]:not(.form-check-input),
        input[type="tel"]:not(.form-check-input),
        input[type="url"]:not(.form-check-input),
        select.form-select,
        select.form-control,
        select {
            width: 100%;
            height: 35px !important;
            min-height: 35px !important;
            max-height: 35px !important;
            padding: 5px 11px !important;
            font-size: 12.5px !important;
            line-height: 1.5 !important;
            color: #1e293b !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 6px !important;
            outline: none !important;
            box-shadow: 0 1px 2px rgba(56, 57, 58, 0.04) !important;
            box-sizing: border-box !important;
            transition: var(--transition) !important;
        }

        .form-select,
        select.form-select,
        select.form-control,
        select {
            background-position: right 10px center !important;
            background-size: 13px 9px !important;
            padding-right: 30px !important;
        }

        .form-control:hover,
        .form-select:hover {
            border-color: #cbd5e1 !important;
        }

        .form-control:focus,
        .form-select:focus,
        input:focus:not([type="checkbox"]):not([type="radio"]),
        select:focus,
        textarea:focus {
            border-color: #94a3b8 !important;
            box-shadow: 0 0 0 3px rgba(148, 163, 184, 0.22) !important;
            background-color: #ffffff !important;
            outline: none !important;
        }

        .form-control::placeholder {
            color: #94a3b8 !important;
            font-size: 12px !important;
        }

        textarea.form-control,
        textarea {
            height: auto !important;
            min-height: 60px !important;
            padding: 7px 11px !important;
            font-size: 12.5px !important;
            border-radius: 6px !important;
            border: 1px solid #e2e8f0 !important;
            color: #1e293b !important;
            background: #ffffff !important;
            box-sizing: border-box !important;
            outline: none !important;
            resize: vertical !important;
        }

        /* Input Group Alignments in Tritcal Style */
        .input-group > .form-control,
        .input-group > .form-select,
        .input-group-sm > .form-control,
        .input-group-sm > .form-select {
            height: 35px !important;
            min-height: 35px !important;
            max-height: 35px !important;
            border-radius: 6px !important;
        }

        .input-group-text,
        .input-group-sm > .input-group-text {
            height: 35px !important;
            min-height: 35px !important;
            max-height: 35px !important;
            padding: 5px 11px !important;
            font-size: 12.5px !important;
            border-radius: 6px !important;
            border: 1px solid #e2e8f0 !important;
            background-color: #f8fafc !important;
            color: #64748b !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-sizing: border-box !important;
        }

        .input-group > .btn,
        .input-group-sm > .btn {
            height: 35px !important;
            min-height: 35px !important;
            max-height: 35px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 5px 14px !important;
            font-size: 12.5px !important;
            border-radius: 6px !important;
            box-sizing: border-box !important;
        }

        .input-with-action {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            width: 100% !important;
            min-width: 0 !important;
            position: relative !important;
        }

        .input-with-action .form-control,
        .input-with-action .form-select {
            flex: 1 1 auto !important;
            min-width: 0 !important;
            width: 100% !important;
        }

        .btn-add-action {
            height: 35px !important;
            width: 35px !important;
            min-width: 35px !important;
            max-width: 35px !important;
            border-radius: 6px !important;
            background: #484646 !important;
            color: #ffffff !important;
            border: 1px solid #3a3838 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 13px !important;
            cursor: pointer !important;
            transition: var(--transition) !important;
            flex-shrink: 0 !important;
            box-shadow: 0 1px 3px rgba(79, 83, 88, 0.15) !important;
            padding: 0 !important;
        }

        .btn-add-action:hover {
            background: #3a3838 !important;
            color: #ffffff !important;
            transform: scale(1.03) !important;
        }

        /* === SELECT2 TRITCAL PROJECT MANAGEMENT STYLE === */
        select.select2-hidden-accessible {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            border: 0 !important;
        }

        .select2-container {
            width: 100% !important;
            display: block !important;
        }

        .select2-container--bootstrap-5 .select2-selection,
        .select2-container .select2-selection--single {
            height: 35px !important;
            min-height: 35px !important;
            max-height: 35px !important;
            padding: 4px 10px !important;
            font-size: 12.5px !important;
            color: #1e293b !important;
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 6px !important;
            display: flex !important;
            align-items: center !important;
            box-shadow: 0 1px 2px rgba(56, 57, 58, 0.04) !important;
            transition: var(--transition) !important;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered,
        .select2-container .select2-selection--single .select2-selection__rendered {
            padding-left: 0 !important;
            padding-right: 20px !important;
            font-size: 12.5px !important;
            color: #1e293b !important;
            line-height: normal !important;
            display: block !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__placeholder {
            color: #94a3b8 !important;
            font-size: 12px !important;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow,
        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 35px !important;
            right: 8px !important;
            top: 0 !important;
            display: flex !important;
            align-items: center !important;
        }

        .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .select2-container--bootstrap-5.select2-container--open .select2-selection {
            border-color: #94a3b8 !important;
            box-shadow: 0 0 0 3px rgba(148, 163, 184, 0.22) !important;
            outline: none !important;
        }

        .select2-dropdown {
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
            font-size: 12.5px !important;
            z-index: 9999 !important;
            overflow: hidden !important;
            background: #ffffff !important;
        }

        .select2-search--dropdown {
            padding: 7px 8px !important;
            background: #f8fafc !important;
            border-bottom: 1px solid #edf2f7 !important;
        }

        .select2-search--dropdown .select2-search__field {
            height: 32px !important;
            min-height: 32px !important;
            max-height: 32px !important;
            padding: 4px 10px !important;
            font-size: 12px !important;
            border-radius: 5px !important;
            border: 1px solid #cbd5e1 !important;
            outline: none !important;
            width: 100% !important;
            box-sizing: border-box !important;
            background: #ffffff !important;
            color: #1e293b !important;
        }

        .select2-search--dropdown .select2-search__field:focus {
            border-color: #484646 !important;
            box-shadow: 0 0 0 2px rgba(72, 70, 70, 0.15) !important;
        }

        .select2-results__options {
            max-height: 220px !important;
            font-size: 12.5px !important;
        }

        .select2-results__option {
            padding: 6px 12px !important;
            font-size: 12.5px !important;
            cursor: pointer !important;
        }

        .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected],
        .select2-results__option--highlighted {
            background-color: #3a3838 !important;
            color: #ffffff !important;
        }

        .select2-container--bootstrap-5 .select2-results__option--selected {
            background-color: #f1f5f9 !important;
            color: #1e293b !important;
            font-weight: 600 !important;
        }

        .select2-container--bootstrap-5 .select2-selection__clear {
            margin-right: 18px !important;
            color: #94a3b8 !important;
        }
        .select2-container--bootstrap-5 .select2-selection__clear:hover {
            color: #dc2626 !important;
        }

        .form-label {
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #475569 !important;
            display: flex !important;
            align-items: center !important;
            gap: 4px !important;
            margin-bottom: 2px !important;
        }

        /* === PAGE HEADER === */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-header h4 {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.3px;
        }

        .page-header p {
            font-size: 13px;
            color: #64748b;
            margin-top: 3px;
            margin-bottom: 0;
        }

        .page-header .breadcrumb {
            margin: 0;
            font-size: 12px;
        }

        /* === ALERTS === */
        .alert {
            border-radius: 12px;
            border: none;
            font-size: 13.5px;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.08);
            color: #059669;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.08);
            color: #dc2626;
        }

        .alert-info {
            background: rgba(6, 182, 212, 0.08);
            color: #0891b2;
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.08);
            color: #d97706;
        }

        /* === LINE ITEMS TABLE === */
        .line-items-table {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
        }

        .line-items-table .table {
            margin: 0;
        }

        /* === MODAL === */
        .modal-content {
            border-radius: 16px;
            border: none;
        }

        .modal-header {
            border-bottom: 1px solid #f1f5f9;
            padding: 20px 24px 16px;
        }

        .modal-header h5 {
            font-weight: 700;
            color: #0f172a;
        }

        .modal-footer {
            border-top: 1px solid #f1f5f9;
            padding: 16px 24px 20px;
        }

        /* === CUSTOM SCROLLBAR === */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* === RESPONSIVE & MOBILE OVERLAY === */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
            transition: opacity 0.25s ease;
        }

        /* Large Desktop (>= 1400px) */
        @media (min-width: 1400px) {
            .main-content {
                padding: 24px 28px;
            }
        }

        /* Laptops & Standard Desktops (992px to 1199.98px - Fixes crowded laptop view!) */
        @media (min-width: 992px) and (max-width: 1199.98px) {
            :root {
                --sidebar-width: 230px;
                --header-height: 58px;
            }
            .main-content {
                padding: 18px 20px;
            }
            .sidebar-link {
                font-size: 13px;
                padding: 8px 16px;
            }
            .sidebar-brand {
                padding: 0 16px;
            }
            .topbar {
                padding: 0 18px;
            }
            .card-body {
                padding: 16px;
            }
        }

        /* Tablets & Mobile Devices (max-width: 991.98px - Full offcanvas drawer for maximum space!) */
        @media (max-width: 991.98px) {
            :root {
                --header-height: 58px;
            }
            .sidebar {
                transform: translateX(-100%);
                box-shadow: 6px 0 28px rgba(0, 0, 0, 0.4);
                width: 270px;
            }
            .sidebar.show,
            body.sidebar-mobile-open .sidebar {
                transform: translateX(0) !important;
            }
            body.sidebar-mobile-open .sidebar-overlay {
                display: block;
            }
            body.sidebar-mobile-open {
                overflow: hidden;
            }
            .topbar,
            .main-content {
                margin-left: 0 !important;
                left: 0 !important;
            }
            .main-content {
                padding: 16px 18px;
            }
            .topbar {
                padding: 0 16px;
            }
        }

        /* Mobile Phones (max-width: 575.98px) */
        @media (max-width: 575.98px) {
            :root {
                --header-height: 56px;
            }
            .main-content {
                padding: 12px 14px;
            }
            .topbar {
                padding: 0 12px;
            }
            .page-header {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 12px;
                margin-bottom: 18px;
            }
            .page-header > div {
                width: 100%;
            }
            .page-header .btn {
                width: 100%;
                justify-content: center;
            }
            .card-body {
                padding: 14px;
            }
            .card-header {
                padding: 12px 14px;
            }
            .form-control:not(textarea), .form-select {
                font-size: 12.5px !important;
                padding: 5px 10px !important;
                height: 35px !important;
            }
        }

        /* === TABLE RESPONSIVE ENHANCEMENTS === */
        .table-responsive {
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            border-radius: var(--card-border-radius);
        }

        /* === NOTIFICATION DOT === */
        .notification-dot {
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }

        /* === DROPDOWN === */
        .dropdown-menu {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 6px;
        }

        .dropdown-item {
            border-radius: 8px;
            font-size: 13.5px;
            padding: 8px 12px;
            color: #374151;
        }

        .dropdown-item:hover {
            background: #f8fafc;
            color: #6366f1;
        }

        /* === SIDEBAR TOGGLE === */
        .sidebar-collapse .sidebar-link span {
            display: none;
        }

        /* === PRINT STYLES === */
        @media print {

            .sidebar,
            .topbar,
            .no-print {
                display: none !important;
            }

            .main-content {
                margin: 0 !important;
                padding: 20px !important;
            }
        }
    </style>

    @yield('styles')
</head>

<body>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2.5">
                <div class="brand-text">
                    <h5>EventPro</h5>
                    <span>Management System</span>
                </div>
            </div>
            <!-- Mobile / Tablet close drawer button -->
            <button type="button" class="btn-close btn-close-white d-lg-none" onclick="toggleSidebar()" aria-label="Close sidebar"></button>
        </div>

        <div class="sidebar-nav">
            <!-- 1. Dashboard -->
            <a href="{{ route('dashboard') }}"
                class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>

            <!-- 2. Operations (Dropdown) -->
            @php
                $isOperationsActive = request()->routeIs('events.*') || request()->routeIs('calendar') || request()->routeIs('orders.*') || request()->routeIs('quotations.*') || request()->routeIs('customers.*');
            @endphp
            <div class="sidebar-dropdown">
                <a href="#menuOperations" 
                   class="sidebar-link sidebar-dropdown-toggle {{ $isOperationsActive ? 'active' : '' }}" 
                   data-bs-toggle="collapse" 
                   role="button" 
                   aria-expanded="{{ $isOperationsActive ? 'true' : 'false' }}" 
                   aria-controls="menuOperations">
                    <i class="bi bi-kanban-fill"></i>
                    <span>Operations</span>
                    <i class="bi bi-chevron-down ms-auto dropdown-chevron"></i>
                </a>
                <div class="collapse {{ $isOperationsActive ? 'show' : '' }}" id="menuOperations">
                    <div class="sidebar-submenu">
                        <a href="{{ route('events.index') }}" class="sidebar-link {{ request()->routeIs('events.index') || request()->routeIs('events.show') || request()->routeIs('events.edit') ? 'active' : '' }}">
                            <i class="bi bi-calendar2-event-fill"></i>
                            <span>All Events</span>
                        </a>
                        <a href="{{ route('events.create') }}" class="sidebar-link {{ request()->routeIs('events.create') ? 'active' : '' }}">
                            <i class="bi bi-plus-circle-fill"></i>
                            <span>Register Event</span>
                        </a>
                        <a href="{{ route('orders.index') }}" class="sidebar-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                            <i class="bi bi-receipt"></i>
                            <span>Orders</span>
                        </a>
                        <a href="{{ route('quotations.index') }}" class="sidebar-link {{ request()->routeIs('quotations.*') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-text-fill"></i>
                            <span>Quotations</span>
                        </a>
                        <a href="{{ route('customers.index') }}" class="sidebar-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                            <i class="bi bi-people-fill"></i>
                            <span>Customers</span>
                        </a>
                        <a href="{{ route('calendar') }}" class="sidebar-link {{ request()->routeIs('calendar') ? 'active' : '' }}">
                            <i class="bi bi-calendar3"></i>
                            <span>Calendar</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 3. Finance (Dropdown) -->
            @php
                $isFinanceActive = request()->routeIs('payments.*') || request()->routeIs('cheques.*') || request()->routeIs('expenses.*') || request()->routeIs('cash-book') || request()->routeIs('bank-book') || request()->routeIs('bank-transactions.*');
            @endphp
            <div class="sidebar-dropdown">
                <a href="#menuFinance" 
                   class="sidebar-link sidebar-dropdown-toggle {{ $isFinanceActive ? 'active' : '' }}" 
                   data-bs-toggle="collapse" 
                   role="button" 
                   aria-expanded="{{ $isFinanceActive ? 'true' : 'false' }}" 
                   aria-controls="menuFinance">
                    <i class="bi bi-wallet2"></i>
                    <span>Finance</span>
                    <i class="bi bi-chevron-down ms-auto dropdown-chevron"></i>
                </a>
                <div class="collapse {{ $isFinanceActive ? 'show' : '' }}" id="menuFinance">
                    <div class="sidebar-submenu">
                        <a href="{{ route('payments.index') }}" class="sidebar-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                            <i class="bi bi-cash-stack"></i>
                            <span>Payments</span>
                        </a>
                        <a href="{{ route('cheques.index') }}" class="sidebar-link {{ request()->routeIs('cheques.*') ? 'active' : '' }}">
                            <i class="bi bi-card-heading"></i>
                            <span>Cheque Management</span>
                        </a>
                        <a href="{{ route('expenses.index') }}" class="sidebar-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                            <i class="bi bi-credit-card-2-front"></i>
                            <span>Expenses</span>
                        </a>
                        <a href="{{ route('cash-book') }}" class="sidebar-link {{ request()->routeIs('cash-book') ? 'active' : '' }}">
                            <i class="bi bi-journal-text"></i>
                            <span>Cash Book</span>
                        </a>
                        <a href="{{ route('bank-book') }}" class="sidebar-link {{ request()->routeIs('bank-book') ? 'active' : '' }}">
                            <i class="bi bi-bank2"></i>
                            <span>Bank Book</span>
                        </a>
                        <a href="{{ route('bank-transactions.index') }}" class="sidebar-link {{ request()->routeIs('bank-transactions.*') ? 'active' : '' }}">
                            <i class="bi bi-arrow-left-right"></i>
                            <span>Transactions</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 4. Reports (Dropdown) -->
            @php
                $isReportsActive = request()->routeIs('reports.*') || request()->routeIs('sms-logs.*');
            @endphp
            <div class="sidebar-dropdown">
                <a href="#menuReports" 
                   class="sidebar-link sidebar-dropdown-toggle {{ $isReportsActive ? 'active' : '' }}" 
                   data-bs-toggle="collapse" 
                   role="button" 
                   aria-expanded="{{ $isReportsActive ? 'true' : 'false' }}" 
                   aria-controls="menuReports">
                    <i class="bi bi-bar-chart-fill"></i>
                    <span>Reports</span>
                    <i class="bi bi-chevron-down ms-auto dropdown-chevron"></i>
                </a>
                <div class="collapse {{ $isReportsActive ? 'show' : '' }}" id="menuReports">
                    <div class="sidebar-submenu">
                        <a href="{{ route('reports.payments') }}" class="sidebar-link {{ request()->routeIs('reports.payments') ? 'active' : '' }}">
                            <i class="bi bi-receipt-cutoff"></i>
                            <span>Payments Report</span>
                        </a>
                        <a href="{{ route('reports.cheques') }}" class="sidebar-link {{ request()->routeIs('reports.cheques') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-check"></i>
                            <span>Cheques Report</span>
                        </a>
                        <a href="{{ route('reports.expenses') }}" class="sidebar-link {{ request()->routeIs('reports.expenses') ? 'active' : '' }}">
                            <i class="bi bi-wallet2"></i>
                            <span>Expenses Report</span>
                        </a>
                        <a href="{{ route('reports.transactions') }}" class="sidebar-link {{ request()->routeIs('reports.transactions') ? 'active' : '' }}">
                            <i class="bi bi-arrow-left-right"></i>
                            <span>Transactions Report</span>
                        </a>
                        <a href="{{ route('reports.order-pl') }}" class="sidebar-link {{ request()->routeIs('reports.order-pl') ? 'active' : '' }}">
                            <i class="bi bi-graph-up-arrow"></i>
                            <span>Order P&L</span>
                        </a>
                        <a href="{{ route('reports.monthly-pl') }}" class="sidebar-link {{ request()->routeIs('reports.monthly-pl') ? 'active' : '' }}">
                            <i class="bi bi-pie-chart-fill"></i>
                            <span>Monthly P&L</span>
                        </a>
                        <a href="{{ route('reports.customer-credit') }}" class="sidebar-link {{ request()->routeIs('reports.customer-credit') ? 'active' : '' }}">
                            <i class="bi bi-person-check-fill"></i>
                            <span>Credit Report</span>
                        </a>
                        <a href="{{ route('reports.order-history') }}" class="sidebar-link {{ request()->routeIs('reports.order-history') ? 'active' : '' }}">
                            <i class="bi bi-clock-history"></i>
                            <span>Order History</span>
                        </a>
                        <a href="{{ route('sms-logs.index') }}" class="sidebar-link {{ request()->routeIs('sms-logs.*') ? 'active' : '' }}">
                            <i class="bi bi-chat-dots-fill"></i>
                            <span>SMS Logs</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 5. Settings (Dropdown) -->
            @php
                $isSettingsActive = request()->routeIs('banks.*') || request()->routeIs('event-categories.*') || request()->routeIs('event-sub-categories.*') || request()->routeIs('expense-categories.*') || request()->routeIs('services.*') || request()->routeIs('users.*') || request()->routeIs('audit-logs.*');
            @endphp
            <div class="sidebar-dropdown">
                <a href="#menuSettings" 
                   class="sidebar-link sidebar-dropdown-toggle {{ $isSettingsActive ? 'active' : '' }}" 
                   data-bs-toggle="collapse" 
                   role="button" 
                   aria-expanded="{{ $isSettingsActive ? 'true' : 'false' }}" 
                   aria-controls="menuSettings">
                    <i class="bi bi-gear-fill"></i>
                    <span>Settings</span>
                    <i class="bi bi-chevron-down ms-auto dropdown-chevron"></i>
                </a>
                <div class="collapse {{ $isSettingsActive ? 'show' : '' }}" id="menuSettings">
                    <div class="sidebar-submenu">
                        <a href="{{ route('banks.index') }}" class="sidebar-link {{ request()->routeIs('banks.*') ? 'active' : '' }}">
                            <i class="bi bi-bank2"></i>
                            <span>Bank Accounts</span>
                        </a>
                        <a href="{{ route('services.index') }}" class="sidebar-link {{ request()->routeIs('services.*') ? 'active' : '' }}">
                            <i class="bi bi-stars"></i>
                            <span>Services</span>
                        </a>
                        <a href="{{ route('event-categories.index') }}" class="sidebar-link {{ request()->routeIs('event-categories.*') ? 'active' : '' }}">
                            <i class="bi bi-bookmark-fill"></i>
                            <span>Event Categories</span>
                        </a>
                        <a href="{{ route('event-sub-categories.index') }}" class="sidebar-link {{ request()->routeIs('event-sub-categories.*') ? 'active' : '' }}">
                            <i class="bi bi-collection-fill"></i>
                            <span>Sub-Categories</span>
                        </a>
                        <a href="{{ route('expense-categories.index') }}" class="sidebar-link {{ request()->routeIs('expense-categories.*') ? 'active' : '' }}">
                            <i class="bi bi-tags-fill"></i>
                            <span>Expense Categories</span>
                        </a>
                        <!-- <a href="{{ route('audit-logs.index') }}" class="sidebar-link {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}">
                            <i class="bi bi-shield-check"></i>
                            <span>Audit Logs</span>
                        </a> -->
                        @if((Auth::user()->role ?? '') === 'Admin')
                        <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="bi bi-person-gear"></i>
                            <span>Users & Roles</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Topbar -->
    <div class="topbar">
        <div class="d-flex align-items-center gap-2 gap-md-3">
            <button class="topbar-btn border-0" onclick="toggleSidebar()" title="Toggle Navigation" aria-label="Toggle navigation">
                <i class="bi bi-list fs-5"></i>
            </button>
            <h6 class="page-title text-truncate mb-0" style="max-width: clamp(140px, 40vw, 360px);">@yield('page-title', 'Dashboard')</h6>
        </div>
        <div class="topbar-right">
            <!-- Notifications -->
            <a href="{{ route('notifications.index') }}" class="topbar-btn" title="Notifications">
                <i class="bi bi-bell-fill"></i>
                @php
                    $notifCount = \App\Models\Event::whereDate('event_date', \Carbon\Carbon::today()->addDays(7))->where('notification_7_sent', false)->where('status', '!=', 'cancelled')->count()
                        + \App\Models\Event::whereDate('event_date', \Carbon\Carbon::today()->addDays(3))->where('notification_3_sent', false)->where('status', '!=', 'cancelled')->count();
                @endphp
                @if($notifCount > 0)
                    <span class="notification-badge">{{ $notifCount }}</span>
                @endif
            </a>
            <!-- New Event Quick Link -->
            <!-- <a href="{{ route('events.create') }}" class="topbar-btn" title="New Event">
                <i class="bi bi-plus-lg"></i>
            </a> -->

            <!-- User Profile Dropdown with Logout -->
            <div class="dropdown">
                <button class="d-flex align-items-center gap-2 border-0 bg-transparent p-0 dropdown-toggle text-decoration-none text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                    <div class="user-avatar shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="d-none d-sm-block text-start">
                        <div class="fw-bold text-white text-truncate" style="font-size: 13px; line-height: 1.1; max-width: 140px;">{{ Auth::user()->name ?? 'System Admin' }}</div>
                        <span class="badge" style="font-size: 9.5px; background: rgba(255, 255, 255, 0.16); color: #f4f4f5; border: 1px solid rgba(255, 255, 255, 0.22);">{{ Auth::user()->role ?? 'Staff' }}</span>
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border mt-2" style="min-width: 220px; border-radius: 14px;">
                    <li class="px-3 py-2 border-bottom bg-light rounded-top">
                        <div class="fw-bold text-dark small">{{ Auth::user()->name ?? 'Administrator' }}</div>
                        <div class="text-muted" style="font-size: 11px;">{{ Auth::user()->email ?? 'admin@eventpro.com' }}</div>
                    </li>
                    @if((Auth::user()->role ?? '') === 'Admin')
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('users.index') }}">
                            <i class="bi bi-people-fill me-2 text-primary"></i> System Users
                        </a>
                    </li>
                    @endif
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('events.index') }}">
                            <i class="bi bi-calendar2-event me-2 text-primary"></i> My Events
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Log Out
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                {{ session('success') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
                <i class="bi bi-exclamation-circle-fill"></i>
                {{ session('error') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
                <i class="bi bi-info-circle-fill"></i>
                {{ session('info') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Universal Delete Confirmation & Payment Warning Modal -->
    <div class="modal fade" id="universalDeleteModal" tabindex="-1" aria-hidden="true" style="z-index: 1065;">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-body p-4 text-center">
                    <div id="universalDeleteIconBox" class="mx-auto d-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 60px; height: 60px; background: #fee2e2; color: #dc2626; font-size: 26px;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2" id="universalDeleteTitle">Confirm Deletion</h5>
                    <div class="text-secondary small mb-3" id="universalDeleteMessage">Are you sure you want to delete this record? This action cannot be undone.</div>
                    
                    <!-- Payment Alert Box (Shown only if payments exist) -->
                    <div id="universalDeletePaymentAlert" class="alert alert-danger border-0 d-none text-start p-3 mb-0 rounded-3">
                        <div class="d-flex align-items-start gap-2.5">
                            <i class="bi bi-shield-slash-fill fs-4 text-danger flex-shrink-0 mt-0.5"></i>
                            <div>
                                <strong class="d-block text-danger mb-1" style="font-size: 13px;">Deletion Blocked — Payments Exist!</strong>
                                <span class="small text-danger-emphasis d-block" id="universalDeletePaymentText" style="line-height: 1.45;"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top p-3 px-4 d-flex justify-content-end gap-2" id="universalDeleteFooter">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal" id="universalDeleteCancelBtn">Cancel</button>
                    <button type="button" class="btn btn-danger px-4 fw-semibold" id="universalDeleteConfirmBtn">
                        <i class="bi bi-trash3 me-1"></i> Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery (Needed for Select2 and rich select interaction) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Select2 JS (Searchable Dropdowns) -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

    <script>
        // Global Select2 Searchable Dropdown Initializer
        window.initSearchableSelects = function (container) {
            if (typeof jQuery === 'undefined' || typeof jQuery.fn.select2 === 'undefined') return;
            const $root = container ? jQuery(container) : jQuery(document);

            $root.find('select.form-select, select.searchable-select, select.select2').each(function () {
                const $select = jQuery(this);
                // Skip if explicitly opted out or already initialized
                if ($select.hasClass('no-search') || $select.data('no-search') !== undefined) return;
                if ($select.hasClass('select2-hidden-accessible')) return;

                const firstOpt = $select.find('option').first();
                const placeholderText = $select.data('placeholder') || (firstOpt.val() === '' ? firstOpt.text() : 'Type to search...');
                const inModal = $select.closest('.modal').length > 0;

                $select.select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: placeholderText,
                    allowClear: !$select.prop('required') && firstOpt.val() === '',
                    dropdownParent: inModal ? $select.closest('.modal') : jQuery(document.body)
                });

                // Ensure native change event and inline onchange triggers on select2 selection
                $select.on('select2:select select2:unselect select2:clear', function () {
                    this.dispatchEvent(new Event('change', { bubbles: true }));
                });
            });
        };

        // Initialize on DOM load
        document.addEventListener('DOMContentLoaded', function () {
            if (window.initSearchableSelects) {
                window.initSearchableSelects();
            }

            // MutationObserver to auto-sync Select2 if options or selects are added dynamically
            if (window.MutationObserver) {
                const observer = new MutationObserver(function (mutations) {
                    let shouldInit = false;
                    mutations.forEach(function (mutation) {
                        if (mutation.type === 'childList') {
                            const target = mutation.target;
                            // If options inside an existing Select2 changed, notify Select2
                            if (target && target.tagName === 'SELECT' && typeof jQuery !== 'undefined' && jQuery(target).hasClass('select2-hidden-accessible')) {
                                jQuery(target).trigger('change.select2');
                            }
                            // If new DOM elements were added, check if new selects need initialization
                            mutation.addedNodes.forEach(function (node) {
                                if (node.nodeType === 1) {
                                    if (node.tagName === 'SELECT' && (node.classList.contains('form-select') || node.classList.contains('searchable-select'))) {
                                        shouldInit = true;
                                    } else if (node.querySelectorAll && node.querySelectorAll('select.form-select, select.searchable-select').length > 0) {
                                        shouldInit = true;
                                    }
                                }
                            });
                        }
                    });
                    if (shouldInit && window.initSearchableSelects) {
                        window.initSearchableSelects();
                    }
                });

                observer.observe(document.body, { childList: true, subtree: true });
            }
        });

        // Re-initialize whenever Bootstrap modals open
        document.addEventListener('shown.bs.modal', function (e) {
            if (window.initSearchableSelects) {
                window.initSearchableSelects(e.target);
            }
        });
        // Sidebar toggle (desktop slide left + mobile/tablet offcanvas drawer)
        function toggleSidebar() {
            if (window.innerWidth <= 991.98) {
                document.body.classList.toggle('sidebar-mobile-open');
                document.getElementById('sidebar').classList.toggle('show');
            } else {
                document.body.classList.toggle('sidebar-collapsed');
                const isCollapsed = document.body.classList.contains('sidebar-collapsed');
                localStorage.setItem('sidebar_collapsed', isCollapsed ? '1' : '0');

                // Trigger window resize event so charts, calendar, and tables adapt immediately
                setTimeout(() => {
                    window.dispatchEvent(new Event('resize'));
                }, 300);
            }
        }

        function closeMobileSidebar() {
            document.body.classList.remove('sidebar-mobile-open');
            const sb = document.getElementById('sidebar');
            if (sb) sb.classList.remove('show');
        }

        // Restore sidebar desktop preference & handle mobile link clicks
        document.addEventListener('DOMContentLoaded', function () {
            if (window.innerWidth > 991.98) {
                if (localStorage.getItem('sidebar_collapsed') === '1') {
                    document.body.classList.add('sidebar-collapsed');
                }
            }

            // Auto close mobile/tablet sidebar when clicking actual navigation links (excluding dropdown toggles)
            document.querySelectorAll('.sidebar-link:not(.sidebar-dropdown-toggle)').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 991.98) {
                        closeMobileSidebar();
                    }
                });
            });
        });

        // Handle window resize between mobile/tablet and desktop
        window.addEventListener('resize', function () {
            if (window.innerWidth > 991.98) {
                closeMobileSidebar();
            }
        });

        // CSRF setup for AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Auto-dismiss temporary flash alerts (do not dismiss modal alerts or permanent messages)
        setTimeout(() => {
            document.querySelectorAll('.alert.alert-dismissible:not(.alert-permanent):not(.modal .alert):not(#categoryModalAlert):not(#quickCustomerAlert):not(#quickBankModalAlert)').forEach(alert => {
                try {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    if (bsAlert) {
                        bsAlert.close();
                    }
                } catch (e) {
                    // Ignore if already disposed
                }
            });
        }, 5000);

        // Session Inactivity Auto-Logout Tracker
        (function() {
            const sessionLifetimeMs = {{ ((int) config('session.lifetime', 120)) * 60 * 1000 }};
            let idleTimer;

            function resetIdleTimer() {
                clearTimeout(idleTimer);
                idleTimer = setTimeout(() => {
                    window.location.href = "{{ route('login') }}";
                }, sessionLifetimeMs);
            }

            ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(evt => {
                document.addEventListener(evt, resetIdleTimer, { passive: true });
            });

            resetIdleTimer();
        })();

        // Universal Delete & Payment Block Handler
        window.handleDeleteConfirmation = function (event, formEl, config) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            const modalEl = document.getElementById('universalDeleteModal');
            if (!modalEl || typeof bootstrap === 'undefined') {
                if (confirm('Are you sure you want to delete this record?')) formEl.submit();
                return;
            }

            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            const titleEl = document.getElementById('universalDeleteTitle');
            const msgEl = document.getElementById('universalDeleteMessage');
            const iconBox = document.getElementById('universalDeleteIconBox');
            const paymentAlert = document.getElementById('universalDeletePaymentAlert');
            const paymentText = document.getElementById('universalDeletePaymentText');
            const confirmBtn = document.getElementById('universalDeleteConfirmBtn');
            const cancelBtn = document.getElementById('universalDeleteCancelBtn');

            const type = config.type || 'Record';
            const title = config.title || '';
            const hasPayments = !!config.hasPayments;
            const paymentAmount = config.paymentAmount || 0;

            if (hasPayments) {
                iconBox.style.background = '#fef2f2';
                iconBox.style.color = '#b91c1c';
                iconBox.innerHTML = '<i class="bi bi-shield-slash-fill"></i>';
                titleEl.innerText = `Cannot Delete ${type}`;
                msgEl.innerText = `This ${type.toLowerCase()} has settled payment records. Deleting it is strictly prevented to protect accounting integrity.`;
                paymentAlert.classList.remove('d-none');
                paymentText.innerHTML = `Payments totaling <strong>Rs. ${parseFloat(paymentAmount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong> have already been paid towards this record. You cannot delete it while payments are present.`;
                confirmBtn.classList.add('d-none');
                cancelBtn.innerText = 'Understood & Close';
                cancelBtn.className = 'btn btn-secondary px-4 fw-semibold';
            } else {
                iconBox.style.background = '#fee2e2';
                iconBox.style.color = '#dc2626';
                iconBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i>';
                titleEl.innerText = `Delete ${type}?`;
                let confirmMessage = config.customMessage;
                if (!confirmMessage) {
                    confirmMessage = `Are you sure you want to permanently delete <strong>${title || ('this ' + type.toLowerCase())}</strong>?`;
                    if (config.extraNote) {
                        confirmMessage += `<div class="mt-2 p-2 bg-danger-subtle text-danger rounded border border-danger-subtle small fw-semibold"><i class="bi bi-exclamation-circle-fill me-1"></i>${config.extraNote}</div>`;
                    } else {
                        confirmMessage += ` This action cannot be undone.`;
                    }
                }
                msgEl.innerHTML = confirmMessage;
                paymentAlert.classList.add('d-none');
                confirmBtn.classList.remove('d-none');
                confirmBtn.innerText = 'Yes, Delete';
                confirmBtn.className = 'btn btn-danger px-4 fw-semibold';
                cancelBtn.innerText = 'No, Cancel';
                cancelBtn.className = 'btn btn-outline-secondary px-3';

                confirmBtn.onclick = function () {
                    confirmBtn.disabled = true;
                    confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Deleting...';
                    formEl.submit();
                };
            }

            modal.show();
        };
    </script>

    @stack('scripts')
    @yield('scripts')
</body>

</html>