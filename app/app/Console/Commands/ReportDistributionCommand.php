<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReportDistributionCommand extends \Telegram\Bot\Commands\Command
{
    protected string $signature = 'reportDistleads';

    protected string $name = 'reportDistleads';
    protected string $description = 'Отчет по переданным лидам';

    public static int $group_leads = 642464;

    public function handle(): void
    {
        $this->replyWithMessage(['text' => static::text()]);
    }

    private static function text() : string
    {
        $text = 'Передано лидов (д | н | м)'.PHP_EOL.PHP_EOL;

        $staffs = Staff::query()
            ->where('group_id', static::$group_leads)
            ->where('archived', 0)
            ->get();

        foreach ($staffs as $staff) {

            $countMonth = Event::query()
                ->whereBetween('created_at', [Carbon::now()->subMonth(), Carbon::now()])
                ->where('event', 'stage-lead')
                ->where('responsible_id_tech', $staff->staff_id)
                ->count();


            $countWeek = Event::query()
                ->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])
                ->where('event', 'stage-lead')
                ->where('responsible_id_tech', $staff->staff_id)
                ->where('responsible_id', $staff->staff_id)
                ->count();

            $countDay = Event::query()
                ->whereDate('created_at', Carbon::today())
                ->where('event', 'stage-lead')
                ->where('responsible_id_tech', $staff->staff_id)
                ->where('responsible_id', $staff->staff_id)
                ->count();

            $text .= $staff->name.' : '.$countDay.' | '.$countWeek.' | '.$countMonth.PHP_EOL;
        }

        return $text;
    }
}
