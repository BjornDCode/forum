<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class ThreadRecievedNewReply
{
    use Dispatchable, SerializesModels;

    public $reply;

    public function __construct($reply)
    {
        $this->reply = $reply;
    }

}
