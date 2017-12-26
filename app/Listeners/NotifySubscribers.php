<?php

namespace App\Listeners;

use App\Events\ThreadRecievedNewReply;

class NotifySubscribers
{

    public function handle(ThreadRecievedNewReply $event)
    {
        $thread = $event->reply->thread;

        $thread->subscriptions
             ->where('user_id', '!=', $event->reply->user_id)
             ->each
             ->notify($event->reply);
    }
    
}
