<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\Mail as MyMail; // 👈 Đổi alias để tránh trùng tên
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendMail()
    {
        $details = [
            'title' => 'Thử gửi mail trong Laravel',
            'body' => 'Đây là nội dung thư test gửi từ hệ thống.'
        ];

        // Gửi mail
        Mail::to('nguoinhan@gmail.com')->send(new MyMail($details));

        return "✅ Đã gửi mail thành công!";
    }
}
