<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Hiển thị danh sách đơn hàng
     */
    public function index()
    {
        // Lấy danh sách đơn hàng từ database
        // $orders = Order::with(['user', 'orderItems'])->latest()->paginate(10);
        
        // Tạm thời return view với dữ liệu mẫu
        $orders = collect([
            (object)[
                'id' => 1,
                'customer_name' => 'Nguyễn Văn A',
                'total' => 500000,
                'status' => 'processing',
                'payment_status' => 'paid',
                'created_at' => now()
            ]
        ]);
        
        return view('admins.orders.index', compact('orders'));
    }

    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function show($order)
    {
        // Tìm đơn hàng theo ID
        // $order = Order::with(['user', 'orderItems.product'])->findOrFail($order);
        
        return view('admins.orders.show', compact('order'));
    }

    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateStatus(Request $request, $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        // Tìm và cập nhật đơn hàng
        // $order = Order::findOrFail($order);
        // $order->status = $request->status;
        // $order->save();

        return redirect()->route('admin.orders.index')->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
    }

    /**
     * Cập nhật trạng thái thanh toán
     */
    public function updatePaymentStatus(Request $request, $order)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,refunded,failed'
        ]);

        // Tìm và cập nhật trạng thái thanh toán
        // $order = Order::findOrFail($order);
        // $order->payment_status = $request->payment_status;
        // $order->save();

        return redirect()->route('admin.orders.index')->with('success', 'Cập nhật trạng thái thanh toán thành công!');
    }

    /**
     * Xóa đơn hàng
     */
    public function destroy($order)
    {
        // Tìm và xóa đơn hàng
        // $order = Order::findOrFail($order);
        // $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Xóa đơn hàng thành công!');
    }

    /**
     * Xuất file Excel danh sách đơn hàng
     */
    public function export()
    {
        // Logic xuất file Excel
        // return Excel::download(new OrdersExport, 'orders.xlsx');
        
        return response()->json(['message' => 'Chức năng xuất file đang được phát triển']);
    }
}