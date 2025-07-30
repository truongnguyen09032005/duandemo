@extends('admins.layouts.master')

@section('title', 'Chi tiết Bình luận')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết Bình luận</h1>
        <a href="{{ route('admin.comments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="row">
        <!-- Comment Details -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin Bình luận</h6>
                    <div>
                        @if($comment->status == 'pending')
                            <button class="btn btn-success btn-sm" onclick="updateStatus('approve')">
                                <i class="fas fa-check"></i> Duyệt
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="updateStatus('reject')">
                                <i class="fas fa-times"></i> Từ chối
                            </button>
                        @elseif($comment->status == 'approved')
                            <button class="btn btn-warning btn-sm" onclick="updateStatus('reject')">
                                <i class="fas fa-times"></i> Từ chối
                            </button>
                        @else
                            <button class="btn btn-success btn-sm" onclick="updateStatus('approve')">
                                <i class="fas fa-check"></i> Duyệt
                            </button>
                        @endif
                        <button class="btn btn-danger btn-sm" onclick="deleteComment()">
                            <i class="fas fa-trash"></i> Xóa
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- User Info -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="mr-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 50px; height: 50px; font-size: 18px;">
                                {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-1">{{ $comment->user->name }}</h5>
                            <small class="text-muted">{{ $comment->user->email }}</small>
                            @if($comment->is_verified_purchase)
                                <span class="badge badge-info ml-2">
                                    <i class="fas fa-check"></i> Đã mua sản phẩm
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="mb-3">
                        <strong>Sản phẩm:</strong> 
                        <a href="#" class="text-decoration-none">{{ $comment->product->name }}</a>
                    </div>

                    <!-- Rating -->
                    @if($comment->rating)
                        <div class="mb-3">
                            <strong>Đánh giá:</strong>
                            <div class="text-warning d-inline-block ml-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star{{ $i <= $comment->rating ? '' : '-o' }}"></i>
                                @endfor
                                <span class="text-muted">({{ $comment->rating }}/5)</span>
                            </div>
                        </div>
                    @endif

                    <!-- Content -->
                    <div class="mb-3">
                        <strong>Nội dung:</strong>
                        <div class="mt-2 p-3 bg-light rounded">
                            {{ $comment->content }}
                        </div>
                    </div>

                    <!-- Parent Comment (if this is a reply) -->
                    @if($comment->parent)
                        <div class="mb-3">
                            <strong>Phản hồi cho bình luận:</strong>
                            <div class="mt-2 p-3 border-left border-primary bg-light">
                                <small class="text-muted">{{ $comment->parent->user->name }} - {{ $comment->parent->created_at->format('d/m/Y H:i') }}</small>
                                <div class="mt-1">{{ Str::limit($comment->parent->content, 200) }}</div>
                            </div>
                        </div>
                    @endif

                    <!-- Status -->
                    <div class="mb-3">
                        <strong>Trạng thái:</strong>
                        @if($comment->status == 'pending')
                            <span class="badge badge-warning ml-2">Chờ duyệt</span>
                        @elseif($comment->status == 'approved')
                            <span class="badge badge-success ml-2">Đã duyệt</span>
                        @else
                            <span class="badge badge-danger ml-2">Bị từ chối</span>
                        @endif
                    </div>

                    <!-- Timestamps -->
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Ngày tạo:</strong>
                            <div class="text-muted">{{ $comment->created_at->format('d/m/Y H:i:s') }}</div>
                        </div>
                        <div class="col-md-6">
                            <strong>Cập nhật lần cuối:</strong>
                            <div class="text-muted">{{ $comment->updated_at->format('d/m/Y H:i:s') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Reply Form -->
            @if($comment->status == 'approved')
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Phản hồi bình luận</h6>
                    </div>
                    <div class="card-body">
                        <form id="replyForm">
                            <div class="form-group">
                                <textarea name="content" id="replyContent" class="form-control" rows="4" 
                                          placeholder="Nhập phản hồi của bạn..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-reply"></i> Gửi phản hồi
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Replies -->
            @if($comment->replies->count() > 0)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Phản hồi ({{ $comment->replies->count() }})
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach($comment->replies as $reply)
                            <div class="border-bottom mb-3 pb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="mr-2">
                                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 35px; height: 35px; font-size: 14px;">
                                            {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                        </div>
                                    </div>
                                    <div>
                                        <strong>{{ $reply->user->name }}</strong>
                                        @if($reply->user->isAdmin())
                                            <span class="badge badge-primary badge-sm">Admin</span>
                                        @endif
                                        <small class="text-muted d-block">{{ $reply->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                </div>
                                <div class="ml-5">
                                    {{ $reply->content }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thống kê nhanh</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Lượt thích:</strong>
                        <span class="text-primary">{{ $comment->likes->count() }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Số phản hồi:</strong>
                        <span class="text-info">{{ $comment->replies->count() }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Tổng bình luận của user:</strong>
                        <span class="text-success">{{ $comment->user->comments->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- User's Other Comments -->
            @if($comment->user->comments->where('id', '!=', $comment->id)->count() > 0)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Bình luận khác của user</h6>
                    </div>
                    <div class="card-body">
                        @foreach($comment->user->comments->where('id', '!=', $comment->id)->take(5) as $otherComment)
                            <div class="mb-3 pb-2 border-bottom">
                                <div class="small text-muted">{{ $otherComment->product->name }}</div>
                                <div>{{ Str::limit($otherComment->content, 100) }}</div>
                                <small class="text-muted">{{ $otherComment->created_at->format('d/m/Y') }}</small>
                            </div>
                        @endforeach
                        @if($comment->user->comments->where('id', '!=', $comment->id)->count() > 5)
                            <small class="text-muted">
                                Và {{ $comment->user->comments->where('id', '!=', $comment->id)->count() - 5 }} bình luận khác...
                            </small>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function updateStatus(action) {
        const url = action === 'approve' 
            ? `/admin/comments/{{ $comment->id }}/approve`
            : `/admin/comments/{{ $comment->id }}/reject`;

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

    function deleteComment() {
        if (confirm('Bạn có chắc chắn muốn xóa bình luận này?')) {
            fetch(`/admin/comments/{{ $comment->id }}`, {
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
                    window.location.href = '{{ route('admin.comments.index') }}';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            });
        }
    }

    // Handle reply form submission
    document.getElementById('replyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const content = document.getElementById('replyContent').value.trim();
        if (!content) {
            alert('Vui lòng nhập nội dung phản hồi.');
            return;
        }

        fetch(`/admin/comments/{{ $comment->id }}/reply`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                content: content
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
    });
</script>
@endpush