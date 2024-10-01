<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\Comment;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;

class CommentController extends Controller
{
    use HttpResponses;
    public function index($case_id)
    {
        $case = State::findOrFail($case_id);
        return $this->success([
            'comments' => $case->comments,
        ], "case comments");
    }

    public function store(StoreCommentRequest $request)
    {
        $user = Auth::user();

        $comment = Comment::create([

            'user_id' => $user->id,
            'case_id' => $request->case_id,
            'comment' => $request->comment,

        ]);

        return $this->success([
            // 'images by files' => $images,
            'comment' => $comment,
        ], "Comment added successfully");
    }

    public function destroy($comment_id)
    {
        $comment = Comment::findOrFail($comment_id);
        $comment->delete();
        return $this->success([], "Comment deleted successfully");
    }
}
