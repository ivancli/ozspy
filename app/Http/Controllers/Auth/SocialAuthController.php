<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 1/10/2017
 * Time: 9:54 PM
 */

namespace App\Http\Controllers\Auth;


use App\Contracts\Models\UserContract;
use App\Exceptions\SocialAuthExceptions\NullEmailException;
use App\Exceptions\SocialAuthExceptions\UnauthorisedException;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    use AuthenticatesUsers;

    protected $userRepo;
    protected $request;

    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @param UserContract $userContract
     * @param Request $request
     */
    public function __construct(UserContract $userContract, Request $request)
    {
        $this->middleware('guest')->except('logout');

        $this->userRepo = $userContract;
        $this->request = $request;
    }

    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        if ($this->request->has('error') || $this->request->has('denied')) {
            throw new UnauthorisedException;
        }

        $socialUser = Socialite::driver($provider)->user();
        switch ($provider) {
            case 'facebook':
            case 'google':
                $first_name = first_word($socialUser->name);
                $last_name = last_word($socialUser->name);
                break;
            case 'twitter':
                $first_name = first_word($socialUser->name, '.');
                $last_name = last_word($socialUser->name, '.');
                break;
        }
        $email = $socialUser->email;
        $provider_id = $socialUser->id;

        if (!is_null($email)) {
            $matchedUsers = $this->userRepo->findBy('email', $email);
            $user = $matchedUsers->first();
            if (is_null($user)) {
                $user = $this->userRepo->create(compact([
                    'email', 'first_name', 'last_name', 'provider', 'provider_id'
                ]));
            }

            auth()->login($user);

            return $this->sendLoginResponse($this->request);
        } else {
            throw new NullEmailException;
        }
    }
}