{{-- resources/views/components/product-comments.blade.php --}}
<div class="product-comments" id="product-comments">
    <!-- Comments Statistics -->
    <div class="comments-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4>Đánh giá sản phẩm</h4>
                <div class="rating-summary">
                    <div class="d-flex align-items-center mb-2">
                        <div class="average-rating mr-3">
                            <span class="rating-number">{{ $product->averageRating() }}</span>
                            <div class="stars text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star{{ $i <= $product->averageRating() ? '' : '-o' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <div class="rating-text">
                            <span>{{ $product->totalRatings() }} đánh giá</span>
                            <br>
                            <span class="text-muted">{{ $product->totalComments() }} bình luận</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Rating Distribution -->
                <div class="rating-distribution">
                    @php $distribution = $product->getRatingDistribution(); @endphp
                    @for($i = 5; $i >= 1; $i--)
                        <div class="rating-bar d-flex align-items-center mb-1">
                            <span class="rating-label">{{ $i }} sao</span>
                            <div class="progress mx-2 flex-grow-1" style="height: 8px;">
                                <div class="progress-bar bg-warning" 
                                     style="width: {{ $product->totalRatings() > 0 ? ($distribution[$i] / $product->totalRatings()) * 100 : 0 }}%"></div>
                            </div>
                            <span class="rating-count">{{ $distribution[$i] }}</span>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Comment Form -->
    @auth
        <div class="comment-form mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Viết đánh giá</h5>
                </div>
                <div class="card-body">
                    <form id="commentForm">
                        @csrf
                        <!-- Rating Selection -->
                        <div class="form-group">
                            <label>Đánh giá của bạn:</label>
                            <div class="rating-input">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star rating-star" data-rating="{{ $i }}"></i>
                                @endfor
                                <input type="hidden" name="rating" id="rating" value="5">
                            </div>
                        </div>

                        <!-- Comment Content -->
                        <div class="form-group">
                            <label for="content">Nội dung bình luận:</label>
                            <textarea name="content" id="content" class="form-control" rows="4" 
                                      placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Gửi đánh giá
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để viết đánh giá.
        </div>
    @endauth

    <!-- Comments List -->
    <div class="comments-list">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5>Tất cả bình luận ({{ $product->totalComments() }})</h5>
            <div class="comment-filters">
                <select id="commentSort" class="form-control form-control-sm" style="width: auto;">
                    <option value="newest">Mới nhất</option>
                    <option value="oldest">Cũ nhất</option>
                    <option value="highest_rating">Đánh giá cao nhất</option>
                    <option value="lowest_rating">Đánh giá thấp nhất</option>
                </select>
            </div>
        </div>

        <div id="commentsContainer">
            <!-- Comments will be loaded here via AJAX -->
        </div>

        <!-- Load More Button -->
        <div class="text-center mt-4">
            <button id="loadMoreComments" class="btn btn-outline-primary" style="display: none;">
                <i class="fas fa-plus"></i> Xem thêm bình luận
            </button>
        </div>
    </div>
</div>

<!-- Comment Template -->
<template id="commentTemplate">
    <div class="comment-item mb-4 p-3 border rounded">
        <div class="comment-header d-flex align-items-center mb-2">
            <div class="user-avatar mr-3">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                     style="width: 40px; height: 40px;">
                    <span class="user-initial"></span>
                </div>
            </div>
            <div class="user-info flex-grow-1">
                <div class="d-flex align-items-center">
                    <strong class="user-name"></strong>
                    <span class="verified-purchase badge badge-success badge-sm ml-2" style="display: none;">
                        <i class="fas fa-check"></i> Đã mua
                    </span>
                </div>
                <div class="comment-meta">
                    <div class="rating text-warning"></div>
                    <small class="text-muted comment-date"></small>
                </div>
            </div>
            <div class="comment-actions">
                <button class="btn btn-sm btn-outline-primary like-btn">
                    <i class="fas fa-thumbs-up"></i> <span class="like-count">0</span>
                </button>
                <button class="btn btn-sm btn-outline-secondary reply-btn">
                    <i class="fas fa-reply"></i> Trả lời
                </button>
            </div>
        </div>
        <div class="comment-content mb-2"></div>
        
        <!-- Reply Form (initially hidden) -->
        <div class="reply-form" style="display: none;">
            <div class="mt-3">
                <form class="reply-form-inner">
                    @csrf
                    <div class="form-group">
                        <textarea class="form-control" rows="2" placeholder="Viết phản hồi..." required></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-sm btn-primary">Gửi phản hồi</button>
                        <button type="button" class="btn btn-sm btn-secondary cancel-reply">Hủy</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Replies -->
        <div class="replies ml-4 mt-3"></div>
    </div>
