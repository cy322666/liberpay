<?php

namespace App\Console\Commands;

use App\Models\ListCompany;
use Illuminate\Console\Command;

class CheckCompanyList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-company-list';

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
        $companies = ListCompany::query()->get();

        foreach ($companies as $company) {


        }
    }
}
