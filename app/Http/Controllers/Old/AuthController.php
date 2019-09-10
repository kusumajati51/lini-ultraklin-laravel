<?php

namespace App\Http\Controllers\Old;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use Carbon\Carbon;
use Validator;

use App\User;

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
            'name' => 'required',
            'phone' => 'required|min:10',
            'email' => 'required|unique:users',
            'password' => 'required|min:6|confirmed'
        ];

        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'response' => $validator->errors()->first()
            ]);
        }

        $user = new User;
        $user->name = $this->request->name;
        $user->phone = $this->request->phone;
        $user->email = strtolower($this->request->email);
        $user->password = bcrypt($this->request->password);
        $user->token = str_random(60);
        $user->created_by = $user->name. ' :: '.$this->request->header('User-Agent');
        $user->updated_by = $user->name. ' :: '.$this->request->header('User-Agent');
        $user->save();

        return response()->json([
            'success' => 'Thank you for register.',
            'uk_token' => $user->token
        ]);
    }

    public function login()
    {
        $user = User::where('email', $this->request->email)->first();

        if (is_null($user)) {
            return response()->json([
                'response' => 'Login invalid, incorect Email or Password.'
            ]);
        }

        $newAuthStart = Carbon::createFromFormat('Y-m-d', config('ultraklin.time.new_auth_start'));

        if ($newAuthStart->diffInDays($user->created_at, false) < 0 && $user->passwordResetHistories->count() < 1) {
            return $this->loginWithOldAuth();
        }

        return $this->loginWithNewAuth();
    }

    public function loginWithOldAuth()
    {
        $user = User::where('email', $this->request->email)
            ->where('password', md5($this->request->password))
            ->first();
        
        if (is_null($user)) {
            return response()->json([
                'response' => 'Login invalid, incorect Email or Password.'
            ]);
        }

		$user->token = str_random(60);
		$user->save();

        return response()->json([
            'success' => 'Login successful, now you can access aplication.',
            'uk_token' => $user->token,
            'name' => $user->name
        ]);
    }

    public function loginWithNewAuth()
    {
        $credentials = $this->request->only('email', 'password');

        if (!Auth::once($credentials)) {
            return response()->json([
                'response' => 'Login invalid, incorect Email or Password.'
            ]);
        }

        $user = auth()->user();
        $user->token = str_random(60);
		$user->save();

        return response()->json([
            'success' => 'Login successful, now you can access aplication.',
            'uk_token' => $user->token,
            'name' => $user->name
        ]);
    }
}
