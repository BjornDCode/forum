<?php

namespace Tests\Unit;

use App\Spam;
use Tests\TestCase;

class SpamTest extends TestCase
{
    
    /** @test */
    public function id_validates_spam()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect('Innocent reply'));
    }

}