</template>

<!-- Reply Template -->
<template id="replyTemplate">
    <div class="reply-item mb-2 p-2 border-left border-primary bg-light">
        <div class="reply-header d-flex align-items-center mb-1">
            <div class="user-avatar mr-2">
                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" 
                     style="width: 30px; height: 30px; font-size: 12px;">
                    <span class="user-initial"></span>
                </div>
            </div>
            <div class="user-info">
                <strong class="user-name"></strong>
                <small class="text-muted comment-date ml-2"></small>
            </div>
        </div>
        <div class="reply-content ml-4"></div>
    </div>
</template>

@push('styles')
<style>
    .rating-input .rating-star {
        font-size: 24px;
        color: #ddd;
        cursor: pointer;
        transition: color 0.2s;
    }
    
    .rating-input .rating-star:hover,
    .rating-input .rating-star.active {
        color: #ffc107;
    }
    
    .rating-summary .rating-number {
        font-size: 2rem;
        font-weight: bold;
        color: #ffc107;
    }
    
    .rating-distribution .rating-bar {
        font-size: 14px;
    }
    
    .rating-distribution .rating-label {
        width: 60px;
    }
    
    .rating-distribution .rating-count {
        width: 30px;
        text-align: right;
    }
    
    .comment-item {
        transition: all 0.3s ease;
    }
    
    .comment-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .user-avatar {
        flex-shrink: 0;
    }
    
    .like-btn.liked {
        background-color: #007bff;
        color: white;
    }
</style>
@endpush

