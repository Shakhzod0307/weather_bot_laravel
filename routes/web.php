<?php

use App\Http\Controllers\TelegramController;
use Illuminate\Support\Facades\Route;

use Telegram\Bot\Laravel\Facades\Telegram;

Route::get('/', function () {
    return view('welcome');
});


Route::get('set-webhook',function (){
    $response = Telegram::setWebhook(['url' => 'https://568d-188-113-231-102.ngrok-free.app/api/telegram/webhook']);
});


