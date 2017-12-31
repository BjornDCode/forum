<?php

namespace App\Http\Controllers;

use App\Reply;
use App\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\CreatePostRequest;

class RepliesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }

    public function store($channelId, Thread $thread, CreatePostRequest $form) 
    {
        return $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id()
        ])->load('owner');
    }

    public function destroy(Reply $reply) 
    {
        $this->authorize('update', $reply);

        $reply->delete();

        if (request()->expectsJson()) {
            return response(['status' => 'Reply Deleted.']);
        }

        return back();
    }

    public function update(Reply $reply) 
    {
        $this->authorize('update', $reply);

        $this->validate(request(), ['body' => 'required|spamfree']);

        $reply->update(['body' => request('body')]);
        
    }

    public function index($channelId, Thread $thread) 
    {
        return $thread->replies()->paginate(10);
    }

}
