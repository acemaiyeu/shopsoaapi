<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use App\Mail\SampleMail;

class Mails
{
    public static function sendMail()
    {
        $data = [
            'message' => 'Đây là nội dung email từ Laravel'
        ];
    
        Mail::to('nthanhhuy11a2@gmail.com')->send(new SampleMail($data));
    
        return 'Đã gửi mail!';
    }
}