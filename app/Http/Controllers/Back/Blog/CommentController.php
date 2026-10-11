<?php

namespace App\Http\Controllers\Back\Blog;

use App\Http\Controllers\Controller;
use App\Models\Blog\Comment;
use Illuminate\Http\Request;

/**
 * Moderasi komentar blog. Komentar dari pengunjung berstatus "pending" dan baru tampil
 * di halaman artikel setelah disetujui (approved).
 */
class CommentController extends Controller
{
    public const FILTER_ALL = 'all';

    public function index(Request $request)
    {
        $status = (string) $request->query('status', Comment::STATUS_PENDING);

        if (!in_array($status, [...Comment::STATUSES, self::FILTER_ALL], true)) {
            $status = Comment::STATUS_PENDING;
        }

        $search = trim((string) $request->query('q', ''));

        $comments = Comment::with(['article', 'moderator'])
            ->when($status !== self::FILTER_ALL, fn ($query) => $query->where('status', $status))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $counts = Comment::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('back.comment.index', [
            'comments' => $comments,
            'status' => $status,
            'search' => $search,
            'counts' => $counts,
            'total' => $counts->sum(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'status' => 'required|in:' . implode(',', Comment::STATUSES),
        ]);

        Comment::findOrFail($id)->moderate($data['status'], $request->user());

        return back()->with('success', match ($data['status']) {
            Comment::STATUS_APPROVED => __('Comment approved and now visible on the website.'),
            Comment::STATUS_SPAM => __('Comment marked as spam.'),
            default => __('Comment moved back to pending.'),
        });
    }

    public function destroy(string $id)
    {
        Comment::findOrFail($id)->delete();

        return back()->with('success', __('Comment has been deleted.'));
    }
}
