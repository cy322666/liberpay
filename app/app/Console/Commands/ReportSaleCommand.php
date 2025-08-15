<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReportSaleCommand extends \Telegram\Bot\Commands\Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'reportsales';

    protected string $name = 'reportsales';
    protected string $description = 'Отчет по лидам оп';

    public static int $group_op = 642468;

    public function handle(): void
    {
        $this->replyWithMessage(['text' => static::text()]);
    }

    private static function text() : string
    {
        $text = 'Взято в работу (д | н | м)'.PHP_EOL.PHP_EOL;

        $staffs = Staff::query()
            ->where('group_id', static::$group_op)
            ->where('archived', 0)
            ->get();

        foreach ($staffs as $staff) {

            $countMonth = Event::query()
                ->whereBetween('created_at', [Carbon::now()->subMonth(), Carbon::now()])
                ->where('event', 'stage-sale')
                ->where('responsible_id', $staff->staff_id)
                ->count();


            $countWeek = Event::query()
                ->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])
                ->where('event', 'stage-sale')
                ->where('responsible_id', $staff->staff_id)
                ->count();

            $countDay = Event::query()
                ->whereDate('created_at', Carbon::today())
                ->where('event', 'stage-sale')
                ->where('responsible_id', $staff->staff_id)
                ->count();

            $text .= $staff->name.' : '.$countDay.' | '.$countWeek.' | '.$countMonth.PHP_EOL;
        }

        return $text;
    }
}
