<?php

namespace App\Traits\V1;

use \GuzzleHttp\Client;

trait FaspayTrait {
    protected $client, $env, $signature, $url;

    public function __construct()
    {
        $this->env = (env('APP_ENV') == 'live') ? 'live' : 'dev';
    }

    protected function signature()
    {
        if (is_null($this->signature)) {
            $id = 'bot'.config("faspay.{$this->env}.id");
            $password = config("faspay.{$this->env}.password");

            $this->signature = sha1(md5($id.$password));
        }

        return $this->signature;
    }

    protected function client()
    {
        if (is_null($this->url)) {
            $this->url = config("faspay.{$this->env}.url");
        }

        if (is_null($this->client)) {
            $this->client = new Client([
                'base_uri' => $this->url
            ]);
        }

        return $this->client;
    }

    public function getPaymentChannels()
    {
        $config = config("faspay.{$this->env}");

        $body = [
            'request' => 'Daftar Payment Channel',
            'merchant' => $config['name'],
            'merchant_id' => $config['id'],
            'signature' => $this->signature()
        ];

        $res = $this->client()->request('POST', '100001/10', [
            'json' => $body
        ]);

        $data = json_decode($res->getBody());

        if (isset($data->response_error)) {
            return response()->json([
                'error' => 1,
                'message' => env('APP_ENV') != 'live' ? $data->response_error->response_desc : 'Bad Request.'
            ], 400);
        }

        return response()->json($data->payment_channel);
    }
}
