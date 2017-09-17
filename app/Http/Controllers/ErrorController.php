<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ErrorController extends Controller
{
    public function javascript()
    {
        return view('errors.javascript_disabled');
    }

    public function cookie()
    {
        return view('errors.cookie_disabled');
    }
}
