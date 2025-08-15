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

    public function telegram(): void
    {
        Telegram::commandsHandler(true);
    }

    public function company(Request $request): void
    {
        $email = null;
        $web   = null;

        $fields = $request->contacts['add'][0]['custom_fields'] ?? [];

        foreach ($fields as $field) {

            if ($field['name'] == 'Email') {

                $email = $field['values'][0]['value'];
            }

            if ($field['name'] == 'Web') {

                $web = $field['values'][0]['value'];
            }
        }

        Artisan::call('app:check-company', [
            'email' => $email,
            'web'   => $web,
            'companyId' => $request->contacts['add'][0]['id'],
        ]);
    }
}
