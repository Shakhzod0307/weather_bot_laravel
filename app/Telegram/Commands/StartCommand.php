<?php


namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = 'Start interacting with the bot';

    public function handle()
    {
        $keyboard = Keyboard::make()
            ->setResizeKeyboard(true)
            ->row([
                Keyboard::button('Andijon'),
                Keyboard::button('Buxoro')
            ])->row([
                Keyboard::button('Fargʻona'),
                Keyboard::button('Jizzax')
            ])->row([
                Keyboard::button('Xorazm'),
                Keyboard::button('Namangan')
            ])->row([
                Keyboard::button('Navoiy'),
                Keyboard::button('Qashqadaryo')
            ])->row([
                Keyboard::button("Qoraqalpogʻiston"),
                Keyboard::button('Samarqand')
            ])->row([
                Keyboard::button('Sirdaryo'),
                Keyboard::button('Surxondaryo')
            ])->row([
                Keyboard::button('Toshkent'),
            ])
        ;

        $this->replyWithMessage([
            'text' => 'Hush kelibsiz! Odatlarni tanlang:',
//            'reply_markup' => $keyboard,
        ]);
    }
}
