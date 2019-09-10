<?php

namespace App\Traits\V1;

use \GuzzleHttp\Client;
use App\UserToken;
use App\User;
use App\Invoice;
use App\Officer;
use App\Region;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;

trait FcmTokenTrait {

    function user() {
        return $this->request->user();
    }

    public function storeFcmToken() {

        if ($this->request->type == 'CMS') {
            $_user = auth('officer')->user();
        } else {
            $_user = auth()->user();
        }

        $_token = UserToken::updateOrCreate(
            ['token' => $this->request->token],
            [
                'user_id' => $_user->id,
//                'user_id' => $this->request->user_id,
                'token' => $this->request->token,
                'type' => $this->request->type,
                'active' => 1
            ]);

        return response()->json([
            'success' => 1,
            'message' => 'Token saved.'
        ], 200);
    }

    public function sendNotification($param, $id, $data) {
        $client = new Client();
        $_user = User::with('userTokens')->find($id);

        if (is_null($_user)) {
            return response()->json([
                'message' => 'No user'
            ]);
        }

        $param['sound'] = 'enabled';
        foreach ($_user->userTokens as $item) {
            if ($item->type != 'CMS') {
                $body = [
                    'to' => $item->token,
                    "notification" => $param,
                    "data" => $data            
                ];
                $res = $client->request('POST', 'https://fcm.googleapis.com/fcm/send', 
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'key=AIzaSyDY1soU4JLC_sl1Trdkvpau5mm5Wu0dl6c'
                    ],
                    'json' => $body
                ]);
            }
        }
    }

    public function sendNotificationOfficer($param, $region_id) {
        $client = new Client();
        $region = Region::with('officers')->where('id', $region_id)->get();
        $officers = $region[0]->officers()->with('officerTokens')->get();
        foreach ($officers as $officer) {
            foreach ($officer->officerTokens as $item) {
                if ($item->type == 'CMS') {
                    $body = [
                        'to' => $item->token,
                        "notification" => $param
                    ];
                    $res = $client->request('POST', 'https://fcm.googleapis.com/fcm/send', 
                    [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Authorization' => 'key=AIzaSyDY1soU4JLC_sl1Trdkvpau5mm5Wu0dl6c'
                        ],
                        'json' => $body
                    ]);
                }
            }
        }
    }

    public function getNotification() {
        $body = $this->request->all();
        $invoice = Invoice::with('payments', 'orders')->where('code', $body["bill_no"])->first();
        $date = Carbon::now()->format('Y-m-d H:i:s');

        if (is_null($invoice)) {
            return response()->json([
                'status' => 0,
                'message' => 'Invoice not found'
            ]);
        }

        $region_id = $invoice->orders[0]->region_id;
        $profile = User::find($invoice->user_id);

        $msg = [
            'body' => 'Order anda sedang di proses',
            'title' => 'Pembayaran berhasil'
        ];

        $msgOfficer = [
            'body' => 'Order untuk '.$invoice->code.' atas nama '.$profile->name.' untuk segera di proses',
            'title' => 'Pembayaran telah dilakukan oleh '.$profile->name
        ];

        $data = null;

        $this->sendNotification($msg, $invoice->user_id, $data);
        $this->sendNotificationOfficer($msgOfficer, $region_id);
        $invoice->status = 'Paid';
        $invoice->save();
        
        return response()->json([
            "response" => "Payment Notification",
            "trx_id" => $body["trx_id"],
            "merchant_id" => "32194",
            "merchant" => "Ultraklin",
            "bill_no" => $body["bill_no"],
            "response_code" => "00",
            "response_desc" => "Sukses",
            "response_date" => $date
        ]);
    }
}