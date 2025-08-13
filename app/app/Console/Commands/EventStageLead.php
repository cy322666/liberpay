<?php

namespace App\Console\Commands;

use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use App\Models\Event;
use App\Services\amoCRM;
use Illuminate\Console\Command;

class EventStageLead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:event-stage-lead {event_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     * @throws AmoCRMMissedTokenException
     */
    public function handle()
    {
        $client = amoCRM::init();

        $event = Event::query()->find($this->argument('event_id'));

        $lead = $client->leads()->getOne($event->lead_id);

        foreach ($lead->getCustomFieldsValues() as $customField) {

            if ($customField->getFieldId() == 955703) {

                $event->responsible_id_tech = $customField->getValues()->first();
            }
        }

        $event->responsible_id = $lead->getResponsibleUserId();
        $event->company_id = $lead->getCompany()?->getId();
        $event->contact_id = $lead->getMainContact()?->getId();
        $event->save();
    }
}
