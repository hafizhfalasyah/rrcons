<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * The path users are redirected to after login.
     */
    protected string $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Validate the user login request.
     */
    protected function validateLogin(Request $request): void
    {
        $username = $this->username();

        $request->validate(
            [
                $username => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                ],

                'password' => [
                    'required',
                    'string',
                ],
            ],
            [
                "{$username}.required" =>
                    __('auth.validation.email.required'),

                "{$username}.email" =>
                    __('auth.validation.email.email'),

                "{$username}.max" =>
                    __('auth.validation.email.max', [
                        'max' => 255,
                    ]),

                'password.required' =>
                    __('auth.validation.password.required'),

                'password.string' =>
                    __('auth.validation.password.string'),
            ],
        );
    }
}