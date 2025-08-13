<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'orderDetails.productVariant.product', 'orderDetails.productVariant.color', 'orderDetails.productVariant.size']);

        // Xử lý tìm kiếm
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            
            $query->where(function($q) use ($keyword) {
                $q->where('id', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")  
                  ->orWhere('phone', 'like', "%{$keyword}%")
                  ->orWhere('address', 'like', "%{$keyword}%")
                  ->orWhereHas('user', function($userQuery) use ($keyword) {
                      $userQuery->where('name', 'like', "%{$keyword}%");
                  });
            });
        }

        // Phân trang và giữ lại query parameters
        $orders = $query->latest()->paginate(10)->appends($request->query());
        
        return view('admins.orders.index', compact('orders'));
    }

    public function show($order)
    {
        $order = Order::with([
            'user',
            'orderDetails.productVariant.product',
            'orderDetails.productVariant.color',
            'orderDetails.productVariant.size'
        ])->findOrFail($order);

        return view('admins.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $order)
    {
        // Debug để kiểm tra data nhận được
        // dd($request->all());
        
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipping,delivered,cancelled,returned'
        ], [
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái không hợp lệ.'
        ]);

        try {
            $order = Order::findOrFail($order);
            $order->status = $request->status;
            $order->save();

            return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function updatePaymentStatus(Request $request, $orderId)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,refunded,failed'
        ], [
            'payment_status.required' => 'Trạng thái thanh toán là bắt buộc.',
            'payment_status.in' => 'Trạng thái thanh toán không hợp lệ.'
        ]);

        try {
            $order = Order::findOrFail($orderId);
            $order->payment_status = $request->payment_status;
            $order->save();
            
            return redirect()->back()->with('success', 'Cập nhật trạng thái thanh toán thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function destroy($order)
    {
        try {
            $order = Order::findOrFail($order);
            $order->delete();
            
            return redirect()->route('admin.orders.index')->with('success', 'Xóa đơn hàng thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}