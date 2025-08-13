<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReportLeadsCommand extends \Telegram\Bot\Commands\Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'reportleads';

    protected string $name = 'reportleads';
    protected string $description = 'Отчет по лидам';

    public static int $group_leads = 642464;

    public function handle(): void
    {
        $this->replyWithMessage([
            'text' => static::text(),
        ]);
    }

    private static function text() : string
    {
        $text = '*Отчет по лидам (неделя/день)*'.PHP_EOL.PHP_EOL;

        $staffs = Staff::query()
            ->where('group_id', static::$group_leads)
            ->where('archived', 0)
            ->get();

        foreach ($staffs as $staff) {

            $countWeek = Event::query()
                ->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])
                ->where('responsible_id', $staff->staff_id)
                ->count();

            $countDay = Event::query()
                ->whereDate('created_at', Carbon::today())
                ->where('responsible_id', $staff->staff_id)
                ->count();

            $text .= $staff->name.' : '.$countWeek.'/'.$countDay.PHP_EOL;
        }

        return $text;
    }
}
