<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Staff;
use Illuminate\Console\Command;

class ReportSaleCommand extends \Telegram\Bot\Commands\Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'app:report-sale-command';

    protected string $name = 'report';
    protected string $description = 'Отчет по менеджерам';

    public function handle(): void
    {
        $this->replyWithMessage([
            'text' => static::text(),
        ]);
    }

    private static function text() : string
    {
        $text = '';

        $staffs = Staff::all();

        foreach ($staffs as $staff) {

            $count = Event::query()
                ->where('responsible_id', $staff->staff_id)
                ->count();

            $text .= $staff->name.' : '.$count.PHP_EOL;
        }

        return $text;
    }
}
