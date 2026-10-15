@extends('admin.layouts.app')

@push('page_css')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap');

    .dashboard-wrapper {
        padding: 10px 14px 14px;
        background-color: #ffffff;
        font-family: 'Outfit', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #0f172a;
    }

    .dashboard-header {
        margin-bottom: 10px;
    }

    .dashboard-title {
        font-size: 1.05rem;
        font-weight: 700;
        margin: 0;
        letter-spacing: -0.02em;
    }

    .dashboard-title-line {
        height: 2px;
        background-color: #518ca8;
        margin-top: 4px;
        border-radius: 2px;
    }

    /* ---- Cards ---- */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        align-items: stretch;
    }

    .dashboard-panel {
        border-radius: 12px;
        padding: 12px 14px 14px;
        display: flex;
        flex-direction: column;
    }

    .panel-green {
        background-color: #e2f0d9;
        border: 1px solid #cde3c0;
        color: #1d2b16;
    }

    .panel-blue {
        background-color: #dbeaf8;
        border: 1px solid #bfd9ee;
        color: #12242f;
    }

    @media (max-width: 991px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
        /* Single column: there is room again, so let the filters breathe */
        .summary-bar,
        .filter-actions {
            flex-wrap: wrap;
        }
    }

    /* ---- Headline stat ---- */
    .stat-box {
        text-align: center;
        margin-bottom: 9px;
    }

    .stat-number {
        display: block;
        font-size: 1.95rem;
        font-weight: 800;
        line-height: 1.05;
        letter-spacing: -0.045em;
        color: #111827;
    }

    .stat-label {
        display: block;
        margin-top: 1px;
        font-size: 0.76rem;
        font-weight: 500;
    }

    .panel-green .stat-label {
        color: #46702f;
    }

    .panel-blue .stat-label {
        color: #2c6b93;
    }

    /* ---- Rules ---- */
    .panel-rule {
        height: 1px;
        border: 0;
        margin: 0 0 8px;
    }

    .panel-green .panel-rule {
        background-color: #bcd9ac;
    }

    .panel-blue .panel-rule {
        background-color: #a9cde6;
    }

    /* ---- Summary bar ---- */
    .summary-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        flex-wrap: nowrap;
        padding-bottom: 8px;
        margin-bottom: 8px;
        border-bottom: 1px solid;
    }

    .panel-green .summary-bar {
        border-bottom-color: #bcd9ac;
    }

    .panel-blue .summary-bar {
        border-bottom-color: #a9cde6;
    }

    .summary-title {
        font-size: 0.85rem;
        font-weight: 700;
        line-height: 1.2;
        margin: 0;
        flex: 0 0 auto;
        white-space: nowrap;
    }

    /* Filters always sit on a single line; they shrink rather than wrap. */
    .filter-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
        flex-wrap: nowrap;
        min-width: 0;
        font-size: 0.7rem;
    }

    .filter-field {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-weight: 600;
        white-space: nowrap;
        min-width: 0;
    }

    .filter-field > input,
    .filter-field > select,
    .filter-select {
        border: 0;
        border-bottom: 1px solid transparent;
        background: transparent;
        font-family: inherit;
        font-size: 0.7rem;
        font-weight: 500;
        color: inherit;
        outline: none;
        cursor: pointer;
        padding: 0 1px;
        min-width: 0;
        max-width: 105px;
    }

    .filter-field > input:hover,
    .filter-field > select:hover,
    .filter-select:hover {
        border-bottom-color: currentColor;
    }

    /* Trim the native picker chrome so the date fields fit side by side */
    .filter-field > input[type="date"] {
        width: 94px;
    }

    .filter-field > input::-webkit-calendar-picker-indicator {
        padding: 0;
        margin-left: 1px;
        opacity: 0.6;
    }

    /* ---- Generic rows ---- */
    .data-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        padding: 5px 2px;
        font-size: 0.82rem;
        border-bottom: 1px solid;
    }

    .panel-green .data-row {
        border-bottom-color: #cde3c0;
    }

    .panel-blue .data-row {
        border-bottom-color: #c2dced;
    }

    .data-label {
        font-weight: 700;
    }

    .data-val {
        font-weight: 700;
        text-align: right;
    }

    /* ---- Green: zone table ---- */
    .table-header-row {
        display: flex;
        padding: 5px 2px;
        font-size: 0.78rem;
        font-weight: 700;
        border-bottom: 1px solid #bcd9ac;
    }

    .zone-row {
        display: flex;
        padding: 5px 2px;
        font-size: 0.8rem;
        border-bottom: 1px solid #cde3c0;
    }

    .zone-row:last-child {
        border-bottom: 0;
    }

    .col-zone {
        flex: 2;
        font-weight: 600;
    }

    .col-count {
        flex: 1.4;
        text-align: right;
        font-weight: 700;
    }

    .col-trend {
        flex: 2;
        text-align: right;
        font-weight: 700;
    }

    /* ---- Blue: collapsible pending ---- */
    .collapsible-row {
        width: 100%;
        border: 0;
        border-bottom: 1px solid #c2dced;
        background: transparent;
        color: inherit;
        font-family: inherit;
        text-align: left;
        cursor: pointer;
    }

    .collapsible-row .data-label {
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .collapse-arrow {
        font-size: 0.7rem;
        transition: transform 0.2s ease;
    }

    .collapsible-row[aria-expanded="true"] .collapse-arrow {
        transform: rotate(90deg);
    }

    .collapsible-content {
        display: none;
    }

    .collapsible-content.show {
        display: block;
    }

    .sub-row {
        padding-left: 20px;
        font-size: 0.74rem;
    }

    .sub-row .data-label,
    .sub-row .data-val {
        font-weight: 500;
    }

    .data-list {
        margin-bottom: 10px;
    }

    /* ---- Blue: pending alert bar ---- */
    .alert-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        flex-wrap: nowrap;
        margin-top: auto;
        padding: 7px 10px;
        background-color: #fffdf5;
        border: 1px solid #f0d9a0;
        border-radius: 8px;
        font-size: 0.76rem;
        color: #4a4a4a;
    }

    .alert-bar .alert-count {
        font-size: 0.95rem;
        font-weight: 800;
        color: #d92d20;
        margin-right: 3px;
    }

    .btn-excel {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background-color: #1e7e34;
        color: #ffffff;
        border: 0;
        border-radius: 6px;
        padding: 5px 12px;
        white-space: nowrap;
        font-family: inherit;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: background-color 0.2s ease;
    }

    .btn-excel:hover {
        background-color: #17652a;
        color: #ffffff;
        text-decoration: none;
    }

    .btn-excel[disabled] {
        background-color: #9aa79c;
        cursor: not-allowed;
    }
