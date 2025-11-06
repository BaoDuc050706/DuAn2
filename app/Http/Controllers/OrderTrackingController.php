<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index()
    {
        return view('order-tracking.index');
    }

    public function track(Request $request)
    {
        $request->validate([
            'order_code' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string'
        ]);

        $query = Order::where('order_code', $request->order_code);

        if ($request->email) {
            $query->where('email', $request->email);
        }

        if ($request->phone) {
            $query->where('phone', $request->phone);
        }

        $order = $query->with(['orderDetails.product', 'user'])->first();

        if (!$order) {
            return back()->with('error', 'Không tìm thấy đơn hàng. Vui lòng kiểm tra lại thông tin.');
        }

        return view('order-tracking.result', compact('order'));
    }
}
