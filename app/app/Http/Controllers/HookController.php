<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Telegram\Bot\Laravel\Facades\Telegram;

class HookController extends Controller
{
    //лидов взято в работу
    public function leads(Request $request): void
    {
        $event = Event::query()
            ->create([
                'lead_id' => $request->leads['status'][0]['id'],
                'event'   => 'stage-lead'
            ]);

        Artisan::call('app:event-stage-lead', ['event_id' => $event->id]);
    }

    //лидов передано в оп
    public function sales(Request $request): void
    {
        $event = Event::query()
            ->create([
                'lead_id' => $request->leads['status'][0]['id'],
                'event'   => 'stage-sale'
            ]);

        Artisan::call('app:event-stage-sale', ['event_id' => $event->id]);
    }

    public function telegram(Request $request): void
    {
        $update = Telegram::commandsHandler(true);

        $message = $update->getMessage();
        $chat_id = $message->getChat()->getId();

        Telegram::sendMessage([
            'chat_id' => $chat_id,
            'text' => $message,
        ]);
    }
}
