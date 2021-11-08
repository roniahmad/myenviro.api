<?php

namespace App\Http\Controllers\Foo\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class TestMailController extends Controller
{
    public function mail()
    {
        $data = array('recepient'=>"Roni Ahmad");
        Mail::send('foo.mail', $data, function($message) {
            $message->to('admin@pestcare.co.id', 'Admin Pestcare')->subject('Test Mail');
            $message->from('sales@pestcare.co.id','Sales Pestcare');
        });

        return response()->json([
            'success' => 1,
            'message' => "Email Sent. Check your inbox.",
            ],
            Response::HTTP_OK);
    }
}
