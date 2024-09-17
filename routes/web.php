<?php

use App\Http\Controllers\TelegramController;
use Illuminate\Support\Facades\Route;

use Telegram\Bot\Laravel\Facades\Telegram;

Route::get('/', function () {
    return view('welcome');
});


Route::get('set-webhook',function (){
    $response = Telegram::setWebhook(['url' => 'https://fed2-95-214-210-78.ngrok-free.app/api/telegram/webhook']);
});


