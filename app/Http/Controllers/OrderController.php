<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Show authenticated user's order history (session-backed fallback).
     */
    public function index(Request $request): View
    {
        $orders = collect();

        // Prefer to fetch from DB if an orders table exists (future work).
        // For now, use session-stored orders inserted in PaymentController::process
        $sessionOrders = $request->session()->get('orders', []);
        if (!empty($sessionOrders)) {
            $orders = collect($sessionOrders);
        }

        return view('orders.index', [
            'orders' => $orders,
        ]);
    }

    /**
     * Public lookup by order id (session-backed).
     */
    public function lookup(Request $request): View
    {
        $code = (string) $request->query('code', '');
        $order = null;

        if ($code !== '') {
            $orders = $request->session()->get('orders', []);
            foreach ($orders as $o) {
                if ((string) ($o['id'] ?? '') === $code) {
                    $order = $o;
                    break;
                }
            }
        }

        return view('orders.lookup', [
            'order' => $order,
            'code' => $code,
        ]);
    }
}