</style>
@endpush

@section('content')
@php
    // Added today — guard so a stale controller cannot fatal the dashboard.
    $pendingOver7Days = $pendingOver7Days ?? 0;
@endphp
<div class="dashboard-wrapper">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Operational Dashboard</h1>
        <div class="dashboard-title-line"></div>
    </div>

    <div class="dashboard-grid">
        <!-- MASON SUMMARY PANEL -->
        <div class="dashboard-panel panel-green">
            <div class="stat-box">
                <span class="stat-number" id="masonCount">{{ number_format($totalMasons) }}</span>
                <span class="stat-label">Mason</span>
            </div>

            <hr class="panel-rule">

            <div class="summary-bar">
                <h3 class="summary-title">Mason Summary</h3>
                <div class="filter-actions">
                    <select class="filter-select" id="masonMonth">
                        <option value="current" {{ $masonMonth == 'current' ? 'selected' : '' }}>Month</option>
                        @foreach(['jan' => 'January', 'feb' => 'February', 'mar' => 'March', 'apr' => 'April', 'may' => 'May', 'jun' => 'June', 'jul' => 'July', 'aug' => 'August', 'sep' => 'September', 'oct' => 'October', 'nov' => 'November', 'dec' => 'December'] as $key => $name)
                            <option value="{{ $key }}" {{ $masonMonth == $key ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>

                    <select class="filter-select" id="masonYear">
                        @foreach($masonYears as $year)
                            <option value="{{ $year }}" {{ $masonYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="data-list">
                <div class="data-row">
                    <span class="data-label">Total Mason</span>
                    <span class="data-val">{{ number_format($totalMasons) }}</span>
                </div>

                <div class="table-header-row">
                    <span class="col-zone">Zone</span>
                    <span class="col-count">Count</span>
                    <span class="col-trend">Vs last month</span>
                </div>

                @foreach(['NE1', 'NE2', 'NB1', 'NB2', 'BIHAR'] as $zone)
                    <div class="zone-row">
                        <span class="col-zone">{{ $zone == 'BIHAR' ? 'Bihar' : $zone }}</span>
                        <span class="col-count">{{ number_format($zoneMasonsTotal[$zone] ?? 0) }}</span>
                        <span class="col-trend">{{ number_format($zoneMasonsThisMonth[$zone] ?? 0) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- REDEEMABLE PRODUCTS SUMMARY PANEL -->
        <div class="dashboard-panel panel-blue">
            <div class="stat-box">
                <span class="stat-number" id="redeemCount">{{ number_format($redeemProductsCount) }}</span>
                <span class="stat-label">Redeemable Products</span>
            </div>

            <hr class="panel-rule">

            <div class="summary-bar">
                <h3 class="summary-title">Order Summary</h3>
                <div class="filter-actions">
                    <select class="filter-select" id="orderZone">
                        <option value="">Zone</option>
                        @foreach($orderZones as $z)
                            <option value="{{ $z->id }}" {{ $orderZone == $z->id ? 'selected' : '' }}>{{ $z->name }}</option>
                        @endforeach
                    </select>

                    <select class="filter-select" id="orderMonth">
                        <option value="current" {{ $orderMonth == 'current' ? 'selected' : '' }}>Month</option>
                        @foreach(['jan' => 'January', 'feb' => 'February', 'mar' => 'March', 'apr' => 'April', 'may' => 'May', 'jun' => 'June', 'jul' => 'July', 'aug' => 'August', 'sep' => 'September', 'oct' => 'October', 'nov' => 'November', 'dec' => 'December'] as $key => $name)
                            <option value="{{ $key }}" {{ $orderMonth == $key ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>

                    <label class="filter-field">
                        From
                        <input type="date" id="orderFromDate" value="{{ $orderFromVal }}">
                    </label>
                    <label class="filter-field">
                        To
                        <input type="date" id="orderToDate" value="{{ $orderToVal }}">
                    </label>
                </div>
            </div>

            <div class="data-list">
                <div class="data-row">
                    <span class="data-label">Total Orders</span>
                    <span class="data-val">{{ number_format($totalOrders) }}</span>
                </div>

                <button type="button" class="data-row collapsible-row" id="pendingOrderToggle" aria-expanded="false" aria-controls="pendingOrderDetails">
                    <span class="data-label">
                        <i class="fas fa-chevron-right collapse-arrow"></i>
                        Pending Order
                    </span>
                    <span class="data-val">{{ number_format($pendingOrders) }}</span>
                </button>

                <!-- Indented items under Pending Order -->
                <div class="collapsible-content" id="pendingOrderDetails">
                    @foreach([
                        'Order confirmation not received',
                        'Address confirmation not received',
                        'Product unavailability & alternatives',
                        'Customer not answering calls',
                        'Incomplete delivery address',
                        'Wrong mobile number',
                    ] as $remark)
                        <div class="data-row sub-row">
                            <span class="data-label">{{ $remark }}</span>
                            <span class="data-val">{{ number_format($remarksCounts[$remark] ?? 0) }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="data-row">
                    <span class="data-label">Order Placed</span>
                    <span class="data-val">{{ number_format($placedOrders) }}</span>
                </div>

                <div class="data-row">
                    <span class="data-label">Order Delivered</span>
                    <span class="data-val">{{ number_format($deliveredOrders) }}</span>
                </div>

                <div class="data-row">
                    <span class="data-label">Order Rejected</span>
                    <span class="data-val">{{ number_format($rejectedOrders) }}</span>
                </div>
            </div>

            <div class="alert-bar">
                <div>
                    <span class="alert-count">{{ number_format($pendingOver7Days) }}</span>
                    orders pending more than 7 days
                </div>
                @if($pendingOver7Days > 0)
                    <a class="btn-excel"
                       id="downloadPendingExcel"
                       href="{{ route('admin.pending-orders.export', request()->only(['order_zone', 'order_month', 'order_from', 'order_to'])) }}">
                        <i class="fas fa-download"></i> Download Excel
                    </a>
                @else
                    <button type="button" class="btn-excel" disabled>
                        <i class="fas fa-download"></i> Download Excel
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('page_scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dynamic count-up animation for the headline numbers
        function animateCount(id, target) {
            const el = document.getElementById(id);
            if (!el) return;
            let start = 0;
            const duration = 1000; // ms
            const stepTime = 15;
            const increment = target / (duration / stepTime);

            const timer = setInterval(() => {
                start += increment;
                if (start >= target) {
                    clearInterval(timer);
                    el.textContent = Number(target).toLocaleString();
                } else {
                    el.textContent = Math.floor(start).toLocaleString();
                }
            }, stepTime);
        }

        animateCount('masonCount', {{ $totalMasons }});
        animateCount('redeemCount', {{ $redeemProductsCount }});

        // Pending Order details toggle
        const pendingOrderToggle = document.getElementById('pendingOrderToggle');
        const pendingOrderDetails = document.getElementById('pendingOrderDetails');
        if (pendingOrderToggle && pendingOrderDetails) {
            pendingOrderToggle.addEventListener('click', function() {
                const isExpanded = pendingOrderToggle.getAttribute('aria-expanded') === 'true';
                pendingOrderToggle.setAttribute('aria-expanded', String(!isExpanded));
                pendingOrderDetails.classList.toggle('show', !isExpanded);
            });
        }

        // Filter reload helper — a falsy value drops the param from the URL
        function applyFilter(params) {
            const url = new URL(window.location.href);
            for (const [key, value] of Object.entries(params)) {
                if (value) {
                    url.searchParams.set(key, value);
                } else {
                    url.searchParams.delete(key);
                }
            }
            window.location.href = url.toString();
        }

        // --- Mason filters ---
        const masonMonthEl = document.getElementById('masonMonth');
        const masonYearEl = document.getElementById('masonYear');
        if (masonMonthEl) {
            masonMonthEl.addEventListener('change', function() {
                applyFilter({
                    'mason_month': this.value,
                    'mason_year': masonYearEl ? masonYearEl.value : ''
                });
            });
        }
        if (masonYearEl) {
            masonYearEl.addEventListener('change', function() {
                applyFilter({
                    'mason_month': masonMonthEl ? masonMonthEl.value : 'current',
                    'mason_year': this.value
                });
            });
        }

        // --- Order filters ---
        const orderZoneEl = document.getElementById('orderZone');
        if (orderZoneEl) {
            orderZoneEl.addEventListener('change', function() {
                applyFilter({ 'order_zone': this.value });
            });
        }

        const orderMonthEl = document.getElementById('orderMonth');
        if (orderMonthEl) {
            orderMonthEl.addEventListener('change', function() {
                // Month and an explicit range are mutually exclusive
                applyFilter({ 'order_month': this.value, 'order_from': '', 'order_to': '' });
            });
        }

        const orderFromEl = document.getElementById('orderFromDate');
        const orderToEl = document.getElementById('orderToDate');
        function applyOrderRange() {
            const from = orderFromEl ? orderFromEl.value : '';
            const to = orderToEl ? orderToEl.value : '';
            if (from && to) {
                applyFilter({ 'order_from': from, 'order_to': to, 'order_month': '' });
            }
        }
        if (orderFromEl) orderFromEl.addEventListener('change', applyOrderRange);
        if (orderToEl) orderToEl.addEventListener('change', applyOrderRange);
    });
</script>
@endpush
