<?php

namespace App\Console\Commands;

use AmoCRM\Collections\TasksCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\TaskModel;
use App\Services\amoCRM;
use Illuminate\Console\Command;

class CheckCompany extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-company {email?} {web?} {companyId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    static array $emails = [
        'triumph-zaun.de',
        'kanzleischroeder-kiel.de',
        'gruenlaw.de',
        'start100.de',
        'info@kanzlei.biz',
        'kontakt@jungepflanzen.de',
        'mail@maxhealth.de',
        'abimanufaktur.de',
    ];
    /**
     * Execute the console command.
     * @throws AmoCRMMissedTokenException
     */
    public function handle()
    {
        $isIgnore = false;

        foreach (static::$emails as $email) {

            if (strripos($this->argument('email'), $email) !== false)

                $isIgnore = true;
        }

        foreach (static::$emails as $email) {

            if (strripos($this->argument('web'), $email) !== false)

                $isIgnore = true;
        }

        if ($isIgnore) {

            $tasksCollection = new TasksCollection();
            $task = new TaskModel();
            $task->setTaskTypeId(TaskModel::TASK_TYPE_ID_FOLLOW_UP)
                ->setText('Добавлена компания из черного списка')
                ->setCompleteTill(time() + 30)
                ->setEntityType(EntityTypesInterface::COMPANIES)
                ->setEntityId($this->argument('companyId'))
                ->setDuration(30 * 60) //30 минут
                ->setResponsibleUserId(12710571); //kirill
            $tasksCollection->add($task);

            $tasksService = amoCRM::init()->tasks();

            $tasksService->add($tasksCollection);
        }
    }
}
