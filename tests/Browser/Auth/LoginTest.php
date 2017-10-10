<?php

namespace Tests\Browser\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoginTest extends DuskTestCase
{
    use RefreshDatabase;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testPageRender()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/auth/login')
                ->assertSee('Facebook')
                ->assertSee('Twitter')
                ->assertSee('Google')
                ->assertSee('Login');
        });
    }

    public function testLoginFormValidation()
    {
        $this->browse(function (Browser $browser) {
            $browser->click('form button')
                ->waitForText('email field is required', 5)
                ->assertSee('email field is required')
                ->assertSee('password field is required');
        });
    }

    public function testLoginFormSubmission()
    {
        $this->browse(function (Browser $browser) {
            $user = factory(User::class)->create();
            $browser->type('email', $user->email)
                ->type('password', 'secret')
                ->pressAndWaitFor('Login')
                ->assertPathIs('/');
        });
    }
}
