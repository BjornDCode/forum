<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function an_unauthenticated_user_cannot_see_the_create_threads_page()
    {
        $this->withExceptionHandling();

        $this->post('/threads')
             ->assertRedirect('/login');

        $this->get('/threads/create')
             ->assertRedirect('/login');
    }

    /** @test */
    public function an_authenticated_user_can_create_new_threads()
    {
        $this->signIn();

        $thread = make('App\Thread');
        $response = $this->post('/threads', $thread->toArray());

        $this->get($response->headers->get('Location'))
             ->assertSee($thread->title);
    }

    /** @test */
    public function a_thread_requires_a_title()
    {
        $this->publishThread(['title' => null])
             ->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_thread_requires_a_body()
    {
        $this->publishThread(['body' => null])
             ->assertSessionHasErrors('body');
    }

    /** @test */
    public function a_thread_requires_a_valid_channel()
    {
        factory('App\Channel', 2)->create();

        $this->publishThread(['channel_id' => null])
             ->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 999])
             ->assertSessionHasErrors('channel_id');
    }

    /** @test */
    public function unauthorized_users_cannot_delete_threads()
    {
        $this->withExceptionHandling();

        $thread = create('App\Thread');

        $this->delete($thread->path())
             ->assertRedirect('/login');

        $this->signIn();
        $this->delete($thread->path())
             ->assertStatus(403);
    }

    /** @test */
    public function authorized_users_can_delete_threads ()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $reply = create('App\Reply', ['thread_id' => $thread->id]);

        $response = $this->json('DELETE', $thread->path());

        $response->assertStatus(204);

        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
    }

    public function publishThread($overwrites = []) 
    {
        $this->withExceptionHandling()->signIn();

        $thread = make('App\Thread', $overwrites);

        return $this->post('/threads', $thread->toArray());

    }

}
