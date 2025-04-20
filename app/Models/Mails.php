<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use App\Mail\SampleMail;

class Mails
{
    public static function sendMail($to, $content, $title)
    {
        $data = [
            'message' => $content,
            'title' =>  $title,
            'subject' => $title . " Từ Theme For Student"
        ];
    
        Mail::to($to)->send(new SampleMail($data));
    
        return 'Đã gửi mail!';
    }
}