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
        $this->get('/threads/create')
             ->assertRedirect('/login');
    }

    /** @test */
    public function an_unauthenticated_user_cannot_create_new_threads()
    {
        $this->expectException('Illuminate\Auth\AuthenticationException');

        $this->post('/threads', []);
    }

    /** @test */
    public function an_authenticated_user_can_create_new_threads()
    {
        $this->signIn();

        $thread = make('App\Thread');
        $this->post('/threads', $thread->toArray());

        $this->get($thread->path())
             ->assertSee($thread->title);
    }

}
