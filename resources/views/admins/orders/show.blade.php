@extends('admins.layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->id)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Thông tin đơn hàng -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chi tiết đơn hàng #{{ $order->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Thông tin khách hàng -->
                        <div class="col-md-6">
                            <h5>Thông tin khách hàng</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Tên:</strong></td>
                                    <td>{{ $order->user->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $order->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Số điện thoại:</strong></td>
                                    <td>{{ $order->phone }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Địa chỉ:</strong></td>
                                    <td>{{ $order->address }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Thông tin đơn hàng -->
                        <div class="col-md-6">
                            <h5>Thông tin đơn hàng</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Mã đơn hàng:</strong></td>
                                    <td>#{{ $order->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Ngày tạo:</strong></td>
                                    <td>{{ $order->created_at ? $order->created_at->format('d/m/Y H:i:s') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Trạng thái:</strong></td>
                                    <td>
                                        <span class="badge {{ $order->status_badge_class }}">
                                            {{ $order->status_text }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Thanh toán:</strong></td>
                                    <td>
                                        <span class="badge {{ $order->payment_badge_class }}">
                                            {{ $order->payment_text }}
                                        </span>
                                    </td>
                                </tr>
                                @if($order->vorcher_code)
                                <tr>
                                    <td><strong>Mã giảm giá:</strong></td>
                                    <td>{{ $order->vorcher_code }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chi tiết sản phẩm -->
            <div class="card">
                <div class="card-header">
                    <h4>Chi tiết sản phẩm</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Màu sắc</th>
                                    <th>Kích thước</th>
                                    <th>Đơn giá</th>
                                    <th>Số lượng</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $subtotal = 0; @endphp
                                @foreach($order->orderDetails as $detail)
                                    @php $itemTotal = $detail->price * $detail->quantity; @endphp
                                    @php $subtotal += $itemTotal; @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($detail->productVariant && $detail->productVariant->image)
                                                    <img src="{{ $detail->productVariant->image }}" 
                                                         alt="Product" 
                                                         class="img-thumbnail me-2" 
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <strong>{{ $detail->productVariant->product->name ?? 'N/A' }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        Mã: {{ $detail->productVariant->product->code ?? 'N/A' }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge" style="background-color: {{ $detail->productVariant->color->code ?? '#ccc' }}">
                                                {{ $detail->productVariant->color->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary">
                                                {{ $detail->productVariant->size->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($detail->price, 0, ',', '.') }}đ</td>
                                        <td>{{ $detail->quantity }}</td>
                                        <td><strong>{{ number_format($itemTotal, 0, ',', '.') }}đ</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <td colspan="5" class="text-right"><strong>Tạm tính:</strong></td>
                                    <td><strong>{{ number_format($subtotal, 0, ',', '.') }}đ</strong></td>
                                </tr>
                                @if($order->sale_price)
                                <tr class="table-light">
                                    <td colspan="5" class="text-right"><strong>Giảm giá:</strong></td>
                                    <td><strong class="text-danger">-{{ number_format($order->sale_price, 0, ',', '.') }}đ</strong></td>
                                </tr>
                                @endif
                                <tr class="table-primary">
                                    <td colspan="5" class="text-right"><strong>Tổng cộng:</strong></td>
                                    <td><strong class="text-primary">{{ number_format($order->pay_amount, 0, ',', '.') }}đ</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h5>Cập nhật trạng thái</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}" id="updateStatusForm">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Trạng thái đơn hàng:</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="0" {{ $order->status == 0 ? 'selected' : '' }}>Chờ xử lý</option>
                                        <option value="1" {{ $order->status == 1 ? 'selected' : '' }}>Đã xác nhận</option>
                                        <option value="2" {{ $order->status == 2 ? 'selected' : '' }}>Đang xử lý</option>
                                        <option value="3" {{ $order->status == 3 ? 'selected' : '' }}>Đang giao hàng</option>
                                        <option value="4" {{ $order->status == 4 ? 'selected' : '' }}>Đã giao</option>
                                        <option value="5" {{ $order->status == 5 ? 'selected' : '' }}>Đã hủy</option>
                                        <option value="6" {{ $order->status == 6 ? 'selected' : '' }}>Đã trả hàng</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary" id="updateBtn">
                                            <i class="fas fa-save"></i> Cập nhật trạng thái
                                        </button>
                                        <button type="button" class="btn btn-secondary ml-2" onclick="window.print()">
                                            <i class="fas fa-print"></i> In đơn hàng
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Đảm bảo DOM và jQuery đã sẵn sàng
    $(document).ready(function() {
        // Auto hide alert after 3 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 3000);

        // Add loading state to update button
        $('#updateStatusForm').submit(function() {
            var $btn = $('#updateBtn');
            var originalHtml = $btn.html();
            
            $btn.html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...')
                .prop('disabled', true);
            
            // Re-enable button after 3 seconds in case of error
            setTimeout(function() {
                $btn.html(originalHtml).prop('disabled', false);
            }, 3000);
        });
    });

    // Print styles - không cần jQuery
    var printStyles = document.createElement('style');
    printStyles.type = 'text/css';
    printStyles.innerHTML = `
        @media print {
            .btn, .card-tools, .card-header h5:contains("Cập nhật"), .card:last-child {
                display: none !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
            .card-header {
                background: none !important;
                border-bottom: 2px solid #000 !important;
            }
        }
    `;
    document.head.appendChild(printStyles);

    // Fallback nếu jQuery chưa load
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $ === 'undefined') {
            console.warn('jQuery chưa được load. Một số tính năng có thể không hoạt động.');
            
            // Auto hide alerts với vanilla JavaScript
            setTimeout(function() {
                var alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                });
            }, 3000);

            // Form submit handler với vanilla JavaScript
            var form = document.getElementById('updateStatusForm');
            if (form) {
                form.addEventListener('submit', function() {
                    var btn = document.getElementById('updateBtn');
                    if (btn) {
                        var originalHtml = btn.innerHTML;
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
                        btn.disabled = true;
                        
                        setTimeout(function() {
                            btn.innerHTML = originalHtml;
                            btn.disabled = false;
                        }, 3000);
                    }
                });
            }
        }
    });
</script>
@endsection