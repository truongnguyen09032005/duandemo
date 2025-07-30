@extends('admins.layouts.master')

@section('title', 'Quản lý Bình luận')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý Bình luận</h1>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng bình luận</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Chờ duyệt</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Đã duyệt</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['approved'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Bị từ chối</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['rejected'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Bộ lọc</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.comments.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Trạng thái</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Tất cả</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Bị từ chối</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="product_id">Sản phẩm</label>
                            <select name="product_id" id="product_id" class="form-control">
                                <option value="">Tất cả sản phẩm</option>
                                @foreach(\App\Models\Product::all() as $product)
                                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search">Tìm kiếm</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Tìm theo nội dung hoặc tên người dùng..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i> Lọc
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Comments Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách Bình luận</h6>
            <div class="dropdown no-arrow">
                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="bulkActionDropdown" 
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" disabled>
                    Thao tác hàng loạt
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="bulkActionDropdown">
                    <a class="dropdown-item" href="#" onclick="bulkAction('approve')">
                        <i class="fas fa-check text-success"></i> Duyệt tất cả
                    </a>
                    <a class="dropdown-item" href="#" onclick="bulkAction('reject')">
                        <i class="fas fa-times text-warning"></i> Từ chối tất cả
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" onclick="bulkAction('delete')">
                        <i class="fas fa-trash text-danger"></i> Xóa tất cả
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>Người dùng</th>
                            <th>Sản phẩm</th>
                            <th>Nội dung</th>
                            <th>Đánh giá</th>
                            <th>Trạng thái</th>
                            <th>Đã mua</th>
                            <th>Ngày tạo</th>
                            <th width="150">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comments as $comment)
                            <tr>
                                <td>
                                    <input type="checkbox" class="comment-checkbox" value="{{ $comment->id }}">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-2">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                 style="width: 35px; height: 35px; font-size: 14px;">
                                                {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">{{ $comment->user->name }}</div>
                                            <small class="text-muted">{{ $comment->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="#" class="text-decoration-none">{{ $comment->product->name }}</a>
                                </td>
                                <td>
                                    <div style="max-width: 300px;">
                                        {{ Str::limit($comment->content, 100) }}
                                        @if($comment->isReply())
                                            <small class="text-muted d-block">
                                                <i class="fas fa-reply"></i> Phản hồi bình luận
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($comment->rating)
                                        <div class="text-warning">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star{{ $i <= $comment->rating ? '' : '-o' }}"></i>
                                            @endfor
                                            <small class="text-muted">({{ $comment->rating }}/5)</small>
                                        </div>
                                    @else
                                        <span class="text-muted">Không có</span>
                                    @endif
                                </td>
                                <td>
                                    @if($comment->status == 'pending')
                                        <span class="badge badge-warning">Chờ duyệt</span>
                                    @elseif($comment->status == 'approved')
                                        <span class="badge badge-success">Đã duyệt</span>
                                    @else
                                        <span class="badge badge-danger">Bị từ chối</span>
                                    @endif
                                </td>
                                <td>
                                    @if($comment->is_verified_purchase)
                                        <span class="badge badge-info">
                                            <i class="fas fa-check"></i> Đã mua
                                        </span>
                                    @else
                                        <span class="text-muted">Chưa mua</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $comment->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.comments.show', $comment) }}" 
                                           class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($comment->status == 'pending')
                                            <button class="btn btn-success btn-sm" 
                                                    onclick="updateStatus({{ $comment->id }}, 'approve')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-warning btn-sm" 
                                                    onclick="updateStatus({{ $comment->id }}, 'reject')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @elseif($comment->status == 'approved')
                                            <button class="btn btn-warning btn-sm" 
                                                    onclick="updateStatus({{ $comment->id }}, 'reject')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-success btn-sm" 
                                                    onclick="updateStatus({{ $comment->id }}, 'approve')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        
                                        <button class="btn btn-danger btn-sm" 
                                                onclick="deleteComment({{ $comment->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-comments fa-3x mb-3"></i>
                                        <p>Không có bình luận nào được tìm thấy.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $comments->links() }}
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Select All functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.comment-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        toggleBulkActionButton();
    });

    // Individual checkbox change
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('comment-checkbox')) {
            toggleBulkActionButton();
        }
    });

    function toggleBulkActionButton() {
        const checkedBoxes = document.querySelectorAll('.comment-checkbox:checked');
        const bulkActionBtn = document.getElementById('bulkActionDropdown');
        bulkActionBtn.disabled = checkedBoxes.length === 0;
    }

    function updateStatus(commentId, action) {
        const url = action === 'approve' 
            ? `/admin/comments/${commentId}/approve`
            : `/admin/comments/${commentId}/reject`;

        fetch(url, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                alert(data.message);
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra. Vui lòng thử lại.');
        });
    }

    function deleteComment(commentId) {
        if (confirm('Bạn có chắc chắn muốn xóa bình luận này?')) {
            fetch(`/admin/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert(data.message);
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            });
        }
    }

    function bulkAction(action) {
        const checkedBoxes = document.querySelectorAll('.comment-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Vui lòng chọn ít nhất một bình luận.');
            return;
        }

        const commentIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        let confirmMessage;
        switch (action) {
            case 'approve':
                confirmMessage = `Bạn có chắc chắn muốn duyệt ${commentIds.length} bình luận?`;
                break;
            case 'reject':
                confirmMessage = `Bạn có chắc chắn muốn từ chối ${commentIds.length} bình luận?`;
                break;
            case 'delete':
                confirmMessage = `Bạn có chắc chắn muốn xóa ${commentIds.length} bình luận?`;
                break;
        }

        if (confirm(confirmMessage)) {
            fetch('/admin/comments/bulk-action', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    action: action,
                    comment_ids: commentIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert(data.message);
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            });
        }
    }
</script>
@endpush