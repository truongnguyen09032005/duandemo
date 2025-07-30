<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $query = Comment::with(['user', 'product', 'parent']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Search by content or user name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $comments = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total' => Comment::count(),
            'pending' => Comment::pending()->count(),
            'approved' => Comment::approved()->count(),
            'rejected' => Comment::rejected()->count(),
        ];

        return view('admins.comments.index', compact('comments', 'stats'));
    }

    public function show(Comment $comment)
    {
        $comment->load(['user', 'product', 'parent', 'replies.user', 'likes']);
        
        return view('admins.comments.show', compact('comment'));
    }

    public function approve(Comment $comment)
    {
        $comment->update(['status' => 'approved']);

        return response()->json([
            'message' => 'Bình luận đã được duyệt.',
            'status' => 'approved'
        ]);
    }

    public function reject(Comment $comment)
    {
        $comment->update(['status' => 'rejected']);

        return response()->json([
            'message' => 'Bình luận đã bị từ chối.',
            'status' => 'rejected'
        ]);
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response()->json(['message' => 'Bình luận đã được xóa.']);
    }

    public function reply(Request $request, Comment $comment)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $reply = Comment::create([
            'user_id' => auth()->id(), // Admin user
            'product_id' => $comment->product_id,
            'parent_id' => $comment->id,
            'content' => $request->content,
            'status' => 'approved' // Admin reply tự động được duyệt
        ]);

        return response()->json([
            'message' => 'Phản hồi đã được gửi.',
            'reply' => $reply->load('user')
        ], 201);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete',
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'exists:comments,id'
        ]);

        $comments = Comment::whereIn('id', $request->comment_ids);

        switch ($request->action) {
            case 'approve':
                $comments->update(['status' => 'approved']);
                $message = 'Các bình luận đã được duyệt.';
                break;
            
            case 'reject':
                $comments->update(['status' => 'rejected']);
                $message = 'Các bình luận đã bị từ chối.';
                break;
            
            case 'delete':
                $comments->delete();
                $message = 'Các bình luận đã được xóa.';
                break;
        }

        return response()->json(['message' => $message]);
    }
}