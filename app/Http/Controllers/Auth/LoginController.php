<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->role === 'user') {
            Auth::logout();
            return redirect('/login')->withErrors(['error' => 'You are not allowed to login.']);
        }

        if ($user->role === 'kasir') {
            return redirect('/transaction')->with('login_success', 'Welcome, ' . $user->name . '!');
        }

        if ($user->role === 'admin') {
            return redirect('/home')->with('login_success', 'Welcome, ' . $user->name . '!');
        }

        session()->flash('login_success', 'Welcome, ' . $user->name . '!');

        return redirect()->intended($this->redirectTo);
    }



    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('logout_success', 'You have been logged out successfully.');
    }

}
