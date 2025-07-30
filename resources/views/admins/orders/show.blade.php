@extends('admins.layouts.master')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chi tiết đơn hàng #{{ $order ?? '1' }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Thông tin đơn hàng -->
                        <div class="col-md-6">
                            <h5>Thông tin đơn hàng</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Mã đơn hàng:</strong></td>
                                    <td>#{{ $order ?? '1' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Ngày đặt:</strong></td>
                                    <td>25/07/2025</td>
                                </tr>
                                <tr>
                                    <td><strong>Trạng thái:</strong></td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.orders.update-status', $order ?? 1) }}" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                                <option value="pending">Chờ xử lý</option>
                                                <option value="processing" selected>Đang xử lý</option>
                                                <option value="shipped">Đã gửi</option>
                                                <option value="delivered">Đã giao</option>
                                                <option value="cancelled">Đã hủy</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Thanh toán:</strong></td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.orders.update-payment-status', $order ?? 1) }}" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <select name="payment_status" class="form-control form-control-sm" onchange="this.form.submit()">
                                                <option value="pending">Chờ thanh toán</option>
                                                <option value="paid" selected>Đã thanh toán</option>
                                                <option value="refunded">Đã hoàn tiền</option>
                                                <option value="failed">Thất bại</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Thông tin khách hàng -->
                        <div class="col-md-6">
                            <h5>Thông tin khách hàng</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Tên:</strong></td>
                                    <td>Nguyễn Văn A</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>nguyenvana@email.com</td>
                                </tr>
                                <tr>
                                    <td><strong>Điện thoại:</strong></td>
                                    <td>0123456789</td>
                                </tr>
                                <tr>
                                    <td><strong>Địa chỉ:</strong></td>
                                    <td>123 Đường ABC, Quận 1, TP.HCM</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Chi tiết sản phẩm -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Chi tiết sản phẩm</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Số lượng</th>
                                        <th>Đơn giá</th>
                                        <th>Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Dữ liệu mẫu -->
                                    <tr>
                                        <td>Áo thun nam</td>
                                        <td>2</td>
                                        <td>150,000 VNĐ</td>
                                        <td>300,000 VNĐ</td>
                                    </tr>
                                    <tr>
                                        <td>Quần jean</td>
                                        <td>1</td>
                                        <td>200,000 VNĐ</td>
                                        <td>200,000 VNĐ</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3">Tổng cộng:</th>
                                        <th>500,000 VNĐ</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection