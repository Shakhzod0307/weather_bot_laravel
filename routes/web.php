<?php

use App\Http\Controllers\TelegramController;
use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;

Route::get('/', function () {
    return view('welcome');
});


Route::get('set-webhook',function (){
    $response = Telegram::setWebhook(['url' => 'https://93e7-188-113-200-73.ngrok-free.app/api/telegram/webhook']);
});
