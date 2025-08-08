<?php

namespace App\Http\Controllers;

use App\Jobs\EventStageLead;
use App\Jobs\EventStageSale;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class HookController extends Controller
{
    //лидов взято в работу
    public function stageLeads(Request $request)
    {
        $event = Event::query()
            ->create([
                'lead_id' => $request->leads['status'][0]['id'],
                'event'   => 'stage-lead'
            ]);

        Artisan::call('app:event-stage-lead', ['event_id' => $event->id]);
    }

    //лидов передано в оп
    public function stageSale(Request $request)
    {
        $event = Event::query()
            ->create([
                'lead_id' => $request->leads['status'][0]['id'],
                'event'   => 'stage-sale'
            ]);

        Artisan::call('app:event-stage-sale', ['event_id' => $event->id]);
    }
}