@push('scripts')
<script>
    let currentPage = 1;
    let isLoading = false;

    // Initialize when document is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeRatingInput();
        loadComments();
        setupEventListeners();
    });

    function initializeRatingInput() {
        const stars = document.querySelectorAll('.rating-star');
        const ratingInput = document.getElementById('rating');
        
        stars.forEach((star, index) => {
            star.addEventListener('click', function() {
                const rating = index + 1;
                ratingInput.value = rating;
                updateStars(rating);
            });
            
            star.addEventListener('mouseover', function() {
                updateStars(index + 1);
            });
        });
        
        document.querySelector('.rating-input').addEventListener('mouseleave', function() {
            updateStars(ratingInput.value);
        });
        
        // Initialize with 5 stars
        updateStars(5);
    }

    function updateStars(rating) {
        const stars = document.querySelectorAll('.rating-star');
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.add('active');
            } else {
                star.classList.remove('active');
            }
        });
    }

    function setupEventListeners() {
        // Comment form submission
        document.getElementById('commentForm').addEventListener('submit', submitComment);
        
        // Sort change
        document.getElementById('commentSort').addEventListener('change', function() {
            currentPage = 1;
            loadComments();
        });
        
        // Load more button
        document.getElementById('loadMoreComments').addEventListener('click', function() {
            currentPage++;
            loadComments(true);
        });
    }

    function submitComment(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        
        fetch(`/comments/products/{{ $product->id }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                alert(data.message);
                e.target.reset();
                updateStars(5);
                document.getElementById('rating').value = 5;
                // Reload comments if approved
                if (data.comment && data.comment.status === 'approved') {
                    currentPage = 1;
                    loadComments();
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra. Vui lòng thử lại.');
        });
    }

    function loadComments(append = false) {
        if (isLoading) return;
        
        isLoading = true;
        const sort = document.getElementById('commentSort').value;
        
        fetch(`/products/{{ $product->id }}/comments?page=${currentPage}&sort=${sort}`)
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('commentsContainer');
                
                if (!append) {
                    container.innerHTML = '';
                }
                
                data.data.forEach(comment => {
                    container.appendChild(createCommentElement(comment));
                });
                
                // Show/hide load more button
                const loadMoreBtn = document.getElementById('loadMoreComments');
                if (data.next_page_url) {
                    loadMoreBtn.style.display = 'block';
                } else {
                    loadMoreBtn.style.display = 'none';
                }
                
                isLoading = false;
            })
            .catch(error => {
                console.error('Error:', error);
                isLoading = false;
            });
    }

    function createCommentElement(comment) {
        const template = document.getElementById('commentTemplate');
        const element = template.content.cloneNode(true);
        
        // Fill comment data
        element.querySelector('.user-initial').textContent = comment.user.name.charAt(0).toUpperCase();
        element.querySelector('.user-name').textContent = comment.user.name;
        element.querySelector('.comment-content').textContent = comment.content;
        element.querySelector('.comment-date').textContent = new Date(comment.created_at).toLocaleDateString('vi-VN');
        element.querySelector('.like-count').textContent = comment.likes_count || 0;
        
        // Show verified purchase badge
        if (comment.is_verified_purchase) {
            element.querySelector('.verified-purchase').style.display = 'inline-block';
        }
        
        // Show rating if exists
        if (comment.rating) {
            const ratingDiv = element.querySelector('.rating');
            for (let i = 1; i <= 5; i++) {
                const star = document.createElement('i');
                star.className = `fas fa-star${i <= comment.rating ? '' : '-o'}`;
                ratingDiv.appendChild(star);
            }
        }
        
        // Add replies
        const repliesContainer = element.querySelector('.replies');
        if (comment.replies && comment.replies.length > 0) {
            comment.replies.forEach(reply => {
                repliesContainer.appendChild(createReplyElement(reply));
            });
        }
        
        // Add event listeners
        const commentDiv = element.querySelector('.comment-item');
        commentDiv.dataset.commentId = comment.id;
        
        // Like button
        const likeBtn = element.querySelector('.like-btn');
        likeBtn.addEventListener('click', () => toggleLike(comment.id, likeBtn));
        
        // Reply button
        const replyBtn = element.querySelector('.reply-btn');
        const replyForm = element.querySelector('.reply-form');
        replyBtn.addEventListener('click', () => {
            replyForm.style.display = replyForm.style.display === 'none' ? 'block' : 'none';
        });
        
        // Cancel reply
        element.querySelector('.cancel-reply').addEventListener('click', () => {
            replyForm.style.display = 'none';
        });
        
        // Reply form submission
        element.querySelector('.reply-form-inner').addEventListener('submit', (e) => {
            submitReply(e, comment.id);
        });
        
        return element;
    }

    function createReplyElement(reply) {
        const template = document.getElementById('replyTemplate');
        const element = template.content.cloneNode(true);
        
        element.querySelector('.user-initial').textContent = reply.user.name.charAt(0).toUpperCase();
        element.querySelector('.user-name').textContent = reply.user.name;
        element.querySelector('.reply-content').textContent = reply.content;
        element.querySelector('.comment-date').textContent = new Date(reply.created_at).toLocaleDateString('vi-VN');
        
        return element;
    }

    function toggleLike(commentId, button) {
        @guest
            alert('Vui lòng đăng nhập để thích bình luận.');
            return;
        @endguest
        
        const isLiked = button.classList.contains('liked');
        const url = isLiked ? `/comments/${commentId}/unlike` : `/comments/${commentId}/like`;
        const method = isLiked ? 'DELETE' : 'POST';
        
        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.likes_count !== undefined) {
                button.querySelector('.like-count').textContent = data.likes_count;
                button.classList.toggle('liked');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function submitReply(e, commentId) {
        e.preventDefault();
        
        @guest
            alert('Vui lòng đăng nhập để trả lời bình luận.');
            return;
        @endguest
        
        const textarea = e.target.querySelector('textarea');
        const content = textarea.value.trim();
        
        if (!content) {
            alert('Vui lòng nhập nội dung phản hồi.');
            return;
        }
        
        fetch(`/comments/${commentId}/reply`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ content: content })
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                alert(data.message);
                textarea.value = '';
                e.target.closest('.reply-form').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra. Vui lòng thử lại.');
        });
    }
</script>
@endpush