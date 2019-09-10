<?php

namespace App\Utils;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

use App\UserToken;

use App\Services\FirebaseService;

class FirebaseUtil {
    protected $firebase;

    public function __construct()
    {
        $this->firebase = (new FirebaseService)->firebase();
    }

    public function storeToken($type, $id, $token)
    {
        $token = UserToken::updateOrCreate(
            [
            'type' => strtolower($type),
            'user_id' => $id,
            'token' => $token
            ],
            [
                'active' => 1
            ]
        );

        return $token;
    }

    public function sendMessageToken($token, $title, $body, $data = null)
    {
        $messaging = $this->firebase->getMessaging();

        $messageArray = [
            'token' => $token,
            'notification' => [
                'title' => $title,
                'body' => $body
            ]
        ];

        if (!is_null($data)) {
            $messageArray['data'] = $data;
        }

        $messaging->send(CloudMessage::fromArray($messageArray));
    }

    public function sendNewOrderNotification($tokens, $order)
    {
        foreach ($tokens as $token) {
            $this->sendMessageToken(
                $token,
                'New Order',
                $order->code.' - '.$order->package->display_name,
                [
                    'target' => '@store:order-detail',
                    'order_id' => (String) $order->id
                ]
            );
        }
    }
}
