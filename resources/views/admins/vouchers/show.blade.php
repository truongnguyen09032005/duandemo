<!-- resources/views/admins/vouchers/show.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Chi tiết Voucher</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }
        .btn {
            padding: 10px 20px;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
        }
        .btn-back {
            background-color: #6c757d;
        }
        .btn-back:hover {
            background-color: #5a6268;
        }
        .btn-edit {
            background-color: #007bff;
        }
        .btn-edit:hover {
            background-color: #0056b3;
        }
        .btn-delete {
            background-color: #dc3545;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
        .voucher-details {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 20px;
            margin-top: 20px;
        }
        .detail-row {
            display: flex;
            margin-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 10px;
        }
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .detail-label {
            font-weight: bold;
            width: 200px;
            color: #495057;
        }
        .detail-value {
            flex: 1;
            color: #212529;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 3px;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }
        .status-active {
            background-color: #28a745;
        }
        .status-inactive {
            background-color: #dc3545;
        }
        .status-expired {
            background-color: #6c757d;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Chi tiết Voucher: {{ $voucher->name }}</h2>
        
        <div class="action-buttons">
            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-back">← Quay lại danh sách voucher</a>
            <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="btn btn-edit">Chỉnh sửa</a>
            <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-delete" onclick="return confirm('Bạn có chắc muốn xóa voucher này?')">Xóa</button>
            </form>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="voucher-details">
            <div class="detail-row">
                <div class="detail-label">ID:</div>
                <div class="detail-value">{{ $voucher->id }}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Tên voucher:</div>
                <div class="detail-value">{{ $voucher->name }}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Mã voucher:</div>
                <div class="detail-value"><strong>{{ $voucher->code }}</strong></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Loại voucher:</div>
                <div class="detail-value">
                    {{ $voucher->type == 'fixed' ? 'Giảm cố định' : 'Giảm theo phần trăm' }}
                </div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Giá trị giảm:</div>
                <div class="detail-value">
                    @if($voucher->type == 'fixed')
                        {{ number_format($voucher->sale_price) }} VNĐ
                    @else
                        {{ $voucher->sale_price }}%
                    @endif
                </div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Đơn hàng tối thiểu:</div>
                <div class="detail-value">{{ number_format($voucher->min_order) }} VNĐ</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Giảm tối đa:</div>
                <div class="detail-value">
                    @if($voucher->max_price)
                        {{ number_format($voucher->max_price) }} VNĐ
                    @else
                        Không giới hạn
                    @endif
                </div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Số lượng:</div>
                <div class="detail-value">{{ $voucher->quantity }}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Ngày bắt đầu:</div>
                <div class="detail-value">{{ \Carbon\Carbon::parse($voucher->start_date)->format('d/m/Y H:i') }}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Ngày kết thúc:</div>
                <div class="detail-value">{{ \Carbon\Carbon::parse($voucher->end_date)->format('d/m/Y H:i') }}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Trạng thái:</div>
                <div class="detail-value">
                    @php
                        $now = now();
                        $startDate = \Carbon\Carbon::parse($voucher->start_date);
                        $endDate = \Carbon\Carbon::parse($voucher->end_date);
                    @endphp
                    
                    @if($voucher->quantity <= 0)
                        <span class="status-badge status-inactive">Hết hàng</span>
                    @elseif($now < $startDate)
                        <span class="status-badge status-inactive">Chưa bắt đầu</span>
                    @elseif($now > $endDate)
                        <span class="status-badge status-expired">Đã hết hạn</span>
                    @else
                        <span class="status-badge status-active">Đang hoạt động</span>
                    @endif
                </div>
            </div>
            
            @if($voucher->description)
            <div class="detail-row">
                <div class="detail-label">Mô tả:</div>
                <div class="detail-value">{{ $voucher->description }}</div>
            </div>
            @endif
            
            <div class="detail-row">
                <div class="detail-label">Ngày tạo:</div>
                <div class="detail-value">{{ $voucher->created_at ? $voucher->created_at->format('d/m/Y H:i:s') : 'N/A' }}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Cập nhật lần cuối:</div>
                <div class="detail-value">{{ $voucher->updated_at ? $voucher->updated_at->format('d/m/Y H:i:s') : 'N/A' }}</div>
            </div>
        </div>
    </div>
</body>
</html>