<?php

namespace App\Http\Controllers;

use App\Inspections\Spam;
use App\Reply;
use App\Thread;
use Illuminate\Http\Request;

class RepliesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }

    public function store($channelId, Thread $thread) 
    {
        try {
            $this->validateReply();

            $reply =  $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id()
        ]);
        } catch (\Exception $e) {
            return response('Sorry, your reply could not be saved at this time', 422);
        }

        return $reply->load('owner');
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

        try {
            $this->validateReply();

            $reply->update(['body' => request('body')]);
        } catch (\Exception $e) {
            return response('Sorry, your reply could not be saved at this time', 422);
        }

        
    }

    public function index($channelId, Thread $thread) 
    {
        return $thread->replies()->paginate(10);
    }

    protected function validateReply() 
    {
        $this->validate(request(), ['body' => 'required']);
        resolve(Spam::class)->detect(request('body'));
    }

}
