@extends('admins.layouts.app')

@section('title', 'Quản lý đơn hàng')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Danh sách đơn hàng</h3>
                    <a href="{{ url('/admin') }}" class="btn btn-secondary float-right">
                      <i class="fas fa-arrow-left"></i> Quay lại trang chủ Admin
                     </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Form tìm kiếm cải tiến -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('admin.orders.index') }}" id="searchForm">
                                <div class="input-group">
                                    <input type="text" 
                                           name="keyword" 
                                           class="form-control" 
                                           placeholder="Tìm kiếm theo ID, tên khách hàng, email, SĐT..." 
                                           value="{{ request('keyword') }}"
                                           id="searchInput">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit" id="searchBtn">
                                            <i class="fas fa-search"></i> Tìm kiếm
                                        </button>
                                        @if(request('keyword'))
                                            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary" title="Xóa tìm kiếm">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        @if(request('keyword'))
                        <div class="col-md-6">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle"></i>
                                Tìm thấy {{ $orders->total() }} kết quả cho "<strong>{{ request('keyword') }}</strong>"
                                <a href="{{ route('admin.orders.index') }}" class="alert-link ml-2">Xóa bộ lọc</a>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Khách hàng</th>
                                    <th>Email</th>
                                    <th>Số điện thoại</th>
                                    <th>Địa chỉ</th>
                                    <th>Tổng tiền</th>
                                    <th>Thanh toán</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td><strong>#{{ $order->id }}</strong></td>
                                        <td>{{ $order->user->name ?? 'N/A' }}</td>
                                        <td>{{ $order->email }}</td>
                                        <td>{{ $order->phone }}</td>
                                        <td>
                                            <span title="{{ $order->address }}">
                                                {{ Str::limit($order->address, 30) }}
                                            </span>
                                        </td>
                                        <td><strong>{{ number_format($order->total, 0, ',', '.') }}đ</strong></td>
                                        <td>
                                            @if($order->payment == 'cod')
                                                <span class="badge badge-warning">COD</span>
                                            @elseif($order->payment == 'online')
                                                <span class="badge badge-info">Online</span>
                                            @else
                                                <span class="badge badge-secondary">{{ $order->payment }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @switch($order->status)
                                                @case('pending')
                                                    <span class="badge badge-warning">Chờ xử lý</span>
                                                    @break
                                                @case('confirmed')
                                                    <span class="badge badge-info">Đã xác nhận</span>
                                                    @break
                                                @case('processing')
                                                    <span class="badge badge-primary">Đang xử lý</span>
                                                    @break
                                                @case('shipping')
                                                    <span class="badge badge-secondary">Đang giao hàng</span>
                                                    @break
                                                @case('delivered')
                                                    <span class="badge badge-success">Đã giao</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge badge-danger">Đã hủy</span>
                                                    @break
                                                @case('returned')
                                                    <span class="badge badge-dark">Đã trả hàng</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-secondary">Không xác định</span>
                                            @endswitch
                                        </td>
                                        <td>{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.orders.show', $order->id) }}" 
                                                   class="btn btn-sm btn-info" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                <!-- Dropdown cập nhật trạng thái -->
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-secondary dropdown-toggle" 
                                                            type="button" data-toggle="dropdown" title="Cập nhật trạng thái">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <button class="dropdown-item" name="status" value="pending" type="submit">
                                                                <i class="fas fa-clock text-warning"></i> Chờ xử lý
                                                            </button>
                                                            <button class="dropdown-item" name="status" value="confirmed" type="submit">
                                                                <i class="fas fa-check text-info"></i> Đã xác nhận
                                                            </button>
                                                            <button class="dropdown-item" name="status" value="processing" type="submit">
                                                                <i class="fas fa-cogs text-primary"></i> Đang xử lý
                                                            </button>
                                                            <button class="dropdown-item" name="status" value="shipping" type="submit">
                                                                <i class="fas fa-truck text-secondary"></i> Đang giao hàng
                                                            </button>
                                                            <button class="dropdown-item" name="status" value="delivered" type="submit">
                                                                <i class="fas fa-check-circle text-success"></i> Đã giao
                                                            </button>
                                                            <button class="dropdown-item text-danger" name="status" value="cancelled" type="submit">
                                                                <i class="fas fa-times text-danger"></i> Hủy đơn
                                                            </button>
                                                            <button class="dropdown-item" name="status" value="returned" type="submit">
                                                                <i class="fas fa-undo text-dark"></i> Trả hàng
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>

                                                <form method="POST" action="{{ route('admin.orders.destroy', $order->id) }}" 
                                                      style="display: inline-block;"
                                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                            <p class="text-muted">
                                                @if(request('keyword'))
                                                    Không tìm thấy đơn hàng nào phù hợp với từ khóa "<strong>{{ request('keyword') }}</strong>"
                                                @else
                                                    Chưa có đơn hàng nào
                                                @endif
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Phân trang với query parameters -->
                    @if($orders->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $orders->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection