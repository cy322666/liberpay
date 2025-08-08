<?php

namespace App\Console\Commands;

use AmoCRM\Client\LongLivedAccessToken;
use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiClient = new \AmoCRM\Client\AmoCRMApiClient();

        $longLivedAccessToken = new LongLivedAccessToken(env('KOMMO_LONG'));

        $apiClient->setAccessToken($longLivedAccessToken)
            ->setAccountBaseDomain('liberpay.amocrm.com');

        $users = $apiClient->users()->get()->toArray();

        foreach ($users as $user) {

            echo $user['name'] . "\n";
        }
    }
}
