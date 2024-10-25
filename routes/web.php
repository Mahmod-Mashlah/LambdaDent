<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/verify', function () {
    return view('mails.VerifyMail');
});
Route::get('/welcome', function () {
    return view('mails.WelcomeMail', ["welcome_message" => 123, "client_name" => "client"]);
});
