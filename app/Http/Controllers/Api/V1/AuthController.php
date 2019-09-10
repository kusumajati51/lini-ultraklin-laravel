<?php

namespace App\Http\Controllers\Api\V1;

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
            'phone' => 'required|unique:users|min:10',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required:min:6'
        ];

        if ($this->request->has('ref') || $this->request->has('ref_code') || $this->request->has('reff_code')) {
            if ($this->request->has('ref')) {
                $rules['ref'] = 'exists:users,code';
            } elseif ($this->request->has('ref_code')) {
                $rules['ref_code'] = 'exists:users,code';
            } elseif ($this->request->has('reff_code')) {
                $rules['reff_code'] = 'exists:users,code';
            }
        }

        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error_validation' => 1,
                'message' => 'Invalid data.',
                'data' => $validator->errors()
            ], 422);
        }

        if ($this->request->has('check') && $this->request->check) {
            return response()->json([
                'error' => 1,
                'message' => 'Email and phone is available.'
            ], 422);
        }

        $user = new User;
        $user->name = $this->request->name;
        $user->phone = $this->request->phone;
        $user->email = strtolower($this->request->email);
        $user->password = bcrypt($this->request->password);
        $user->created_by = $user->name. ' :: '.$this->request->header('User-Agent');
        $user->updated_by = $user->name. ' :: '.$this->request->header('User-Agent');

        if ($this->request->has('ref') || $this->request->has('ref_code') || $this->request->has('reff_code')) {
            $ref = '';

            if ($this->request->has('ref')) {
                $ref = $this->request->ref;
            } elseif ($this->request->has('ref_code')) {
                $ref = $this->request->ref_code;
            } elseif ($this->request->has('reff_code')) {
                $ref = $this->request->reff_code;
            }

            $user->referral = strtoupper($ref);
        }

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
                'error_validation' => 1,
                'message' => 'Invalid data.',
                'data' => $validator->errors()
            ], 422);
        }

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
                'error_validation' => 1,
                'message' => 'Invalid data.',
                'data' => $validator->errors()
            ], 422);
        }

        $response = Password::broker()->sendResetLink(
            $this->request->only('email')
        );

        if ($response != Password::RESET_LINK_SENT) {
            return response()->json([
                'error' => 1,
                'message' => trans($response)
            ], 400);
        }

        return response()->json([
            'success' => 1,
            'message' => trans($response)
        ]);
    }
}
