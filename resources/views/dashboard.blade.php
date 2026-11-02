@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('styles')
    <style>
        /* === DASHBOARD HERO CARD === */
        .welcome-hero-card {
            background: linear-gradient(135deg, #2b2a2a 0%, #3a3838 45%, #484646 100%);
            border-radius: 18px;
            padding: clamp(18px, 2.4vw, 26px);
            color: #ffffff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(43, 42, 42, 0.18), 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .welcome-hero-card::after {
            content: '';
            position: absolute;
            right: -60px;
            top: -60px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .welcome-hero-card::before {
            content: '';
            position: absolute;
            right: 180px;
            bottom: -80px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
            pointer-events: none;
        }

        .live-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            letter-spacing: 0.5px;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .live-dot {
            width: 7px;
            height: 7px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 8px #10b981;
            animation: pulse-dot 1.8s infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.3); opacity: 0.6; }
        }

        .date-badge {
            display: inline-flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.12);
            color: #e4e4e7;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .hero-greeting {
            font-size: clamp(1.2rem, 1.8vw, 1.55rem);
            font-weight: 800;
            letter-spacing: -0.3px;
            color: #ffffff;
        }

        .hero-name {
            color: #f4f4f5;
            background: linear-gradient(135deg, #ffffff 0%, #d4d4d8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtext {
            color: #d1cfcf;
            font-size: clamp(0.85rem, 1vw, 0.92rem);
            font-weight: 400;
        }

        .btn-hero-primary {
            background: #ffffff;
            color: #27272a !important;
            border: none;
            font-weight: 700;
            font-size: 13px;
            padding: 9px 16px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.25s ease;
        }

        .btn-hero-primary:hover {
            background: #f4f4f5;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
            color: #000000 !important;
        }

        .btn-hero-secondary {
            background: rgba(255, 255, 255, 0.14);
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.25);
            font-weight: 600;
            font-size: 13px;
            padding: 9px 16px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.25s ease;
        }

        .btn-hero-secondary:hover {
            background: rgba(255, 255, 255, 0.22);
            color: #ffffff !important;
            transform: translateY(-2px);
        }

        /* === EYE-CATCHING STAT CARDS === */
        .lively-stat-card {
            background: #ffffff;
            border-radius: 16px;
            padding: clamp(15px, 1.3vw, 20px);
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.03);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .lively-stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            opacity: 0.9;
        }

        .lively-stat-card.card-blue::before { background: linear-gradient(90deg, #0284c7, #38bdf8); }
        .lively-stat-card.card-purple::before { background: linear-gradient(90deg, #7c3aed, #a855f7); }
        .lively-stat-card.card-green::before { background: linear-gradient(90deg, #059669, #10b981); }
        .lively-stat-card.card-amber::before { background: linear-gradient(90deg, #ea580c, #f59e0b); }

        .lively-stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.08);
            border-color: rgba(0, 0, 0, 0.08);
            color: inherit;
        }

        .lively-stat-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .lively-icon-box {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #ffffff;
            flex-shrink: 0;
            transition: transform 0.3s ease;
        }

        .lively-stat-card:hover .lively-icon-box {
            transform: scale(1.08) rotate(3deg);
        }

        .lively-icon-box.icon-blue {
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            box-shadow: 0 8px 18px rgba(2, 132, 199, 0.3);
        }

        .lively-icon-box.icon-purple {
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            box-shadow: 0 8px 18px rgba(124, 58, 237, 0.3);
        }

        .lively-icon-box.icon-green {
            background: linear-gradient(135deg, #059669, #10b981);
            box-shadow: 0 8px 18px rgba(5, 150, 105, 0.3);
        }

        .lively-icon-box.icon-amber {
            background: linear-gradient(135deg, #ea580c, #f59e0b);
            box-shadow: 0 8px 18px rgba(234, 88, 12, 0.3);
        }

        .lively-arrow-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 12px;
            transition: all 0.2s ease;
        }

        .lively-stat-card:hover .lively-arrow-btn {
            background: #27272a;
            color: #ffffff;
            transform: translate(2px, -2px);
        }

        .lively-stat-label {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .lively-stat-value {
            font-size: clamp(1.35rem, 1.6vw, 1.85rem);
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -0.5px;
            line-height: 1.15;
            margin-bottom: 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .lively-stat-footer {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: #94a3b8;
            font-weight: 500;
            padding-top: 8px;
            border-top: 1px dashed #f1f5f9;
        }

        /* === DASHBOARD CARDS === */
        .dash-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 2px 14px rgba(0, 0, 0, 0.03);
            transition: box-shadow 0.25s ease;
            overflow: hidden;
        }

        .dash-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        .dash-card-header {
            padding: 18px 22px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
        }

        .dash-card-title {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dash-card-body {
            padding: 22px;
        }

        /* Metric Chips */
        .metric-chip {
            padding: 6px 12px;
            border-radius: 12px;
            display: inline-flex;
            flex-direction: column;
            gap: 2px;
        }

        .metric-chip.chip-revenue {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
        }

        .metric-chip.chip-revenue .chip-title { color: #047857; font-size: 10.5px; font-weight: 700; }
        .metric-chip.chip-revenue .chip-val { color: #065f46; font-size: 13.5px; font-weight: 800; }

        .metric-chip.chip-profit {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .metric-chip.chip-profit .chip-title { color: #475569; font-size: 10.5px; font-weight: 700; }
        .metric-chip.chip-profit .chip-val { color: #0f172a; font-size: 13.5px; font-weight: 800; }

        /* Monthly Highlights Tile */
        .financial-tile {
            border-radius: 14px;
            padding: 16px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: all 0.2s ease;
        }

        .financial-tile.tile-income {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 1px solid #a7f3d0;
        }

        .financial-tile.tile-expense {
            background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%);
            border: 1px solid #fecdd3;
        }

        .tile-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .tile-income .tile-icon {
            background: #10b981;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .tile-expense .tile-icon {
            background: #f43f5e;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(244, 63, 94, 0.3);
        }

        .tile-info {
            display: flex;
            flex-direction: column;
        }

        .tile-info span {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .tile-income .tile-info span { color: #047857; }
        .tile-expense .tile-info span { color: #be123c; }

        .tile-info h5 {
            font-size: 1.25rem;
            font-weight: 800;
            margin: 0;
            line-height: 1.1;
        }

        .tile-income .tile-info h5 { color: #064e3b; }
        .tile-expense .tile-info h5 { color: #881337; }

        /* Net Margin Pill Banner */
        .margin-banner {
            background: #f8fafc;
            border: 1.5px dashed #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* === FULL WIDTH CALENDAR & EVENT DETAILS STYLING === */
        .dash-calendar-box {
            background: #ffffff;
            border-radius: 14px;
            padding: 18px;
            border: 1px solid #edf2f7;
        }

        #dashCalendar .fc-toolbar-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: #1e293b;
        }

        #dashCalendar .fc-toolbar.fc-header-toolbar {
            margin-bottom: 0.75rem;
            gap: 6px;
        }

        #dashCalendar .fc-button-primary {
            background: linear-gradient(135deg, #484646 0%, #3a3838 100%) !important;
            border: 1px solid #3a3838 !important;
            font-weight: 700;
            text-transform: capitalize;
            border-radius: 8px;
            padding: 4px 12px;
            font-size: 12px;
            box-shadow: 0 3px 10px rgba(58, 56, 56, 0.28);
        }

        #dashCalendar .fc-button-primary:hover,
        #dashCalendar .fc-button-primary:focus {
            background: linear-gradient(135deg, #3a3838 0%, #252424 100%) !important;
            border-color: #252424 !important;
        }

        #dashCalendar .fc-theme-standard td,
        #dashCalendar .fc-theme-standard th {
            border-color: #f1f5f9;
        }

        #dashCalendar .fc-col-header-cell-cushion {
            font-size: .72rem;
            font-weight: 700;
            color: #64748b;
            padding: 6px 0;
            text-transform: uppercase;
            text-decoration: none;
        }

        #dashCalendar .fc-daygrid-day-number {
            font-size: .82rem;
            font-weight: 700;
            color: #374151;
            padding: 3px 5px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s ease;
        }

        #dashCalendar .fc-daygrid-day:hover .fc-daygrid-day-number {
            background: #f1f5f9;
        }

        #dashCalendar .fc-day-today .fc-daygrid-day-number {
            background: #3a3838;
            color: #fff !important;
        }

        #dashCalendar .fc-daygrid-event {
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
            margin: 1px 0 !important;
            box-shadow: none !important;
            display: flex !important;
            justify-content: center !important;
        }

        #dashCalendar .fc-daygrid-event:hover {
            background: transparent !important;
            transform: none !important;
        }

        .day-count-icon-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 2.5px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 800;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            line-height: 1.2;
        }

        .day-count-icon-badge:hover {
            transform: translateY(-1px) scale(1.1);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
        }

        .day-count-icon-badge i {
            font-size: 11px;
        }

        /* Right Side: Month Events Panel with Scrollbar */
        .dash-events-panel {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #edf2f7;
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
        }

        .dash-events-panel-header {
            padding: 14px 18px 12px;
            border-bottom: 1px solid #f1f5f9;
            background: #ffffff;
        }

        .dash-events-scrollable {
            padding: 16px;
            overflow-y: auto;
            max-height: 540px;
            scrollbar-width: thin;
            scrollbar-color: #94a3b8 #f1f5f9;
            background: #f8fafc;
        }

        .dash-events-scrollable::-webkit-scrollbar {
            width: 8px;
        }

        .dash-events-scrollable::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .dash-events-scrollable::-webkit-scrollbar-thumb {
            background-color: #94a3b8;
            border-radius: 10px;
            border: 2px solid #f1f5f9;
        }

        .dash-events-scrollable::-webkit-scrollbar-thumb:hover {
            background-color: #64748b;
        }

        .dash-event-card-item {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 14px;
            padding: 16px 18px;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06), 0 1px 3px rgba(15, 23, 42, 0.04);
            width: 100%;
        }

        .dash-event-card-item:hover {
            border-color: #cbd5e1;
            transform: translateY(-3px);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.1), 0 3px 8px rgba(15, 23, 42, 0.05);
        }

        .dash-event-card-item.active-highlight {
            border-color: #3a3838;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(58, 56, 56, 0.15), 0 8px 20px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        /* Dashboard Action Buttons (Matching Tritcal Style - #3a3838) */
        .dash-action-btn {
            background: linear-gradient(135deg, #484646 0%, #3a3838 100%) !important;
            border: 1px solid #3a3838 !important;
            color: #ffffff !important;
            font-size: 11.5px;
            font-weight: 600;
            padding: 4px 11px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none !important;
            transition: var(--transition);
            box-shadow: 0 2px 6px rgba(58, 56, 56, 0.2);
            cursor: pointer;
            line-height: 1.4;
        }

        .dash-action-btn:hover,
        .dash-action-btn:focus {
            background: linear-gradient(135deg, #3a3838 0%, #252424 100%) !important;
            border-color: #252424 !important;
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(58, 56, 56, 0.28);
        }

        .dash-action-btn:active {
            transform: translateY(0);
            box-shadow: 0 1px 3px rgba(58, 56, 56, 0.2);
        }

        .dash-action-btn i {
            font-size: 12px;
        }

        /* Table Action Buttons (#3a3838) */
        .btn-dash-table-action {
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 13px;
            text-decoration: none !important;
            background: linear-gradient(135deg, #484646 0%, #3a3838 100%) !important;
            color: #ffffff !important;
            border: 1px solid #3a3838 !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 1px 3px rgba(58, 56, 56, 0.2);
            cursor: pointer;
        }

        .btn-dash-table-action:hover {
            background: linear-gradient(135deg, #3a3838 0%, #252424 100%) !important;
            border-color: #252424 !important;
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(58, 56, 56, 0.35);
        }

        .btn-dash-table-action:active {
            transform: translateY(0);
        }

        /* Status Pills */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .status-pill.status-pending { background: #fef3c7; color: #b45309; }
        .status-pill.status-confirmed { background: #dbeafe; color: #1d4ed8; }
        .status-pill.status-completed { background: #d1fae5; color: #047857; }
        .status-pill.status-cancelled { background: #fee2e2; color: #b91c1c; }

        .pill-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .status-pending .pill-dot { background: #d97706; }
        .status-confirmed .pill-dot { background: #2563eb; }
        .status-completed .pill-dot { background: #059669; }
        .status-cancelled .pill-dot { background: #dc2626; }

        .avatar-circle {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            color: #ffffff;
            flex-shrink: 0;
        }

        .date-filter-chip {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            border-radius: 20px;
            padding: 2px 10px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .date-filter-chip:hover {
            background: #fecaca;
        }
    </style>
@endsection

@section('content')
    <!-- === HERO BANNER === -->
    <div class="welcome-hero-card mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-7">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="live-status-pill">
                        <span class="live-dot"></span> Live Operations
                    </span>
                    <span class="date-badge">
                        <i class="bi bi-calendar3 me-1"></i> {{ now()->format('l, F j, Y') }}
                    </span>
                    @if($notificationCount > 0)
                        <a href="{{ route('notifications.index') }}" class="badge bg-danger text-white text-decoration-none py-1 px-2 rounded-pill shadow-sm" style="font-size: 11px;">
                            <i class="bi bi-bell-fill me-1"></i> {{ $notificationCount }} Reminders
                        </a>
                    @endif
                </div>
                <h3 class="hero-greeting mb-1">
                    Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, 
                    <span class="hero-name">{{ Auth::user()->name ?? 'Manager' }}</span> 👋
                </h3>
                <p class="hero-subtext mb-0">
                    Your event business is running smoothly with <strong class="text-white">{{ $upcomingEvents->count() }} upcoming events</strong> in the next 30 days and <strong class="text-white">{{ $totalOrders }} orders</strong> tracked.
                </p>
            </div>
            <div class="col-lg-5 text-lg-end">
                <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                    <a href="{{ route('events.create') }}" class="btn-hero-secondary shadow-sm text-white" target="_blank">
                        <i class="bi bi-calendar-plus-fill text-white"></i> Register Event
                    </a>
                    <a href="{{ route('quotations.create') }}" class="btn-hero-secondary shadow-sm" target="_blank">
                        <i class="bi bi-file-earmark-plus text-white"></i> New Quotation
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- === EYE-CATCHING KPI CARDS === -->
    <div class="row g-3 mb-4">
        <!-- Customers -->
        <div class="col-12 col-sm-6 col-lg-6 col-xl-3">
            <a class="lively-stat-card card-blue" href="{{ route('customers.index') }}">
                <div>
                    <div class="lively-stat-header">
                        <div class="lively-icon-box icon-blue">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="lively-arrow-btn">
                            <i class="bi bi-arrow-up-right"></i>
                        </div>
                    </div>
                    <div class="lively-stat-label">Total Customers</div>
                    <div class="lively-stat-value">{{ $totalCustomers }}</div>
                </div>
                <div class="lively-stat-footer">
                    <i class="bi bi-check-circle-fill text-primary"></i> Verified client directory
                </div>
            </a>
        </div>

        <!-- Events -->
        <div class="col-12 col-sm-6 col-lg-6 col-xl-3">
            <a class="lively-stat-card card-purple" href="{{ route('events.index') }}">
                <div>
                    <div class="lively-stat-header">
                        <div class="lively-icon-box icon-purple">
                            <i class="bi bi-calendar-event-fill"></i>
                        </div>
                        <div class="lively-arrow-btn">
                            <i class="bi bi-arrow-up-right"></i>
                        </div>
                    </div>
                    <div class="lively-stat-label">Total Events</div>
                    <div class="lively-stat-value">{{ $totalEvents }}</div>
                </div>
                <div class="lively-stat-footer">
                    <i class="bi bi-clock-history text-purple"></i> {{ $upcomingEvents->count() }} active next 30 days
                </div>
            </a>
        </div>

        <!-- Orders -->
        <div class="col-12 col-sm-6 col-lg-6 col-xl-3">
            <a class="lively-stat-card card-green" href="{{ route('orders.index') }}">
                <div>
                    <div class="lively-stat-header">
                        <div class="lively-icon-box icon-green">
                            <i class="bi bi-receipt-cutoff"></i>
                        </div>
                        <div class="lively-arrow-btn">
                            <i class="bi bi-arrow-up-right"></i>
                        </div>
                    </div>
                    <div class="lively-stat-label">Orders & Bookings</div>
                    <div class="lively-stat-value">{{ $totalOrders }}</div>
                </div>
                <div class="lively-stat-footer">
                    <i class="bi bi-bag-check-fill text-success"></i> Lifetime confirmed orders
                </div>
            </a>
        </div>

        <!-- Outstanding Balance -->
        <div class="col-12 col-sm-6 col-lg-6 col-xl-3">
            <a class="lively-stat-card card-amber" href="{{ route('reports.customer-credit') }}">
                <div>
                    <div class="lively-stat-header">
                        <div class="lively-icon-box icon-amber">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div class="lively-arrow-btn">
                            <i class="bi bi-arrow-up-right"></i>
                        </div>
                    </div>
                    <div class="lively-stat-label">Outstanding Balance</div>
                    <div class="lively-stat-value" style="font-size: 1.55rem;">Rs. {{ number_format($pendingPayments, 2) }}</div>
                </div>
                <div class="lively-stat-footer">
                    <i class="bi bi-exclamation-circle-fill text-warning"></i> Pending collections
                </div>
            </a>
        </div>
    </div>

    <!-- === FULL WIDTH EVENT CALENDAR & MONTH EVENTS DETAILS === -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="dash-card">
                <div class="dash-card-header flex-wrap gap-2">
                    <div>
                        <h6 class="dash-card-title">
                            <i class="bi bi-calendar3-event-fill text-primary"></i> Event Calendar & Monthly Schedule
                        </h6>
                        <span class="small text-muted">Browse month on the left; scheduled events and customer details update automatically on the right</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="status-pill status-pending"><span class="pill-dot"></span> Pending</span>
                        <span class="status-pill status-confirmed"><span class="pill-dot"></span> Confirmed</span>
                        <span class="status-pill status-completed"><span class="pill-dot"></span> Completed</span>
                        <a href="{{ route('calendar') }}" class="dash-action-btn ms-2 py-1 px-2.5" style="font-size: 11.5px;">
                            <i class="bi bi-arrows-fullscreen me-1"></i> Full Calendar View
                        </a>
                    </div>
                </div>
                <div class="p-3">
                    <div class="row g-3">
                        <!-- Left Side: Interactive Calendar -->
                        <div class="col-12 col-xl-5">
                            <div class="dash-calendar-box">
                                <div id="dashCalendar" aria-label="Events calendar"></div>
                            </div>
                        </div>

                        <!-- Right Side: Month Event Details with Scrollbar -->
                        <div class="col-12 col-xl-7">
                            <div class="dash-events-panel">
                                <!-- Panel Header -->
                                <div class="dash-events-panel-header">
                                    <div class="d-flex align-items-center justify-content-between mb-1 flex-wrap gap-2">
                                        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 14.5px;">
                                            <i class="bi bi-calendar-check text-primary"></i>
                                            <span id="dashActiveMonthTitle">Current Month</span>
                                        </h6>
                                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                            <button type="button" class="dash-action-btn py-0.5 px-2" id="dashBtnFilterToday" onclick="filterToToday()" style="font-size: 11px;">
                                                <i class="bi bi-clock me-1"></i> Today
                                            </button>
                                            <button type="button" class="dash-action-btn py-0.5 px-2" id="dashBtnFilterAll" onclick="resetDashDateFilter()" style="font-size: 11px; display: none;">
                                                <i class="bi bi-calendar3 me-1"></i> All Month
                                            </button>
                                            <span class="badge bg-dark text-white rounded-pill px-2.5 py-1" id="dashEventsCountBadge" style="font-size: 11px;">0 Events</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mt-1 flex-wrap gap-1">
                                        <small class="text-muted" id="dashActiveMonthSubtitle">Events in this month</small>
                                        <div id="dashDateFilterBadge" style="display: none;">
                                            <span class="date-filter-chip" onclick="resetDashDateFilter()" title="Click to view all events in this month">
                                                <span id="dashDateFilterText"></span> <i class="bi bi-x-circle-fill ms-1"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Search / Filter Input -->
                                <div class="p-2 border-bottom bg-light">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                                        <input type="text" id="dashFilterInput" class="form-control border-start-0" placeholder="Filter by event, client, venue, package...">
                                    </div>
                                </div>

                                <!-- Scrollable Events List (2 cards per row on desktop/laptop/tab, 1 on mobile) -->
                                <div class="dash-events-scrollable" id="dashEventsListContainer">
                                    <div class="text-center py-5 text-muted">
                                        <div class="spinner-border spinner-border-sm text-secondary mb-2" role="status"></div>
                                        <p class="small mb-0">Loading calendar events...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- === CHARTS & THIS MONTH ROW === -->
    <div class="row g-3 mb-4">
        <!-- Revenue & Expenses Line Chart -->
        <div class="col-12 col-xl-7">
            <div class="dash-card h-100">
                <div class="dash-card-header flex-wrap gap-2">
                    <div>
                        <h6 class="dash-card-title">
                            <i class="bi bi-graph-up-arrow text-primary"></i> Financial Trends & Cashflow
                        </h6>
                        <span class="small text-muted">Revenue vs expenses for the past 6 months</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="metric-chip chip-revenue">
                            <span class="chip-title">6M REVENUE</span>
                            <span class="chip-val">Rs. {{ number_format(collect($chartData)->sum('revenue'), 0) }}</span>
                        </div>
                        <div class="metric-chip chip-profit">
                            <span class="chip-title">6M PROFIT</span>
                            <span class="chip-val">Rs. {{ number_format(collect($chartData)->sum('profit'), 0) }}</span>
                        </div>
                    </div>
                </div>
                <div class="dash-card-body">
                    <div style="height: 320px;">
                        <canvas id="financeChart" aria-label="Revenue and expenses for the last six months"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- This Month Financial Health -->
        <div class="col-12 col-xl-5">
            <div class="dash-card h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="dash-card-header">
                        <h6 class="dash-card-title">
                            <i class="bi bi-pie-chart-fill text-success"></i> This Month's Performance
                        </h6>
                        <span class="badge bg-light text-dark border" style="font-size: 11px;">{{ now()->format('F Y') }}</span>
                    </div>
                    <div class="dash-card-body">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="financial-tile tile-income">
                                    <div class="tile-icon">
                                        <i class="bi bi-cash-stack"></i>
                                    </div>
                                    <div class="tile-info">
                                        <span>Collected</span>
                                        <h5>Rs. {{ number_format($monthlyRevenue, 0) }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="financial-tile tile-expense">
                                    <div class="tile-icon">
                                        <i class="bi bi-credit-card-2-front"></i>
                                    </div>
                                    <div class="tile-info">
                                        <span>Expenses</span>
                                        <h5>Rs. {{ number_format($monthlyExpenses, 0) }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php
                            $monthNet = $monthlyRevenue - $monthlyExpenses;
                        @endphp
                        <div class="margin-banner mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi {{ $monthNet >= 0 ? 'bi-shield-check text-success' : 'bi-exclamation-triangle text-danger' }} fs-5"></i>
                                <div>
                                    <div class="fw-bold text-dark small">Net Operating Cashflow</div>
                                    <span class="text-muted" style="font-size: 11px;">Current month revenue minus expenses</span>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge {{ $monthNet >= 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} px-2 py-1" style="font-size: 13px; font-weight: 800;">
                                    {{ $monthNet >= 0 ? '+' : '' }}Rs. {{ number_format($monthNet, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-4 pb-3">
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                        <span class="text-muted small">Need detailed payment history?</span>
                        <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-secondary py-1 px-3" style="font-size: 12px; border-radius: 8px;">
                            View Payments <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- === TABLES ROW === -->
    <div class="row g-3">
        <!-- Upcoming Events Table -->
        <div class="col-12 col-xl-7">
            <div class="dash-card h-100">
                <div class="dash-card-header">
                    <h6 class="dash-card-title">
                        <i class="bi bi-stars text-warning"></i> Upcoming Events
                    </h6>
                    <a href="{{ route('events.index') }}" class="dash-action-btn" style="font-size: 11px; padding: 4px 10px;">
                        View All ({{ $totalEvents }}) <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">Event Details</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($upcomingEvents as $event)
                                <tr>
                                    <td class="ps-3 py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div>
                                                <a href="{{ route('events.show', $event) }}" class="fw-bold text-dark text-decoration-none">
                                                    {{ $event->event_name }}
                                                </a>
                                                <div class="small text-muted" style="font-size: 11.5px;">
                                                    @if($event->subCategory)
                                                        {{ $event->subCategory->name }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $event->customer?->name ?? '—' }}</div>
                                        <div class="text-muted small" style="font-size: 11px;">{{ $event->venue ?: 'Venue TBD' }}</div>
                                    </td>
                                    <td>
                                        <div class="d-inline-flex align-items-center gap-1 badge bg-light text-dark border px-2 py-1" style="font-size: 11.5px;">
                                            <i class="bi bi-calendar3 text-primary"></i> {{ $event->event_date?->format('M j, Y') }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-pill status-{{ $event->status }}">
                                            <span class="pill-dot"></span>
                                            {{ ucfirst($event->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3 text-nowrap">
                                        <div class="d-inline-flex align-items-center gap-1.5">
                                            <a href="{{ route('events.show', $event) }}" class="btn-action btn-action-view" title="View Event Details">
                                                <i class="bi bi-eye"></i>
                                            </a>&nbsp;
                                            <a href="{{ route('events.create-order', $event) }}" class="btn-action btn-action-edit" title="Order">
                                                <i class="bi bi-receipt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="bi bi-calendar-x fs-2 d-block mb-2 text-secondary"></i>
                                        No upcoming events scheduled in the next 30 days.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Payments Table -->
        <div class="col-12 col-xl-5">
            <div class="dash-card h-100">
                <div class="dash-card-header">
                    <h6 class="dash-card-title">
                        <i class="bi bi-shield-check text-success"></i> Recent Payments
                    </h6>
                    <a href="{{ route('payments.index') }}" class="dash-action-btn" style="font-size: 11px; padding: 4px 10px;">
                        All Payments <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">Customer & Ref</th>
                                <th>Date</th>
                                <th class="text-end pe-3">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $payment)
                                <tr>
                                    <td class="ps-3 py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-circle" style="background: linear-gradient(135deg, #10b981, #059669);">
                                                <i class="bi bi-check2"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $payment->customer?->name ?? 'Direct Payment' }}</div>
                                                <span class="badge bg-light text-secondary border" style="font-size: 10.5px;">{{ $payment->payment_no }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-dark small fw-medium">{{ $payment->payment_date?->format('M j, Y') }}</div>
                                        <span class="text-muted" style="font-size: 11px;">{{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'Cash')) }}</span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold" style="font-size: 13px;">
                                            Rs. {{ number_format($payment->amount, 2) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                        No recent payments recorded.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // === FINANCE CHART ===
        const financeData = @json($chartData);
        const ctx = document.getElementById('financeChart').getContext('2d');

        const revenueGradient = ctx.createLinearGradient(0, 0, 0, 320);
        revenueGradient.addColorStop(0, 'rgba(16, 185, 129, 0.28)');
        revenueGradient.addColorStop(1, 'rgba(16, 185, 129, 0.01)');

        const expenseGradient = ctx.createLinearGradient(0, 0, 0, 320);
        expenseGradient.addColorStop(0, 'rgba(239, 68, 68, 0.22)');
        expenseGradient.addColorStop(1, 'rgba(239, 68, 68, 0.01)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: financeData.map(item => item.month),
                datasets: [
                    {
                        label: 'Revenue',
                        data: financeData.map(item => item.revenue),
                        borderColor: '#10b981',
                        borderWidth: 2.5,
                        backgroundColor: revenueGradient,
                        fill: true,
                        tension: 0.38,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Expenses',
                        data: financeData.map(item => item.expenses),
                        borderColor: '#f43f5e',
                        borderWidth: 2.5,
                        backgroundColor: expenseGradient,
                        fill: true,
                        tension: 0.38,
                        pointBackgroundColor: '#f43f5e',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 8,
                            padding: 15,
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: '600' }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 10,
                        titleFont: { weight: '700', size: 13 },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                if (context.parsed.y !== null) {
                                    label += 'Rs. ' + context.parsed.y.toLocaleString('en-US', { minimumFractionDigits: 2 });
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.04)' },
                        ticks: {
                            font: { size: 11 },
                            callback: function(value) {
                                if (value >= 1000) return 'Rs. ' + (value / 1000) + 'k';
                                return 'Rs. ' + value;
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });

        // === DASHBOARD FULL-WIDTH CALENDAR WITH RIGHT-SIDE EVENTS PANEL ===
        let dashCalendarInstance = null;
        let dashAllEvents = [];
        let dashCurrentFilterDate = null;
        let dashCurrentSearchQuery = '';
        let dashFilterIsToday = false;

        document.addEventListener('DOMContentLoaded', () => {
            const calendarEl = document.getElementById('dashCalendar');
            dashCalendarInstance = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 'auto',
                events: function(fetchInfo, successCallback, failureCallback) {
                    const url = '{{ route('api.calendar.events') }}?start=' + encodeURIComponent(fetchInfo.startStr) + '&end=' + encodeURIComponent(fetchInfo.endStr);
                    fetch(url)
                        .then(res => res.json())
                        .then(data => {
                            dashAllEvents = data;

                            // Group events by day to calculate totals
                            const dayMap = {};
                            data.forEach(item => {
                                const dateKey = item.start ? item.start.split('T')[0] : '';
                                if (!dateKey) return;
                                if (!dayMap[dateKey]) {
                                    dayMap[dateKey] = {
                                        count: 0,
                                        statuses: []
                                    };
                                }
                                dayMap[dateKey].count++;
                                dayMap[dateKey].statuses.push(item.extendedProps?.status || 'pending');
                            });

                            // Create ONE summary event per date for the calendar cell
                            const summaryEvents = Object.keys(dayMap).map(dateKey => {
                                const info = dayMap[dateKey];
                                const count = info.count;
                                const label = count === 1 ? '1 Event' : `${count} Events`;

                                let bgColor = '#ecfdf5';
                                let textColor = '#047857';
                                let borderColor = '#a7f3d0';
                                let dotColor = '#10b981';

                                if (info.statuses.includes('pending')) {
                                    bgColor = '#fffbeb';
                                    textColor = '#b45309';
                                    borderColor = '#fde68a';
                                    dotColor = '#f59e0b';
                                } else if (info.statuses.includes('confirmed')) {
                                    bgColor = '#eff6ff';
                                    textColor = '#1d4ed8';
                                    borderColor = '#bfdbfe';
                                    dotColor = '#3b82f6';
                                }

                                return {
                                    id: 'dash-summary-' + dateKey,
                                    start: dateKey,
                                    allDay: true,
                                    title: label,
                                    extendedProps: {
                                        dateKey: dateKey,
                                        count: count,
                                        bgColor: bgColor,
                                        textColor: textColor,
                                        borderColor: borderColor,
                                        dotColor: dotColor
                                    }
                                };
                            });

                            successCallback(summaryEvents);
                            renderDashEvents();
                        })
                        .catch(err => {
                            console.error('Error fetching calendar events:', err);
                            failureCallback(err);
                        });
                },
                eventContent: function(arg) {
                    const props = arg.event.extendedProps || {};
                    const count = props.count || 1;
                    const tooltipText = count === 1 ? '1 Event scheduled' : `${count} Events scheduled`;
                    const bgColor = props.bgColor || '#eff6ff';
                    const textColor = props.textColor || '#1d4ed8';
                    const borderColor = props.borderColor || '#bfdbfe';
                    const dotColor = props.dotColor || '#3b82f6';

                    return {
                        html: `
                            <div class="day-count-icon-badge" title="${tooltipText}" style="background: ${bgColor}; color: ${textColor}; border: 1px solid ${borderColor};">
                                <i class="bi bi-calendar2-event-fill" style="color: ${dotColor};"></i>
                                <span>${count}</span>
                            </div>
                        `
                    };
                },
                customButtons: {
                    todayBtn: {
                        text: 'Today',
                        click: function() {
                            dashCalendarInstance.today();
                            filterToToday();
                        }
                    }
                },
                headerToolbar: {
                    left: 'prev,next todayBtn',
                    center: 'title',
                    right: '' // list button removed as requested!
                },
                datesSet(dateInfo) {
                    document.getElementById('dashActiveMonthTitle').innerText = dateInfo.view.title;
                    if (!dashFilterIsToday) {
                        dashCurrentFilterDate = null;
                        document.getElementById('dashDateFilterBadge').style.display = 'none';
                        const allBtn = document.getElementById('dashBtnFilterAll');
                        if (allBtn) allBtn.style.display = 'none';
                    }
                    setTimeout(renderDashEvents, 50);
                },
                eventClick(info) {
                    info.jsEvent.preventDefault();
                    const props = info.event.extendedProps || {};
                    const dateStr = props.dateKey || info.event.startStr.split('T')[0];
                    filterDashBySpecificDate(dateStr, false);
                },
                dateClick(info) {
                    filterDashBySpecificDate(info.dateStr, false);
                }
            });
            dashCalendarInstance.render();

            // Search filter listener
            document.getElementById('dashFilterInput').addEventListener('input', function(e) {
                dashCurrentSearchQuery = e.target.value.toLowerCase().trim();
                renderDashEvents();
            });
        });

        function getTodayDateString() {
            const now = new Date();
            const y = now.getFullYear();
            const m = String(now.getMonth() + 1).padStart(2, '0');
            const d = String(now.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }

        function filterToToday() {
            const todayStr = getTodayDateString();
            dashFilterIsToday = true;
            dashCurrentFilterDate = todayStr;
            const textEl = document.getElementById('dashDateFilterText');
            if (textEl) textEl.innerText = 'Today: ' + formatDashDate(todayStr);
            const badgeEl = document.getElementById('dashDateFilterBadge');
            if (badgeEl) badgeEl.style.display = 'inline-block';
            const allBtn = document.getElementById('dashBtnFilterAll');
            if (allBtn) allBtn.style.display = 'inline-block';
            renderDashEvents();
        }

        function filterDashBySpecificDate(dateStr, isToday = false) {
            dashFilterIsToday = isToday;
            dashCurrentFilterDate = dateStr;
            const textEl = document.getElementById('dashDateFilterText');
            if (textEl) textEl.innerText = (isToday ? 'Today: ' : 'Date: ') + formatDashDate(dateStr);
            const badgeEl = document.getElementById('dashDateFilterBadge');
            if (badgeEl) badgeEl.style.display = 'inline-block';
            const allBtn = document.getElementById('dashBtnFilterAll');
            if (allBtn) allBtn.style.display = 'inline-block';
            renderDashEvents();
        }

        function resetDashDateFilter() {
            dashFilterIsToday = false;
            dashCurrentFilterDate = null;
            const badgeEl = document.getElementById('dashDateFilterBadge');
            if (badgeEl) badgeEl.style.display = 'none';
            const allBtn = document.getElementById('dashBtnFilterAll');
            if (allBtn) allBtn.style.display = 'none';
            renderDashEvents();
        }

        function refreshDashEventsList() {
            if (!dashCalendarInstance) return;
            dashAllEvents = dashCalendarInstance.getEvents();
            renderDashEvents();
        }

        function renderDashEvents() {
            const container = document.getElementById('dashEventsListContainer');
            const countBadge = document.getElementById('dashEventsCountBadge');
            const subtitle = document.getElementById('dashActiveMonthSubtitle');

            if (!dashCalendarInstance) return;
            const curDate = dashCalendarInstance.getDate();
            const targetYear = curDate.getFullYear();
            const targetMonth = curDate.getMonth();
            const viewTitle = (dashCalendarInstance.view && dashCalendarInstance.view.title) ? dashCalendarInstance.view.title : curDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

            // Default: show all events belonging to the active month
            let events = dashAllEvents.filter(e => {
                const rawDate = e.startStr || e.start || '';
                if (!rawDate) return false;
                const parts = rawDate.split('T')[0].split('-');
                if (parts.length >= 2) {
                    const y = parseInt(parts[0], 10);
                    const m = parseInt(parts[1], 10) - 1;
                    return y === targetYear && m === targetMonth;
                }
                const d = new Date(rawDate);
                return !isNaN(d) && d.getFullYear() === targetYear && d.getMonth() === targetMonth;
            });

            // If filtered by specific date or today
            if (dashCurrentFilterDate) {
                events = events.filter(e => {
                    const s = e.startStr ? e.startStr.split('T')[0] : (e.start ? e.start.split('T')[0] : '');
                    return s === dashCurrentFilterDate;
                });
                subtitle.innerText = dashFilterIsToday
                    ? `Showing today's scheduled events (${formatDashDate(dashCurrentFilterDate)})`
                    : `Events on ${formatDashDate(dashCurrentFilterDate)}`;
            } else {
                subtitle.innerText = `All scheduled events for ${viewTitle}`;
            }

            // Search query filter
            if (dashCurrentSearchQuery) {
                events = events.filter(e => {
                    const title = (e.title || '').toLowerCase();
                    const props = e.extendedProps || {};
                    const customer = (props.customer || '').toLowerCase();
                    const venue = (props.venue || '').toLowerCase();
                    const eventNo = (props.event_no || '').toLowerCase();
                    const pkg = (props.package || '').toLowerCase();

                    return title.includes(dashCurrentSearchQuery) ||
                           customer.includes(dashCurrentSearchQuery) ||
                           venue.includes(dashCurrentSearchQuery) ||
                           eventNo.includes(dashCurrentSearchQuery) ||
                           pkg.includes(dashCurrentSearchQuery);
                });
            }

            // Sort chronologically
            events.sort((a, b) => new Date(a.start) - new Date(b.start));

            countBadge.innerText = `${events.length} Event${events.length === 1 ? '' : 's'}`;
            container.innerHTML = '';

            if (events.length === 0) {
                const createUrl = dashCurrentFilterDate 
                    ? `{{ route('events.create') }}?event_date=${dashCurrentFilterDate}` 
                    : `{{ route('events.create') }}`;

                const resetLink = dashCurrentFilterDate ? `
                    <div class="mt-2">
                        <button type="button" class="dash-action-btn py-1 px-3" onclick="resetDashDateFilter()">
                            <i class="bi bi-calendar3 me-1"></i> Show All Month Events
                        </button>
                    </div>
                ` : '';

                container.innerHTML = `
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                        <h6 class="fw-bold mb-1 text-dark">${dashFilterIsToday ? 'No Events Scheduled for Today' : (dashCurrentFilterDate ? 'No Events on this Date' : 'No Events Scheduled')}</h6>
                        <p class="small text-muted mb-3">${dashFilterIsToday ? 'There are no events registered for today in this calendar.' : (dashCurrentFilterDate ? 'No events found on ' + formatDashDate(dashCurrentFilterDate) + '.' : 'No events scheduled for ' + viewTitle + '.')}</p>
                        <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                            <a href="${createUrl}" target="_blank" rel="noopener noreferrer" class="dash-action-btn">
                                <i class="bi bi-plus-lg me-1"></i> Register Event
                            </a>
                            ${resetLink}
                        </div>
                    </div>
                `;
                return;
            }

            // Render responsive FULL-WIDTH card list with smooth scrolling
            const row = document.createElement('div');
            row.className = 'row g-3';

            events.forEach(e => {
                const props = e.extendedProps || {};
                const status = props.status || 'pending';
                const statusClass = status === 'confirmed' ? 'status-confirmed' : (status === 'completed' ? 'status-completed' : (status === 'cancelled' ? 'status-cancelled' : 'status-pending'));
                const rawDate = e.startStr ? e.startStr.split('T')[0] : (e.start ? e.start.split('T')[0] : '');
                const dateFormatted = formatDashDate(rawDate);
                const amountFormatted = props.total_amount ? 'Rs. ' + parseFloat(props.total_amount).toLocaleString('en-US', { minimumFractionDigits: 2 }) : '';
                const orderUrl = props.order_url || ('/events/' + e.id + '/create-order');

                const col = document.createElement('div');
                col.className = 'col-12 col-md-6 col-xl-12';

                col.innerHTML = `
                    <div class="dash-event-card-item w-100" id="dash-event-item-${e.id}">
                        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge bg-light text-dark border fw-bold px-2.5 py-1" style="font-size: 11.5px;">
                                     <i class="bi bi-calendar-event me-1 text-primary"></i> ${dateFormatted}
                                </span>
                                <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 11px;">
                                    ${props.event_no || ('EVT-' + e.id)}
                                </span>
                                ${props.package ? `<span class="badge bg-light text-dark border px-2 py-1" style="font-size: 11px;"><i class="bi bi-box-seam me-1 text-primary"></i>${props.package}</span>` : ''}
                            </div>
                            <span class="status-pill ${statusClass}">
                                <span class="pill-dot"></span> ${capitalizeFirst(status)}
                            </span>
                        </div>

                        <div class="mb-2">
                            <a href="${e.url}" target="_blank" rel="noopener noreferrer" class="fw-bold text-dark text-decoration-none fs-6 d-inline-block" title="Open event details in new tab">
                                ${props.event_name || e.title} <i class="bi bi-box-arrow-up-right ms-1 text-muted" style="font-size: 11px;"></i>
                            </a>
                        </div>

                        <div class="d-flex align-items-center gap-3 flex-wrap small text-muted pt-2 border-top mt-2" style="font-size: 12px;">
                            <div><i class="bi bi-person-fill text-muted me-1"></i><strong>Client:</strong> <span class="text-dark fw-medium">${props.customer || 'Walk-in'}</span></div>
                            <div><i class="bi bi-geo-alt-fill text-danger me-1"></i><strong>Venue:</strong> ${props.venue || 'Venue TBD'}</div>
                            ${amountFormatted ? `<div><i class="bi bi-wallet2 text-success me-1"></i><strong>Total:</strong> <span class="text-success fw-bold">${amountFormatted}</span></div>` : ''}
                        </div>

                        <div class="d-flex align-items-center justify-content-start gap-2 mt-3">
                            <a href="${e.url}" target="_blank" rel="noopener noreferrer" class="dash-action-btn" title="View Event Details">
                                <i class="bi bi-eye"></i> View Event
                            </a>
                            <a href="${orderUrl}" target="_blank" rel="noopener noreferrer" class="dash-action-btn" title="Create / View Order">
                                <i class="bi bi-receipt"></i> Order
                            </a>
                        </div>
                    </div>
                `;
                row.appendChild(col);
            });

            container.appendChild(row);
        }

        function highlightDashEventInPanel(eventId) {
            const item = document.getElementById(`dash-event-item-${eventId}`);
            if (item) {
                document.querySelectorAll('.dash-event-card-item').forEach(el => el.classList.remove('active-highlight'));
                item.classList.add('active-highlight');
                item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                resetDashDateFilter();
                setTimeout(() => {
                    const el = document.getElementById(`dash-event-item-${eventId}`);
                    if (el) {
                        el.classList.add('active-highlight');
                        el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                }, 100);
            }
        }

        function formatDashDate(dateString) {
            if (!dateString) return '';
            const parts = dateString.split('-');
            if (parts.length === 3) {
                const d = new Date(parts[0], parts[1] - 1, parts[2]);
                return d.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
            }
            return dateString;
        }

        function capitalizeFirst(string) {
            if (!string) return '';
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
    </script>
@endsection