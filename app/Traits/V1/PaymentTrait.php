<?php
namespace App\Traits\V1;

use \GuzzleHttp\Client;
use \App\Invoice;
use \App\Payment;
// use App\Traits\V1\FcmTokenTrait;

trait PaymentTrait {

    protected $_url;
    
    public function env() {
        return $env = \App::environment();
    }

    public function sign() {

        if ($this->env() == 'local') {
            $sign = json_decode('{"id": "bot32194","pass": "p@ssw0rd"}');
        } else {
            $sign = json_decode('{"id": "bot32194","pass": "5y5LZMqv"}'); 
        }
        return $sign;
    }

    protected function client() {
        if ($this->env() == 'local') {
            $_url = 'https://dev.faspay.co.id/cvr/';
        } else {
            $_url = 'https://web.faspay.co.id/cvr/';
        }

        return new Client([
            'base_uri' => $_url
        ]);
    }

    function getPaymentChannel() {
        // $client = new Client();
        $body = $this->request->all();
        $res = $this->client()->request('POST', '100001/10', 
            ['json' => $body]);

        $resBody = json_decode($res->getBody());

        return response()->json($resBody);
    }
    
    function sendPayment($id) {
        // $client = new Client();
        $body = (object)$this->request->all();
        $res = $this->client()->request('POST', $body->add_detail['url'], 
        [
            $body->add_detail['content_type'] => $body->data,
            'http_errors' => false
        ]);
        $user_id = $this->user()->id;

        // $signature = SHA1('##'.$body->add_detail->.)

        $msg = [
            'body' => 'Silahkan selesaikan pembayaran anda',
            'title' => 'Order berhasil'
        ];

        $data = null;

        if ($res->getStatusCode() == 500) {
            return $res->getBody()->getContents();
        }

        if ($body->add_detail['method'] == 'credit') {
            return $res->getBody()->getContents();
        }

        $resBody = json_decode($res->getBody());
        if ($resBody->response_code == 00) {
            $invoice = Invoice::find($id);
            
            $invoice->payments->trx_no = $resBody->trx_id;
            $invoice->payments->bill_no = $resBody->bill_no;
            $invoice->payments->status_payment = 1;
            $invoice->payments->bank = $body->add_detail['bank']; 
            
            $invoice->payments->save();

            $this->sendNotification($msg, $user_id, $data);
        }
        $resBody = json_decode($res->getBody());

        return response()->json($resBody);
    }
    
    public function getPaymentStatus($payment) {
        // $client = new Client();
        $sign = $this->sign();

        $body = [
            'request' => 'Pengecekan Status Pembayaran',
            'trx_id' => $payment->trx_no,
            'merchant_id' => '32194',
            'bill_no' => $payment->bill_no,
            'signature' => sha1(md5($sign->id.$sign->pass.$payment->bill_no))
        ];  
        $res = $this->client()->request('POST', '100004/10', 
            ['json' => $body]);
            
        $resBody = json_decode($res->getBody()->getContents());
            
        return response()->json($resBody);
    }

    public function cancelPayment() {
        // $client = new Client();
        $body = $this->request->all();  
        $res = $this->client()->request('POST', '100005/10', 
            ['json' => $body]);

        $resBody = json_decode($res->getBody());
            
        return response()->json($resBody);
    }

    public function getPaymentDetail($code) {
        // $client = new Client();
        $invoice = Invoice::with('payments')->where('code', $code)->first();
        $sign = $this->sign();
        $hashSign = sha1(md5($sign->id.$sign->pass.$invoice->payments->bill_no));
        $res = $this->client()->request('GET', 'https://dev.faspay.co.id/pws/100003/0830000010100000/'.$hashSign.'?trx_id='.$invoice->payments->trx_no.'&merchant_id=32194&bill_no='.$invoice->payments->bill_no, 
            [
                'debug' => true
            ]
        );

        return $res->getBody();
    }
}
