<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-emails';

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
        $companyUrls = Company::query()
            ->where('status', 0)
            ->where('emails', null)
            ->get()
            ->pluck('url');

        foreach ($companyUrls as $companyUrl) {

            $companyUrlNew = str_replace(';', '', $companyUrl);

            try {

                $response = Http::get($companyUrlNew);

            } catch (\Exception $e) {
                dump($e->getMessage());

                Company::query()
                    ->where('url', $companyUrl)
                    ->update([
                        'status' => 9,
                    ]);

                continue;
            }

            if ($response->successful()) {

                preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $response->body(), $matches);

                $emails = json_decode(response()->json($matches)->getContent(), true)[0];

                $collection = collect($emails);

                $uniqueCollection = $collection->unique();

                $uniqueArray = $uniqueCollection->values()->all();

                Company::query()
                    ->where('url', $companyUrl)
                    ->update([
                        'emails' => $uniqueArray,
                        'url'    => $companyUrlNew,
                        'status' => 1
                    ]);

            } else {

                Company::query()
                    ->where('url', $companyUrl)
                    ->update([
                        'url'    => $companyUrlNew,
                        'status' => 9
                    ]);
            }

        }
    }
}
