@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    <!-- Welcome Banner -->
    <div class="welcome-message">
        <div>
            <h2>Welcome back, {{ explode(' ', Auth::user()->name)[0] }} <i class="fa-solid fa-hand-sparkles"></i></h2>
            <p>
                Logged in as <strong>{{ Auth::user()->name }}</strong> &middot; {{ ucfirst(Auth::user()->role) }}
                <br>
                <i class="fa-regular fa-envelope"></i> {{ Auth::user()->email }}
                &nbsp;&nbsp;
                <i class="fa-solid fa-phone"></i> {{ Auth::user()->phone ?? 'Not provided' }}
                &nbsp;&nbsp;
                Status:
                <span class="welcome-status {{ Auth::user()->status === 'active' ? 'is-active' : 'is-inactive' }}">
                    <i class="fa-solid fa-circle" style="font-size:7px;"></i> {{ ucfirst(Auth::user()->status) }}
                </span>
            </p>
        </div>
        <div class="welcome-icon">
            <i class="fa-solid fa-gauge-high"></i>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-card-top">
                <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                <span class="stat-trend up"><i class="fa-solid fa-arrow-trend-up"></i> 8.2%</span>
            </div>
            <div class="stat-number">{{ number_format($stats['total_users'] ?? 0) }}</div>
            <div class="stat-label">Total Users</div>
        </div>

        <div class="stat-card secondary">
            <div class="stat-card-top">
                <div class="stat-icon"><i class="fa-solid fa-bag-shopping"></i></div>
                <span class="stat-trend up"><i class="fa-solid fa-arrow-trend-up"></i> 3.4%</span>
            </div>
            <div class="stat-number">{{ number_format($stats['total_products'] ?? 0) }}</div>
            <div class="stat-label">Total Products</div>
        </div>

        <div class="stat-card success">
            <div class="stat-card-top">
                <div class="stat-icon"><i class="fa-solid fa-sack-dollar"></i></div>
                <span class="stat-trend up"><i class="fa-solid fa-arrow-trend-up"></i> 12.1%</span>
            </div>
            <div class="stat-number">${{ number_format($stats['revenue'] ?? 0, 2) }}</div>
            <div class="stat-label">Revenue</div>
        </div>

        <div class="stat-card info">
            <div class="stat-card-top">
                <div class="stat-icon"><i class="fa-solid fa-box"></i></div>
                <span class="stat-trend down"><i class="fa-solid fa-arrow-trend-down"></i> 1.6%</span>
            </div>
            <div class="stat-number">{{ number_format($stats['total_orders'] ?? 0) }}</div>
            <div class="stat-label">Orders</div>
        </div>

        <div class="stat-card warning">
            <div class="stat-card-top">
                <div class="stat-icon"><i class="fa-solid fa-hourglass-half"></i></div>
                <span class="stat-trend flat"><i class="fa-solid fa-minus"></i> 0.0%</span>
            </div>
            <div class="stat-number">{{ number_format($stats['pending_orders'] ?? 0) }}</div>
            <div class="stat-label">Pending Orders</div>
        </div>

        <div class="stat-card danger">
            <div class="stat-card-top">
                <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <span class="stat-trend down"><i class="fa-solid fa-arrow-trend-down"></i> 4.5%</span>
            </div>
            <div class="stat-number">{{ number_format($stats['low_stock_products'] ?? 0) }}</div>
            <div class="stat-label">Low Stock Items</div>
        </div>

        <div class="stat-card primary">
            <div class="stat-card-top">
                <div class="stat-icon"><i class="fa-solid fa-user-plus"></i></div>
                <span class="stat-trend up"><i class="fa-solid fa-arrow-trend-up"></i> 5.9%</span>
            </div>
            <div class="stat-number">{{ number_format($stats['new_users_this_month'] ?? 0) }}</div>
            <div class="stat-label">New Users (This Month)</div>
        </div>

        <div class="stat-card secondary">
            <div class="stat-card-top">
                <div class="stat-icon"><i class="fa-solid fa-receipt"></i></div>
                <span class="stat-trend up"><i class="fa-solid fa-arrow-trend-up"></i> 2.3%</span>
            </div>
            <div class="stat-number">${{ number_format($stats['avg_order_value'] ?? 0, 2) }}</div>
            <div class="stat-label">Avg. Order Value</div>
        </div>
    </div>

    <!-- Chart + Quick Actions -->
    <div class="admin-grid-2">
        <div class="admin-card">
            <div class="admin-card-header">
                <h3><i class="fa-solid fa-chart-line"></i> Revenue Overview</h3>
                <span class="badge badge-manager">Last 6 months</span>
            </div>
            <div class="admin-card-body">
                <canvas id="revenueChart" height="110"></canvas>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <h3><i class="fa-solid fa-bolt"></i> Quick Actions</h3>
            </div>
            <div class="admin-card-body">
                <div class="quick-actions">
                    <a href="#" class="quick-action">
                        <span class="qa-icon"><i class="fa-solid fa-plus"></i></span> Add Product
                    </a>
                    <a href="#" class="quick-action">
                        <span class="qa-icon"><i class="fa-solid fa-tags"></i></span> Add Category
                    </a>
                    <a href="#" class="quick-action">
                        <span class="qa-icon"><i class="fa-solid fa-image"></i></span> Home Sliders
                    </a>
                    <a href="#" class="quick-action">
                        <span class="qa-icon"><i class="fa-solid fa-star"></i></span> Reviews
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3><i class="fa-solid fa-clock-rotate-left"></i> Recent Orders</h3>
            <a href="#" class="btn-admin btn-admin-outline"><i class="fa-solid fa-arrow-right"></i> View All</a>
        </div>
        <div class="admin-card-body" style="overflow-x:auto;">
            <table class="table-admin data-table" style="width:100%;">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse (($recentOrders ?? []) as $order)
                        <tr>
                            <td>#{{ $order['id'] }}</td>
                            <td>{{ $order['customer'] }}</td>
                            <td>{{ $order['date'] }}</td>
                            <td>${{ number_format($order['amount'], 2) }}</td>
                            <td><span class="status-pill {{ $order['status'] }}">{{ ucfirst($order['status']) }}</span></td>
                            <td style="text-align:right;">
                                <button type="button" class="btn-icon-admin" title="View"><i class="fa-solid fa-eye"></i></button>
                                <button type="button" class="btn-icon-admin danger" title="Delete"
                                        onclick="AdminModal.delete({ title: 'Delete order #{{ $order['id'] }}?', onConfirm: function(){ AdminToast.success('Order #{{ $order['id'] }} deleted.'); } })">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        {{-- Sample rows so the layout/table renders nicely before real data is wired up --}}
                        <tr>
                            <td>#1042</td><td>Sarah Nguyen</td><td>Aug 28, 2026</td><td>$128.50</td>
                            <td><span class="status-pill completed">Completed</span></td>
                            <td style="text-align:right;">
                                <button type="button" class="btn-icon-admin" title="View"><i class="fa-solid fa-eye"></i></button>
                                <button type="button" class="btn-icon-admin danger" title="Delete"
                                        onclick="AdminModal.delete({ title: 'Delete order #1042?', onConfirm: function(){ AdminToast.success('Order deleted.'); } })">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>#1041</td><td>James Carter</td><td>Aug 27, 2026</td><td>$74.00</td>
                            <td><span class="status-pill pending">Pending</span></td>
                            <td style="text-align:right;">
                                <button type="button" class="btn-icon-admin" title="View"><i class="fa-solid fa-eye"></i></button>
                                <button type="button" class="btn-icon-admin danger" title="Delete"
                                        onclick="AdminModal.delete({ title: 'Delete order #1041?', onConfirm: function(){ AdminToast.success('Order deleted.'); } })">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>#1040</td><td>Amelia Torres</td><td>Aug 27, 2026</td><td>$212.90</td>
                            <td><span class="status-pill processing">Processing</span></td>
                            <td style="text-align:right;">
                                <button type="button" class="btn-icon-admin" title="View"><i class="fa-solid fa-eye"></i></button>
                                <button type="button" class="btn-icon-admin danger" title="Delete"
                                        onclick="AdminModal.delete({ title: 'Delete order #1040?', onConfirm: function(){ AdminToast.success('Order deleted.'); } })">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>#1039</td><td>David Kim</td><td>Aug 25, 2026</td><td>$46.20</td>
                            <td><span class="status-pill cancelled">Cancelled</span></td>
                            <td style="text-align:right;">
                                <button type="button" class="btn-icon-admin" title="View"><i class="fa-solid fa-eye"></i></button>
                                <button type="button" class="btn-icon-admin danger" title="Delete"
                                        onclick="AdminModal.delete({ title: 'Delete order #1039?', onConfirm: function(){ AdminToast.success('Order deleted.'); } })">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@php
    // Computed here (instead of inline inside @json()) because Blade's @json()
    // directive splits its argument on every comma — an array literal passed
    // directly to @json() breaks compilation with an "Unclosed '['" error.
    $chartLabels = $revenueChart['labels'] ?? ['Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'];
    $chartValues = $revenueChart['values'] ?? [4200, 5100, 4800, 6300, 7150, 8420];
@endphp

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.getElementById('revenueChart');
            if (!ctx || !window.Chart) return;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Revenue ($)',
                        data: @json($chartValues),
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.12)',
                        borderWidth: 2.5,
                        tension: 0.35,
                        fill: true,
                        pointRadius: 3,
                        pointBackgroundColor: '#4f46e5'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>
@endpush