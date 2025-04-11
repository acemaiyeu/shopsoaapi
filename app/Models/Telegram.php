<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Telegram
{
    public static function sendMessage($text)
    {
        $chat_id = env('BOT_TELEGRAM_ID');
        $bot_token = env('BOT_TELEGRAM_TOKEN');
        $url = "https://api.telegram.org/bot$bot_token/sendMessage";

        $post_fields = [
            'chat_id' => $chat_id,
            'text' => $text,
        ];

        $ch = curl_init(); // <-- phải thêm dấu \ ở đây
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type:multipart/form-data"
        ]);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }
}