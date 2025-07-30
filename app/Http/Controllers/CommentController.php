<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index(Product $product)
    {
        $comments = Comment::with(['user', 'replies.user', 'likes'])
            ->where('product_id', $product->id)
            ->approved()
            ->parentComments()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($comments);
    }

    public function store(Request $request, Product $product)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        // Kiểm tra người dùng đã mua sản phẩm chưa
        $hasPurchased = $this->checkUserPurchase(Auth::id(), $product->id);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'content' => $request->content,
            'rating' => $request->rating,
            'is_verified_purchase' => $hasPurchased,
            'status' => 'pending' // Cần admin duyệt
        ]);

        return response()->json([
            'message' => 'Bình luận của bạn đã được gửi và đang chờ duyệt.',
            'comment' => $comment->load('user')
        ], 201);
    }

    public function update(Request $request, Comment $comment)
    {
        // Kiểm tra quyền sở hữu
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Không có quyền chỉnh sửa bình luận này.'], 403);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $comment->update([
            'content' => $request->content,
            'rating' => $request->rating,
            'status' => 'pending' // Cần duyệt lại sau khi chỉnh sửa
        ]);

        return response()->json([
            'message' => 'Bình luận đã được cập nhật và đang chờ duyệt.',
            'comment' => $comment->load('user')
        ]);
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Không có quyền xóa bình luận này.'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Bình luận đã được xóa.']);
    }

    public function reply(Request $request, Comment $comment)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // Không cho phép trả lời bình luận đã bị từ chối
        if ($comment->isRejected()) {
            return response()->json(['message' => 'Không thể trả lời bình luận này.'], 400);
        }

        $reply = Comment::create([
            'user_id' => Auth::id(),
            'product_id' => $comment->product_id,
            'parent_id' => $comment->id,
            'content' => $request->content,
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Phản hồi của bạn đã được gửi và đang chờ duyệt.',
            'reply' => $reply->load('user')
        ], 201);
    }

    public function like(Comment $comment)
    {
        if ($comment->isLikedBy(Auth::user())) {
            return response()->json(['message' => 'Bạn đã thích bình luận này rồi.'], 400);
        }

        $comment->likes()->attach(Auth::id());

        return response()->json([
            'message' => 'Đã thích bình luận.',
            'likes_count' => $comment->likesCount()
        ]);
    }

    public function unlike(Comment $comment)
    {
        if (!$comment->isLikedBy(Auth::user())) {
            return response()->json(['message' => 'Bạn chưa thích bình luận này.'], 400);
        }

        $comment->likes()->detach(Auth::id());

        return response()->json([
            'message' => 'Đã bỏ thích bình luận.',
            'likes_count' => $comment->likesCount()
        ]);
    }

    private function checkUserPurchase($userId, $productId): bool
    {
        // Logic kiểm tra người dùng đã mua sản phẩm chưa
        // Bạn có thể customize logic này dựa trên structure của bảng orders
        return \DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.user_id', $userId)
            ->where('order_items.product_id', $productId)
            ->where('orders.status', 'completed')
            ->exists();
    }
}