<?php

namespace App\Console\Commands;

use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Filters\EventsFilter;
use App\Console\Commands\Cron\GetLeadStatuses;
use App\Models\Event;
use App\Models\Staff;
use App\Services\amoCRM;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\UniqueConstraintViolationException;

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

        $staffs = Staff::query()
            ->where('group_id', ReportLeadsCommand::$group_leads)
            ->where('staff_id', '!=', 13028323)
            ->where('staff_id', '!=', 13261316)
            ->pluck('staff_id')
            ->toArray();

        foreach ($staffs as $staff) {

            $statusBefore['leads_statuses'][] = [
                'pipeline_id' => 11373956,
                'status_id'   => 88196676,
            ];

            $statusAfter['leads_statuses'][] = [
                'pipeline_id' => 11373956,
                'status_id'   => 87311640,
            ];

            for ($i = 1; ;$i++) {

                $filter = (new EventsFilter())
                    ->setTypes(['lead_status_changed'])
                    ->setValueBefore($statusBefore)
                    ->setValueAfter($statusAfter)
                    ->setCreatedBy($staff)
                    ->setPage($i)
                    ->setLimit(250);

                try {
                    $events = $client->events()->get($filter);

                    foreach ($events as $event) {

                        try {
                            $eventModel = Event::query()->create([
                                'event_id' => $event->getId()
                            ]);
                            $eventModel->event = 'stage-lead';
                            $eventModel->event_created_at = Carbon::parse($event->getCreatedAt())->format('Y-m-d');
                            $eventModel->lead_id = $event->getEntityId();
                            $eventModel->responsible_id = $event->getCreatedBy();
                            $eventModel->save();

                        } catch (UniqueConstraintViolationException $e) {

                            continue;
                        }
                    }
                } catch (AmoCRMMissedTokenException $e) {
                } catch (AmoCRMoAuthApiException $e) {
                } catch (AmoCRMApiException $e) {
                    dump($e->getLastRequestInfo());

                    sleep(5);

                    continue;
                }
            }
        }
    }

      public static function prepareStatusFilter(array $statuses) : array
    {
        $prepareStatuses = [];

        foreach ($statuses as $status) {

            $prepareStatuses['leads_statuses'][] = [
                'pipeline_id' => GetLeadStatuses::MAIN_PIPELINE_ID,
                'status_id'   => $status,
            ];

        }

        $prepareStatuses['leads_statuses'][] = [
            'pipeline_id' => GetLeadStatuses::MAIN_PIPELINE_ID,
            'status_id'   => 142,
        ];

        return $prepareStatuses;
    }
}
