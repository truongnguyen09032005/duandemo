<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('admin.product.index') }}" class="brand-link">
        <img src="{{ asset('assets/admin/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Admin</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
                <li class="nav-item">
                    <a href="{{ url('admin') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.categories.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-th"></i>
                        <p>Danh mục</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.product.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>Sản phẩm</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.orders.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Đơn hàng
                            @php
                                try {
                                    $pendingOrdersCount = \App\Models\Order::pending()->count();
                                } catch (\Exception $e) {
                                    $pendingOrdersCount = 0;
                                }
                            @endphp
                            @if($pendingOrdersCount > 0)
                                <span class="badge badge-warning right">{{ $pendingOrdersCount }}</span>
                            @endif
                        </p>
                    </a>
                </li>

                {{-- Menu Bình luận với dropdown --}}
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-comments"></i>
                        <p>
                            Quản lý Bình luận
                            @php
                                try {
                                    $pendingCommentsCount = \App\Models\Comment::where('status', 'pending')->count();
                                } catch (\Exception $e) {
                                    $pendingCommentsCount = 0;
                                }
                            @endphp
                            @if($pendingCommentsCount > 0)
                                <span class="badge badge-danger right">{{ $pendingCommentsCount }}</span>
                            @endif
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.comments.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tất cả bình luận</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}" class="nav-link">
                                <i class="far fa-clock nav-icon"></i>
                                <p>
                                    Chờ duyệt
                                    @if($pendingCommentsCount > 0)
                                        <span class="badge badge-warning badge-sm right">{{ $pendingCommentsCount }}</span>
                                    @endif
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.comments.index', ['status' => 'approved']) }}" class="nav-link">
                                <i class="far fa-check-circle nav-icon"></i>
                                <p>Đã duyệt</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.comments.index', ['status' => 'rejected']) }}" class="nav-link">
                                <i class="far fa-times-circle nav-icon"></i>
                                <p>Bị từ chối</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa fa-book"></i>
                        <p>Banner</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Người dùng</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

{{-- CSS tùy chỉnh cho AdminLTE sidebar --}}
<style>
.nav-treeview .nav-link {
    padding-left: 2rem;
}

.badge-sm {
    font-size: 0.65rem;
    padding: 0.2rem 0.4rem;
}

.nav-link .badge.right {
    margin-left: auto;
}
</style>