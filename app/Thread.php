<?php

namespace App;

use App\Events\ThreadRecievedNewReply;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use RecordsActivity;

    protected $guarded = [];
    protected $with = ['owner', 'channel'];
    protected $appends = ['isSubscribedTo'];

    protected static function boot() 
    {
        parent::boot();

        static::deleting(function ($thread) {
            $thread->replies->each->delete();
        });

    }

    public function channel() 
    {
        return $this->belongsTo(Channel::class);
    }

    public function owner() 
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replies() 
    {
        return $this->hasMany(Reply::class);
    }
    
    public function path() 
    {
        return "/threads/{$this->channel->slug}/{$this->id}";
    }

    public function addReply($reply) 
    {
        $reply = $this->replies()->create($reply);

        event(new ThreadRecievedNewReply($reply));

        return $reply;
    }

    public function scopeFilter($query, $filters) 
    {
        return $filters->apply($query);
    }

    public function subscribe($userId = null) 
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
        ]);

        return $this;
    }

    public function unsubscribe($userId = null) 
    {
        $this->subscriptions()
             ->where('user_id', $userId ?: auth()->id())
             ->delete();
    }

    public function subscriptions() 
    {
        return $this->hasMany(ThreadSubscription::class);    
    }

    public function getIsSubscribedToAttribute() 
    {
        return $this->subscriptions()
                    ->where('user_id', auth()->id())
                    ->exists();
    }

    public function hasUpdatesFor($user) 
    {
        $key = $user->visitedThreadCacheKey($this);
 
        return $this->updated_at > cache($key);
    }

}
