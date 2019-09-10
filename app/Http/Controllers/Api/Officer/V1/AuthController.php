<?php

namespace App\Http\Controllers\Api\Officer\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;

class AuthController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
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

        $client = \Laravel\Passport\Client::find(4);

        $this->request->request->add([
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => $this->request->email,
            'password' => $this->request->password,
            'scope' => '*'
        ]);

        $proxy = Request::create('/officer/oauth/token', 'POST');

        return \Route::dispatch($proxy);
    }
}
