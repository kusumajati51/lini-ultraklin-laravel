<?php

namespace App\Traits\V1;

use Telegram\Bot\Api;

trait TelegramTrait {
    protected $telegram;

    protected function sendMessage($text = "") {
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));

        if (@fsockopen('api.telegram.org', 80, $errno, $errstr, 2)) {
            $this->telegram->sendMessage([
                'chat_id' => env('TELEGRAM_CHAT_ID'),
                'text' => $text,
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => true
            ]);
        }
    }
}