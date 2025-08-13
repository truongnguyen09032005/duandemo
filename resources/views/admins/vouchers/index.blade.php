<!-- resources/views/admins/vouchers/index.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Danh sách Voucher</title>
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
        .btn-create {
            background-color: #28a745;
        }
        .btn-create:hover {
            background-color: #218838;
        }
        .btn-back-admin {
            background-color: #6c757d;
        }
        .btn-back-admin:hover {
            background-color: #5a6268;
        }
        .action-buttons {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
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
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .alert ul {
            margin: 0;
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Danh sách Voucher</h2>
        
        <div class="action-buttons">
            <a href="/admin" class="btn btn-back-admin">← Quay lại trang Admin</a>
            <a href="{{ route('admin.vouchers.create') }}" class="btn btn-create">+ Tạo Voucher Mới</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Mã</th>
                    <th>Loại</th>
                    <th>Giá trị giảm</th>
                    <th>Đơn hàng tối thiểu</th>
                    <th>Giảm tối đa</th>
                    <th>Số lượng</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày kết thúc</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vouchers as $voucher)
                    <tr>
                        <td>{{ $voucher->id }}</td>
                        <td>{{ $voucher->name }}</td>
                        <td>{{ $voucher->code }}</td>
                        <td>{{ $voucher->type == 'fixed' ? 'Cố định' : 'Phần trăm' }}</td>
                        <td>{{ $voucher->sale_price }}</td>
                        <td>{{ $voucher->min_order }}</td>
                        <td>{{ $voucher->max_price }}</td>
                        <td>{{ $voucher->quantity }}</td>
                        <td>{{ $voucher->start_date }}</td>
                        <td>{{ $voucher->end_date }}</td>
                        <td>
                            <a href="{{ route('admin.vouchers.show', $voucher) }}">Xem</a> |
                            <a href="{{ route('admin.vouchers.edit', $voucher) }}">Sửa</a> |
                            <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $vouchers->links() }}
    </div>
</body>
</html>