<?php

namespace Tests\Browser\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RegisterTest extends DuskTestCase
{
    use RefreshDatabase;

    protected $testUserInfo = [
        'first_name' => 'Ivan',
        'last_name' => 'Li',
        'email' => 'ivan.li@hotmail.com',
        'password' => 'helloworld',
        'password_confirmation' => 'helloworld'
    ];

    /**
     * Test elements on register page
     *
     * @return void
     */
    public function testPageRender()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('auth.register.get'))
                ->assertSee('Sign Up')
                ->assertSee('Already have an account');
        });
    }

    /**
     * Test register form validation
     *
     * @return void
     */
    public function testRegisterFormValidation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('auth.register.get'))
                ->click('form button')
                ->waitForText('email field is required', 5)
                ->assertSee('first name field is required')
                ->assertSee('last name field is required')
                ->assertSee('email field is required')
                ->assertSee('password field is required');
        });
    }

    /**
     * Test submitting registration form
     *
     * TODO this test doesn't work\
     * 
     * @return void
     */
    public function testRegisterWithDuplicateEmail()
    {
        $this->browse(function (Browser $browser) {
            $user = factory(User::class)->create();
            $browser->visit(route('auth.register.get'))
                ->type('first_name', array_get($this->testUserInfo, 'first_name'))
                ->type('last_name', array_get($this->testUserInfo, 'last_name'))
                ->type('email', $user->email)
                ->type('password', array_get($this->testUserInfo, 'password'))
//                ->type('password_confirmation', array_get($this->testUserInfo, 'password_confirmation'))
                ->click('form button')
                ->waitFor('.help.text-danger')
                ->assertSee('The email has already been taken');
        });
    }

    /**
     * Test submitting registration form
     *
     * @return void
     */
    public function testRegisterFormSubmission()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()->visit(route('auth.register.get'))
                ->type('first_name', array_get($this->testUserInfo, 'first_name'))
                ->type('last_name', array_get($this->testUserInfo, 'last_name'))
                ->type('email', array_get($this->testUserInfo, 'email'))
                ->type('password', array_get($this->testUserInfo, 'password'))
                ->type('password_confirmation', array_get($this->testUserInfo, 'password_confirmation'))
                ->pressAndWaitFor('Sign Up')
                ->assertRouteIs('home')
                ->logout();
        });
    }

    /**
     * Test authenticated user visiting register page
     */
    public function testAuthenticatedRegisterPageRedirect()
    {
        $this->browse(function (Browser $browser) {
            $user = factory(User::class)->create();
            $browser->loginAs($user)
                ->visit(route('auth.register.get'))
                ->pause(1000)
                ->assertRouteIs('home')
                ->logout();
        });
    }
}
