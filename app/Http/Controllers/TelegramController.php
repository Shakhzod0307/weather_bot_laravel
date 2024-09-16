<?php

namespace App\Http\Controllers;


use App\Models\Habit;
use App\Telegram\Commands\HabitSelectionCommand;
use App\Telegram\Commands\StartCommand;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;


class TelegramController extends Controller
{
    public function handle(Request $request)
    {
        $keyboard = Keyboard::make()
            ->setResizeKeyboard(true)
            ->row([
                Keyboard::button('Andijan'),
                Keyboard::button('Bukhara')
            ])->row([
                Keyboard::button('Fergana'),
                Keyboard::button('Jizzakh')
            ])->row([
                Keyboard::button('Namangan'),
                Keyboard::button('Navoiy	')
            ])->row([
                Keyboard::button('Qarshi'),
                Keyboard::button('Samarkand')
            ])->row([
                Keyboard::button("Guliston"),
                Keyboard::button('Termez')
            ])->row([
                Keyboard::button('Nurafshon'),
                Keyboard::button('Urgench')
            ])->row([
                Keyboard::button('Tashkent'),
            ]);
        try {
        $telegramMessage = $request->all();
        if (isset($telegramMessage['message']['text'])) {
            $chatId = $telegramMessage['message']['chat']['id'];
            $messageText = $telegramMessage['message']['text'];
            if ($telegramMessage['message']['text'] === '/start'){
                Telegram::sendMessage([
                    'chat_id'=>$chatId,
                    'text'=>"Salom. Ob-havoni ko'rsatadigan botimizga xush kelibsiz!",
                    'reply_markup' => $keyboard,
                ]);
            }else {
                if (preg_match('/^\\s*(.+)?$/', $messageText, $matches)) {
                    $city = $matches[1] ?? 'Tashkent';
                    $weatherData = $this->getWeather($city);
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => $weatherData,
                    ]);
//                    Log::info('message',$request->getUpdate());
                } else {
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => "Shahar nomini yuboring.",
                    ]);
                }
            }
        }
        }catch (Exception $exception){
            Log::error('error', (array)$exception);
//            Telegram::sendMessage([
//                'chat_id'=> $chatId,
//                'text'=>'Something went wrong!'
//            ]);
        }
        return 'ok';
    }

    private function getWeather($city)
    {
        $client = new Client();
        $apiKey = env('WEATHER_API_KEY');
        $url = "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}&units=metric";

        try {
            $response = $client->request('GET', $url);
            $data = json_decode($response->getBody(), true);

            if ($data['cod'] === 200) {
                $temp = $data['main']['temp'];
                $description = $data['weather'][0]['description'];
                $cityName = $data['name'];
                $countryName = $data['sys']['country'];

                return "Weather in {$cityName}, {$countryName}: {$temp}°C, {$description}.";
            } else {
                return "City not found. Please check the city name and try again.";
            }
        } catch (Exception $e) {
            return "Error: Unable to fetch weather data.";
        }

    }

}
