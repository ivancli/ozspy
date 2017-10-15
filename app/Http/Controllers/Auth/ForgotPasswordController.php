<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    protected $request;

    /**
     * Create a new controller instance.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->middleware('guest');

        $this->request = $request;
    }

    protected function sendResetLinkResponse($response)
    {
        if (!$this->request->ajax()) {
            return back()->with('status', trans($response));
        }
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        $errors = ['email' => trans($response)];
        if ($request->ajax()) {
            return compact(['errors']);
        } else {
            return back()->withErrors($errors);
        }
    }
}
