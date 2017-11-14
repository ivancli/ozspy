<?php

namespace Tests\Unit\Repositories;

use OzSpy\Contracts\Models\Auth\UserContract;
use OzSpy\Models\Auth\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected $userRepo;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    public function testAll()
    {
        $this->userRepo = $this->app->make(UserContract::class);

        $createdUsers = factory(User::class, 3)->make();

        $fetchedUsers = $this->userRepo->all();

        $diffUsers = $fetchedUsers->diff($createdUsers);

        $diffCount = $diffUsers->count();

        $this->assertTrue($diffCount > 0);
    }

    public function testGet()
    {

    }

    public function testStore()
    {

    }

    public function testUpdate()
    {

    }

    public function testDelete()
    {

    }
}
