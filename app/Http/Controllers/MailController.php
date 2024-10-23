<?php

namespace App\Http\Controllers;

use App\Mail\SendWelcomeMail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendMail()
    {
        try {

            $to = "mashlahmahmod2000@gmail.com";
            $welcome_message = " Hi there !! . Welcom to LambdaDent Application ";
            $response = Mail::to($to)->send(new SendWelcomeMail($welcome_message));
            dd(vars: 1);
        } catch (Exception $e) {
            \Log::error("Unable to send email ," . $e->getMessage());
        }
    }
}
