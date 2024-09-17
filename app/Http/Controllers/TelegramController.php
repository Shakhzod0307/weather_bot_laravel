<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Laravel\Facades\Image;
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
                Keyboard::button('Navoiy')
            ])->row([
                Keyboard::button('Qarshi'),
                Keyboard::button('Samarkand')
            ])->row([
                Keyboard::button('Guliston'),
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
                if ($telegramMessage['message']['text'] === '/start') {
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => "Salom. Ob-havoni ko'rsatadigan botimizga xush kelibsiz!",
                        'reply_markup' => $keyboard,
                    ]);
                } else {
                    if (preg_match('/^\\s*(.+)?$/', $messageText, $matches)) {
                        $city = $matches[1] ?? 'Tashkent';
                        $weatherData = $this->getWeather($city);
                        if ($weatherData) {
                            Telegram::sendPhoto([
                                'chat_id' => $chatId,
                                'photo' => InputFile::create($weatherData), // Send the generated weather image
                                'caption' => "Here is the weather data for {$city}!",
                            ]);
                        } else {
                            Telegram::sendMessage([
                                'chat_id' => $chatId,
                                'text' => "Error fetching weather data.",
                            ]);
                        }
                    } else {
                        Telegram::sendMessage([
                            'chat_id' => $chatId,
                            'text' => "Shahar nomini yuboring.",
                        ]);
                    }
                }
            }
        } catch (\Exception $exception) {
            report($exception);
            Log::error('exp', ['message' => $exception->getMessage()]);
            return response('error', 200);
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
                return $this->generateWeatherImage($data); // Return the generated image path
            } else {
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Error fetching weather data: ' . $e->getMessage());
            return null;
        }
    }

    public function generateWeatherImage($data)
    {
        $background = Image::make(public_path('images/weather-bg.jpg'));

        $city = $data['name'];
        $temp = $data['main']['temp'];
        $description = $data['weather'][0]['description'];
        $humidity = $data['main']['humidity'];
        $windSpeed = $data['wind']['speed'];

        $background->text("Weather in {$city}", 100, 100, function ($font) {
            $font->file(public_path('fonts/Arial.ttf'));
            $font->size(36);
            $font->color('#FFFFFF');
            $font->align('left');
            $font->valign('top');
        });

        $background->text("Temperature: {$temp}°C", 100, 150, function ($font) {
            $font->file(public_path('fonts/Arial.ttf'));
            $font->size(30);
            $font->color('#FFFFFF');
        });

        $background->text("Description: {$description}", 100, 200, function ($font) {
            $font->file(public_path('fonts/Arial.ttf'));
            $font->size(30);
            $font->color('#FFFFFF');
        });

        $background->text("Humidity: {$humidity}%", 100, 250, function ($font) {
            $font->file(public_path('fonts/Arial.ttf'));
            $font->size(30);
            $font->color('#FFFFFF');
        });

        $background->text("Wind Speed: {$windSpeed} m/s", 100, 300, function ($font) {
            $font->file(public_path('fonts/Arial.ttf'));
            $font->size(30);
            $font->color('#FFFFFF');
        });

        $imagePath = public_path('images/generated-weather.png');
        $background->save($imagePath);

        return $imagePath; // Return the image path to be sent
    }
}
