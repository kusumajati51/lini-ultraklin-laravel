<?php

namespace App\Http\Controllers\Api\V2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Utils\FirebaseUtil;

class FirebaseController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function storeToken()
    {
        (new FirebaseUtil)->storeToken(
            $this->request->type,
            auth('api')->user()->id,
            $this->request->token
        );

        return response()->json([
            'success' => 1,
            'message' => 'Token saved.'
        ]);
    }
}
