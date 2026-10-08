<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManageOrderController extends Controller
{
    protected array $statuses = [
        'pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'returned'
    ];

    protected array $paymentMethods = [
        'cod', 'bkash', 'nagad', 'bank_transfer', 'card',
    ];

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getDataTableData($request, false);
        }

        return view('admin.orders.index');
    }

    public function incomplete(Request $request)
    {
        if ($request->ajax()) {
            return $this->getDataTableData($request, true);
        }

        return view('admin.orders.incomplete');
    }

    protected function getDataTableData(Request $request, $showIncomplete = false)
    {
        $query = Order::with('items.product');

        // Exclude or include incomplete status
        if ($showIncomplete) {
            $query->where('status', 'incomplete');
        } else {
            $query->where('status', '!=', 'incomplete');
        }

        // Global search
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('courier_tracking_id', 'like', "%{$search}%")
                    ->orWhere('consignment_id', 'like', "%{$search}%")
                    ->orWhereHas('items.product', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filters
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($paymentMethod = $request->input('payment_method')) {
            $query->where('payment_method', $paymentMethod);
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $recordsTotal = $showIncomplete ? Order::where('status', 'incomplete')->count() : Order::where('status', '!=', 'incomplete')->count();
        $recordsFiltered = (clone $query)->count();

        // Ordering
        $orderColumns = [1 => 'id', 2 => 'name', 4 => 'city', 5 => 'total', 7 => 'status', 8 => 'created_at'];
        if ($request->filled('order.0.column')) {
            $columnIndex = (int) $request->input('order.0.column');
            $columnName = $orderColumns[$columnIndex] ?? 'id';
            $columnDir = $request->input('order.0.dir', 'desc') === 'asc' ? 'asc' : 'desc';
            $query->orderBy($columnName, $columnDir);
        } else {
            $query->orderBy('id', 'desc');
        }

        // Pagination
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $orders = $query->get();

        $data = [];
        foreach ($orders as $order) {
            // Get product names from items with quantity
            $productItems = $order->items->map(function($item) {
                $name = $item->product?->name ?? 'N/A';
                $qty = $item->quantity ?? 1;
                return '<span class="product-item">' . e($name) . ' × ' . $qty . '</span>';
            })->implode(' ');

            // Alternative: simple comma-separated list
            // $productNames = $order->items->map(function($item) {
            //     return $item->product?->name ?? 'N/A';
            // })->implode(', ');

            $totalAmount = $order->items->sum('subtotal') + ($order->delivery_charge ?? 0) - ($order->discount ?? 0);

            $data[] = [
                'checkbox' => '<input type="checkbox" class="form-check-input row-checkbox" value="'.$order->id.'">',
                'order_id' => '#'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT),
                'customer' => '<div class="fw-semibold">'.e($order->name).'</div><div class="text-muted small">'.e($order->phone_number).'</div>',
                'products' => $productItems ?: '<span class="text-muted">No items</span>',
                'city' => e($order->city),
                'total' => number_format($totalAmount, 2),
                'payment_method' => ucfirst(str_replace('_', ' ', $order->payment_method ?? '')),
                'status' => $this->statusBadge($order->status),
                'date' => $order->created_at ? $order->created_at->format('d M Y, h:i A') : '',
                'action' => $this->actionButtons($order),
            ];
        }

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    protected function statusBadge(?string $status): string
    {
        $map = [
            'pending' => 'bg-warning text-dark',
            'confirmed' => 'bg-info text-white',
            'processing' => 'bg-primary text-white',
            'shipped' => 'bg-secondary text-white',
            'delivered' => 'bg-success text-white',
            'cancelled' => 'bg-danger text-white',
            'returned' => 'bg-dark text-white',
            'incomplete' => 'bg-secondary text-white',
        ];

        $class = $map[$status] ?? 'bg-light text-dark';
        $label = $status ? ucfirst($status) : 'N/A';

        return '<span class="badge '.$class.'">'.$label.'</span>';
    }

    protected function actionButtons(Order $order): string
    {
        $editUrl = route('admin.orders.edit', $order->id);
        $deleteUrl = route('admin.orders.destroy', $order->id);

        return '
            <div class="d-flex gap-1 justify-content-center">
                <a href="'.$editUrl.'" class="btn btn-sm btn-outline-primary" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>
                <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-url="'.$deleteUrl.'" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        ';
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();

        $attributeData = [];

        foreach ($products as $product) {
            if ($product->hasProductAttribute()) {
                $options = $product->getAvailableAttributeOptionsAttribute();

                $attributeData[$product->id] = [
                    'attributeId' => $product->product_attribute_id,
                    'options' => $options->map(function ($option) {
                        return [
                            'id' => $option->id,
                            'name' => $option->name,
                            'added_price' => (float) ($option->added_price ?? 0),
                        ];
                    })->values()->toArray(),
                ];
            }
        }

        return view('admin.orders.create', compact(
            'products',
            'attributeData'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'delivery_charge' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:1000',
            'payment_method' => 'nullable|in:'.implode(',', $this->paymentMethods),
            'status' => 'required|in:'.implode(',', array_merge($this->statuses, ['incomplete'])),
            'courier_name' => 'nullable|string|max:100',
            'courier_tracking_id' => 'nullable|string|max:100',
            'consignment_id' => 'nullable|string|max:100',
            'discount' => 'nullable|numeric|min:0',
            'admin_note' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
            'items.*.size' => 'nullable|string|max:50',
            'items.*.color' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();
        try {
            $order = Order::create([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'address' => $request->address,
                'city' => $request->city,
                'delivery_charge' => $request->delivery_charge ?? 0,
                'note' => $request->note,
                'payment_method' => $request->payment_method,
                'status' => $request->status,
                'discount' => $request->discount ?? 0,
                'admin_note' => $request->admin_note,
                'courier_name' => $request->courier_name,
                'courier_tracking_id' => $request->courier_tracking_id,
                'consignment_id' => $request->consignment_id,
            ]);

            // Create order items
            foreach ($request->items as $itemData) {
                $order->items()->create($itemData);
            }

            DB::commit();

            return redirect()
                ->route('admin.orders.index')
                ->with('success', 'Order created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    public function edit(Order $order)
    {
        $order->load('items.product');
        $products = Product::orderBy('name')->get();
        return view('admin.orders.edit', compact('order', 'products'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'delivery_charge' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:1000',
            'payment_method' => 'nullable|in:'.implode(',', $this->paymentMethods),
            'status' => 'required|in:'.implode(',', array_merge($this->statuses, ['incomplete'])),
            'courier_name' => 'nullable|string|max:100',
            'courier_tracking_id' => 'nullable|string|max:100',
            'consignment_id' => 'nullable|string|max:100',
            'discount' => 'nullable|numeric|min:0',
            'admin_note' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:order_items,id',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
            'items.*.size' => 'nullable|string|max:50',
            'items.*.color' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();
        try {
            // Update order
            $order->update([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'address' => $request->address,
                'city' => $request->city,
                'delivery_charge' => $request->delivery_charge ?? 0,
                'note' => $request->note,
                'payment_method' => $request->payment_method,
                'status' => $request->status,
                'discount' => $request->discount ?? 0,
                'admin_note' => $request->admin_note,
                'courier_name' => $request->courier_name,
                'courier_tracking_id' => $request->courier_tracking_id,
                'consignment_id' => $request->consignment_id,
            ]);

            // Get existing item IDs
            $existingIds = $order->items->pluck('id')->toArray();
            $incomingIds = collect($request->items)->pluck('id')->filter()->toArray();

            // Delete removed items
            $toDelete = array_diff($existingIds, $incomingIds);
            if (!empty($toDelete)) {
                $order->items()->whereIn('id', $toDelete)->delete();
            }

            // Update or create items
            foreach ($request->items as $itemData) {
                if (!empty($itemData['id'])) {
                    $item = $order->items()->find($itemData['id']);
                    if ($item) {
                        $item->update($itemData);
                    }
                } else {
                    $order->items()->create($itemData);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.orders.index')
                ->with('success', 'Order updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update order: ' . $e->getMessage());
        }
    }

    public function destroy(Order $order)
    {
        DB::beginTransaction();
        try {
            $order->items()->delete();
            $order->delete();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Order deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to delete order: ' . $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:'.implode(',', array_merge($this->statuses, ['incomplete'])),
        ]);

        $order->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }
}