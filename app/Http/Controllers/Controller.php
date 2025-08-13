<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Order;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function testConnection()
{
    try {
        $orders = Order::all();
        return response()->json(['message' => 'Kết nối thành công', 'count' => $orders->count()]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
}
}
