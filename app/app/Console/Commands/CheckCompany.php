<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckCompany extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-company {email?} {web?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    static array $emails = [
        'triumph-zaun.de',
        'kanzleischroeder-kiel.de',
    ];
    /**
     * Execute the console command.
     */
    public function handle()
    {
        foreach (static::$emails as $email) {

            if (strripos($this->argument('email'), $email) !== false)

                return true;
        }

        foreach (static::$emails as $email) {

            if (strripos($this->argument('web'), $email) !== false)

                return true;
        }
    }
}
