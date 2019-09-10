<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/password/reset/success';

    protected $request;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;

        // $this->middleware('guest');
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.__reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function resetSuccess() {
        if (auth()->guest()) {
            return redirect('/');
        }

        return view('auth.passwords.__reset_done');
    }

    protected function sendResetResponse($response)
    {
        \App\PasswordResetHistory::create([
            'user_id' => auth()->user()->id,
            'data' => [
                'ip' => $this->request->ip(),
                'user_agent' => $this->request->header('User-Agent')
            ]
        ]);

        return redirect($this->redirectPath())
            ->with('status', trans($response));
    }
}
