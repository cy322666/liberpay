<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Staff;
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
        $text = '';

        $staffs = Staff::query()
            ->where('group', static::$group_leads)
            ->where('archived', 0)
            ->get();

        foreach ($staffs as $staff) {

            $count = Event::query()
                ->where('responsible_id', $staff->staff_id)
                ->count();

            $text .= $staff->name.' : '.$count.PHP_EOL;
        }

        return $text;
    }
}
