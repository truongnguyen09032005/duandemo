<!-- resources/views/admins/vouchers/edit.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Chỉnh sửa Voucher</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group .error {
            color: red;
            font-size: 0.9em;
        }
        .btn {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Chỉnh sửa Voucher</h2>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.vouchers.update', $voucher) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Tên Voucher</label>
                <input type="text" name="name" id="name" value="{{ old('name', $voucher->name) }}" required>
            </div>

            <div class="form-group">
                <label for="code">Mã Voucher</label>
                <input type="text" name="code" id="code" value="{{ old('code', $voucher->code) }}" required>
            </div>

            <div class="form-group">
                <label for="type">Loại Voucher</label>
                <select name="type" id="type" required>
                    <option value="fixed" {{ old('type', $voucher->type) == 'fixed' ? 'selected' : '' }}>Giảm giá cố định</option>
                    <option value="percent" {{ old('type', $voucher->type) == 'percent' ? 'selected' : '' }}>Giảm giá theo phần trăm</option>
                </select>
            </div>

            <div class="form-group">
                <label for="sale_price">Giá trị giảm</label>
                <input type="number" step="0.01" name="sale_price" id="sale_price" value="{{ old('sale_price', $voucher->sale_price) }}" required>
            </div>

            <div class="form-group">
                <label for="min_order">Giá trị đơn hàng tối thiểu</label>
                <input type="number" step="0.01" name="min_order" id="min_order" value="{{ old('min_order', $voucher->min_order) }}" required>
            </div>

            <div class="form-group">
                <label for="max_price">Giá trị giảm tối đa</label>
                <input type="number" step="0.01" name="max_price" id="max_price" value="{{ old('max_price', $voucher->max_price) }}" required>
            </div>

            <div class="form-group">
                <label for="quantity">Số lượng</label>
                <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $voucher->quantity) }}" required>
            </div>

            <div class="form-group">
                <label for="start_date">Ngày bắt đầu</label>
                <input type="datetime-local" name="start_date" id="start_date" value="{{ old('start_date', $voucher->start_date->format('Y-m-d\TH:i')) }}" required>
            </div>

            <div class="form-group">
                <label for="end_date">Ngày kết thúc</label>
                <input type="datetime-local" name="end_date" id="end_date" value="{{ old('end_date', $voucher->end_date->format('Y-m-d\TH:i')) }}" required>
            </div>

            <button type="submit" class="btn">Cập nhật Voucher</button>
        </form>
    </div>
</body>
</html>