<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(Request $request)
    {
        // TODO: replace these with real queries, e.g.:
        // User::count(), Product::count(), Order::sum('total'), Order::count(),
        // Order::where('status', 'pending')->count(),
        // Product::where('stock', '<=', 5)->count(),
        // User::whereMonth('created_at', now()->month)->count(),
        // Order::avg('total')
        $stats = [
            'total_users'           => 1234,
            'total_products'        => 567,
            'revenue'               => 12345,
            'total_orders'          => 89,
            'pending_orders'        => 14,
            'low_stock_products'    => 9,
            'new_users_this_month'  => 62,
            'avg_order_value'       => 138.71,
        ];

        // TODO: replace with Order::latest()->take(5)->get() mapped to this shape.
        $recentOrders = [
            ['id' => 1042, 'customer' => 'Sarah Nguyen', 'date' => 'Aug 28, 2026', 'amount' => 128.50, 'status' => 'completed'],
            ['id' => 1041, 'customer' => 'James Carter', 'date' => 'Aug 27, 2026', 'amount' => 74.00, 'status' => 'pending'],
            ['id' => 1040, 'customer' => 'Amelia Torres', 'date' => 'Aug 27, 2026', 'amount' => 212.90, 'status' => 'processing'],
            ['id' => 1039, 'customer' => 'David Kim', 'date' => 'Aug 25, 2026', 'amount' => 46.20, 'status' => 'cancelled'],
        ];

        // TODO: replace with a real monthly revenue aggregation query.
        $revenueChart = [
            'labels' => ['Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
            'values' => [4200, 5100, 4800, 6300, 7150, 8420],
        ];

        return view('admin.dashboard', compact('stats', 'recentOrders', 'revenueChart'));
    }
}