<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user');

        // Lọc theo trạng thái
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Tìm kiếm theo mã đơn hàng hoặc tên khách hàng
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $newStatus = $request->status;
        $oldStatus = $order->status;

        DB::beginTransaction();
        try {
            // Cập nhật trạng thái đơn hàng
            $order->update(['status' => $newStatus]);

            // Nếu đơn hàng chuyển sang trạng thái "delivered" thì giảm tồn kho
            if ($newStatus === 'delivered' && $oldStatus !== 'delivered') {
                $items = $order->items ?? [];
                foreach ($items as $item) {
                    $productId = $item['product_id'] ?? null;
                    $quantity = (int) ($item['qty'] ?? 0);

                    if ($productId && $quantity > 0) {
                        Product::where('id', $productId)
                            ->decrement('stock', $quantity);
                    }
                }
            }

            DB::commit();
            return redirect()->back()
                ->with('success', 'Cập nhật trạng thái đơn hàng thành công! Tồn kho đã được cập nhật.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating order status: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi cập nhật trạng thái: ' . $e->getMessage());
        }
    }
}
