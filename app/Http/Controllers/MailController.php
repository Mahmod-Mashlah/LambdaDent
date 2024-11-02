<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Mail\VerifyMail;
use Illuminate\Http\Request;
use App\Mail\SendWelcomeMail;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\CheckVereficationCodeRequest;
use App\Http\Requests\RegisterVerificationCodeRequest;

class MailController extends Controller
{
    use HttpResponses;
    public function sendMail()
    {
        try {

            $to = "mashlahmahmod2000@gmail.com";
            $welcome_message = " Hi there !! . Welcom to LambdaDent Application ";
            $response = Mail::to($to)->send(new SendWelcomeMail($welcome_message));
            dd(1);
        } catch (Exception $e) {
            Log::error("Unable to send email ," . $e->getMessage());
        }
    }
    public function send_verification_code($email)
    {
        try {
            $user = User::where("email", $email)->first();
            $verification_code = mt_rand(10000, 99999);
            $user->update(["verification_code" => $verification_code]);
            $user->save();
            $response = Mail::to($email)->send(new VerifyMail($email, $verification_code));
            return $this->success([], "Verification code has sent successfully");
        } catch (Exception $e) {
            Log::error("Unable to send email ," . $e->getMessage());
        }
    }
    public function verify_email_code(RegisterVerificationCodeRequest $request)
    {
        try {
            $user = User::where("email", $request->email)->first();

            if ($request->verification_code == $user->verification_code) {
                $user->update([
                    'email_is_verified' => 1,
                    'email_verified_at' => now()
                ]);
                $user->save();
                return $this->success(["client" => $user], "Your email has been verified successfully, waiting for admin approval");
            }
            return $this->error("Verification code doesn't match. Plaease try again or send verification code again.", "Error", 422);
        } catch (Exception $e) {
            Log::error("Unable to send email ," . $e->getMessage());
        }
    }
    public function check_verification_code(CheckVereficationCodeRequest $request)
    {
        try {
            $user = User::where("email", $request->email)->first();
            if ($request->last_verification_code == $user->verification_code) {

                return $this->success([], "Verification Code is valid");
            }
            return $this->error("Verification code doesn't match. Plaease try again or send verification code again.", "Error", 422);
        } catch (Exception $e) {
            Log::error("Unable to send email ," . $e->getMessage());
        }
    }
    public function forget_password(ForgetPasswordRequest $request)
    {
        try {
            $user = User::where("email", $request->email)->first();

            $user->update([
                'password' => Hash::make($request->new_password),
            ]);
            $user->save();
            return $this->success(["client" => $user], "New password has been saved successfully");
        } catch (Exception $e) {
            Log::error("Unable to send email ," . $e->getMessage());
        }
    }
}
