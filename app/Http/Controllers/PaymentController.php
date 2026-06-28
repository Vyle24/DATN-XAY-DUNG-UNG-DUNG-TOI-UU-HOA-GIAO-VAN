<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Show checkout page or initiate payment.
     */
    public function checkout($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Security check
        if (Auth::user()->role_id == 3 && $order->customer_id != optional(Auth::user()->customer)->id) {
            abort(403, 'Bạn không có quyền truy cập đơn hàng này.');
        }

        if ($order->payment_status == 'paid') {
            return redirect()->route('customer.dashboard')->with('info', 'Đơn hàng này đã được thanh toán.');
        }

        return view('customer.payment_mock', compact('order'));
    }

    /**
     * Process mock VNPay callback.
     */
    public function process(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($request->input('status') === 'success') {
            $order->payment_status = 'paid';
            $order->payment_method = 'VNPay (Mock)';
            $order->save();

            return redirect()->route('customer.dashboard')->with('success', 'Thanh toán thành công qua VNPay cho đơn hàng ' . $order->order_code . '!');
        }

        return redirect()->route('customer.dashboard')->with('error', 'Thanh toán thất bại hoặc đã bị hủy.');
    }
}
