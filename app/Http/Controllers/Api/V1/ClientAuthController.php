<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;

class ClientAuthController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getToken()
    {
        $this->request->request->add([
            'grant_type' => 'client_credentials',
            'client_id' => $this->request->clientId,
            'client_secret' => $this->request->clientSecret,
            'scope' => '*'
        ]);

        $client = DB::table('oauth_clients')
            ->where('id', $this->request->clientId)
            ->where('secret', $this->request->clientSecret)
            ->first();

        if (!is_null($client)) {
            DB::table('oauth_access_tokens')
                ->where('client_id', $client->id)
                ->update(['revoked' => 1]);
        }

        $proxy = Request::create('oauth/token', 'POST');

        return \Route::dispatch($proxy);
    }
}
