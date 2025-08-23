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

        // Thêm tìm kiếm nếu có keyword
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('id', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%")
                  ->orWhereHas('user', function($userQuery) use ($keyword) {
                      $userQuery->where('name', 'like', "%{$keyword}%");
                  });
            });
        }

        $orders = $query->latest()->paginate(10);
        
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
        // Validation cho integer status
        $request->validate([
            'status' => 'required|integer|in:0,1,2,3,4,5,6'
        ], [
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.integer' => 'Trạng thái phải là số nguyên.',
            'status.in' => 'Trạng thái không hợp lệ.'
        ]);

        try {
            $order = Order::findOrFail($order);
            $order->status = (int) $request->status;
            $order->save();

            // Redirect back để giữ nguyên trang hiện tại
            return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
            
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