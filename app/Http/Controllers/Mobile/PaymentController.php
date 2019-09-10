<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\V1\PaymentTrait;
use App\Traits\V1\FcmTokenTrait;
use Illuminate\Support\Facades\Input;
use \GuzzleHttp\Client;

class PaymentController extends Controller
{
    use PaymentTrait, FcmTokenTrait;

    protected $request;
    protected $user;
    
    public function __construct(Request $request, Client $client) {
        $this->request = $request;
        
    }

    // function render() {
    //     $renderer_source = File::get(base_path('node_modules/vue-server-renderer/basic.js'));
    //     $app_source = File::get(public_path('js/entry-server.js'));

    //     $v8 = new \V8Js();

    //     ob_start();

    //     $js = 'var process = { env: { VUE_ENV: "server", NODE_ENV: "production" } }; this.global = { process: process };';

    //     $v8->executeString($js);
    //     $v8->executeString($renderer_source);
    //     $v8->executeString($app_source);

    //     return ob_get_clean();
    // } 

    function index()
    {
        // $ssr = $this->render();

        $auth = $this->request->headers->all();
        $body = $this->request->all();
        $data = json_encode([
            'invId' => $body,
            'token' => $auth["authorization"][0]
        ]);
        
        return view('payment.payment', ['data' => $data]);
    }

    function show() {
        $auth = $this->request->headers->all();
        $body = Input::get('data');

        $data = (object)[
            'invId' => json_decode($body),
            'token' => $auth["authorization"][0]
        ];

        $data = json_encode($data);

        return view('payment.payment', ['data' => $data]);
    }

    function finish() {
        return response('finish');
    }
}
