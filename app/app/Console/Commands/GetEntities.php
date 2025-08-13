<?php

namespace App\Console\Commands;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Filters\BaseEntityFilter;
use AmoCRM\Models\UserModel;
use App\Models\Staff;
use App\Models\User;
use App\Services\amoCRM;
use Carbon\Carbon;
use Illuminate\Console\Command;

use function PHPUnit\Framework\throwException;

class GetEntities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-entities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    /**
     * @var AmoCRMApiClient
     */
    public AmoCRMApiClient $client;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->client = amoCRM::init();

        try {

            $users = ($this->client->users())->get(null, ['group', 'role']);

            Staff::query()->truncate();

            /** @var UserModel $user */
            foreach ($users as $user) {

                Staff::query()->updateOrCreate([
                    'staff_id' => $user->getId(),
                ], [
                    'name'     => $user->getName(),
                    'email'    => $user->getEmail(),
                    'is_admin' => $user->getRights()->getIsAdmin(),
                    'archived' => !$user->getRights()->getIsActive(),
                    'role_id'  => $user->getRights()->getRoleId(),
                    'group_id' => $user->getRights()->getGroupId(),
                ]);
            }

        } catch (AmoCRMMissedTokenException|AmoCRMoAuthApiException|AmoCRMApiException $e) {

            throwException($e->getMessage() .' '. $e->getLastRequestInfo());
        }
    }
}
