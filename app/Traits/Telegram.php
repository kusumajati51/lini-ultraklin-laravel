<?php

namespace App\Traits;

use Telegram\Bot\Api;

trait Telegram {
    protected $telegram;

    protected function sendMessage($text = "") {
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));

        $this->telegram->sendMessage([
            'chat_id' => env('TELEGRAM_CHAT_ID'),
            'text' => $text,
            'parse_mode' => 'Markdown'
        ]);
    }
}