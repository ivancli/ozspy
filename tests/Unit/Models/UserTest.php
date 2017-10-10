<?php

namespace Tests\Feature\Unit\Models;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUserObserver()
    {
        $testUser = User::create([
            'first_name' => 'Ivan',
            'last_name' => 'Ivan',
            'email' => 'ivan.li@hotmail.com',
        ]);

        $this->assertTrue($testUser->roles->count() > 0);
    }
}
