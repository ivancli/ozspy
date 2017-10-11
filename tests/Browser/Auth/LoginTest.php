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
     * Test elements on the page
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

    /**
     * Test login validation
     *
     * @return void
     */
    public function testLoginFormValidation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/auth/login')
                ->click('form button')
                ->waitForText('email field is required', 5)
                ->assertSee('email field is required')
                ->assertSee('password field is required');
        });
    }

    /**
     * Test login submission
     *
     * @return void
     */
    public function testLoginFormSubmission()
    {
        $this->browse(function (Browser $browser) {
            $user = factory(User::class)->create();
            $browser->visit('/auth/login')
                ->type('email', $user->email)
                ->type('password', 'secret')
                ->press('Login')
                ->assertRouteIs('home');
        });
    }

    /**
     * Test authenticated user visiting login page
     */
    public function testAuthenticatedLoginPageRedirect()
    {
        $this->browse(function (Browser $browser) {
            $user = factory(User::class)->create();
            $browser->loginAs($user)
                ->visit(route('auth.login.get'))
                ->pause(1000)
                ->assertRouteIs('home')
                ->logout();
        });
    }

    public function testUnauthenticatedHomePageRedirect()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('home'))
                ->pause(1000)
                ->assertRouteIs('auth.login.get');
        });
    }
}
