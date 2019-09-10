<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Password;
use Validator;

use App\User;
use App\PasswordResetHistory;

class AuthController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function register()
    {
        $rules = [
            'name' => 'required|min:3',
            'phone' => 'required|min:10|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required:min:6'
        ];

        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ]);
        }

        $user = new User;
        $user->name = $this->request->name;
        $user->phone = $this->request->phone;
        $user->email = strtolower($this->request->email);
        $user->password = bcrypt($this->request->password);
        $user->created_by = $user->name. ' :: '.$this->request->header('User-Agent');
        $user->updated_by = $user->name. ' :: '.$this->request->header('User-Agent');
        $user->save();

        $client = \Laravel\Passport\Client::find(2);

        $this->request->request->add([
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => $this->request->email,
            'password' => $this->request->password,
            'scope' => '*'
        ]);

        $proxy = Request::create('oauth/token', 'POST');

        return \Route::dispatch($proxy);
    }

    public function login() {
        $rules = [
            'email' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ]);
        }
        
        $user = User::where('email', $this->request->email)->first();

        if (is_null($user)) {
            return response()->json([
                'error' => 'invalid_credentials',
                'message' => 'The user credentials were incorrect.'
            ]);
        }

        // Start # Auto change password encryption
        $newAuthStart = Carbon::createFromFormat('Y-m-d', config('ultraklin.time.new_auth_start'));

        if ($newAuthStart->diffInDays($user->created_at, false) < 0 && $user->passwordResetHistories->count() < 1) {
            $user = User::where('email', $this->request->email)
                ->where('password', md5($this->request->password))
                ->first();

            if (is_null($user)) {
                return response()->json([
                    'error' => 'invalid_credentials',
                    'message' => 'The user credentials were incorrect.'
                ]);
            }

            $user->password = bcrypt($this->request->password);
            $user->save();

            $user->passwordResetHistories()->save(
                new PasswordResetHistory([
                    'data' => [
                        'ip' => $this->request->ip(),
                        'user_agent' => $this->request->header('User-Agent')
                    ]
                ])
            );
        }
        // End # Auto change password encryption

        $client = \Laravel\Passport\Client::find(2);

        $this->request->request->add([
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => $this->request->email,
            'password' => $this->request->password,
            'scope' => '*'
        ]);

        $proxy = Request::create('oauth/token', 'POST');

        return \Route::dispatch($proxy);
    }

    public function sendResetLinkEmail()
    {
        $rules = [
            'email' => 'required|exists:users,email',
        ];

        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ]);
        }

        $response = Password::broker()->sendResetLink(
            $this->request->only('email')
        );

        if ($response != Password::RESET_LINK_SENT) {
            return response()->json([
                'error' => trans($response)
            ]);
        }

        return response()->json([
            'success' => trans($response)
        ]);
    }
}
