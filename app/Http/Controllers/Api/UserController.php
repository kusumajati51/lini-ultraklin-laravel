<?php

namespace App\Http\Controllers\Api;

use App\Traits\V1\FcmTokenTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    use FcmTokenTrait;

    protected $request;
    protected $user;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function profile()
    {
        $user = $this->request->user();

        $user = [
            'name' => $user->name,
            'phone' => $user->phone,
            'email' => $user->email,
            'status' => $user->status,
            'info' => $user->info,
        ];

        return response()->json($user);
    }
}
