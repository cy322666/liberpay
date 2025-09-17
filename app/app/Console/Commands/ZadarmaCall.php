<?php

namespace App\Console\Commands;

use AmoCRM\Collections\EventsCollections;
use AmoCRM\Collections\NotesCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\NoteModel;
use AmoCRM\Models\NoteType\ServiceMessageNote;
use App\Services\amoCRM;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Console\Command;

class ZadarmaCall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:zadarma-call';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle()
    {
        //https://zadarma.com/ru/support/api/#api_callback

        $sip = 691763;
        $key = 'ac6ac5375e501d952d41';
        $secret = '23451161956f0d6f6cdf';
//
//        $params = [
////            'sip' => $sip,
//            'from' => '+493052014942',
////            'from' => $sip,
////            'to' => '+495419997350',
//            'to' => '+4917681447979',
////            'predicted' => '',
//        ];
//
        $zd = new \Zadarma_API\Client($key, $secret);
        //это внутренние
//        $sips = $zd->call('/v1/pbx/internal/', [], 'GET');

        //
//        $sips = $zd->call('/v1/pbx/internal/103/status/', [], 'GET');
        $sips = $zd->call('/v1/pbx/webhooks/', [], 'GET');
        dd($sips);


        //это звонок
//        /*
//        $zd->call('METHOD', 'PARAMS_ARRAY', 'REQUEST_TYPE', 'FORMAT', 'IS_AUTH');
//        where:
//        - METHOD - a method API, started from /v1/ and ended by '/';
//        - PARAMS_ARRAY - an array of parameters to a method;
//        - REQUEST_TYPE: GET (default), POST, PUT, DELETE;
//        - FORMAT: json (default), xml;
//        - IS_AUTH: true (default), false - is method under authentication or not.
//        */
//        $answer = $zd->call('/v1/request/callback/', $params, 'GET');
//
//        $answerObject = json_decode($answer);
//
//        if ($answerObject->status == 'success') {
//
//            dump($answerObject);
////            dump('Redirection on your SIP "' . $answerObject->sip . " has been changed to " . $answerObject->current_status . ".");
//
//        } else {
//            dump($answerObject);
////            dump($answerObject->message);
//        }


//        try {

//            $notesCollection = new NotesCollection();
//            $serviceMessageNote = new ServiceMessageNote();
//            $serviceMessageNote->setEntityId(19731711)
//                ->setText('Текст примечания')
//                ->setService('Api Library')
//                ->setCreatedBy(0);
//
//            $notesCollection->add($serviceMessageNote);
//
//            try {
//                $leadNotesService =  amoCRM::init()->notes(EntityTypesInterface::LEADS);
//                $notesCollection = $leadNotesService->add($notesCollection);
//            } catch (AmoCRMApiException $e) {
//                printError($e);
//                die;
//            }
            ///api/v4/{entity_type}/{entity_id}/notes
//            amoCRM::init()->getRequest()->post('api/v2/events', [
//                'add' => [
//                    [
//                        'type' => "phone_call",
//                        'phone_number' => "+4915231738874",
//                        'users' => [
//                            13724332
////                            '5998951'
//                        ]
//                    ]
//                ]
//            ]);
//
//
//
//        } catch (AmoCRMApiException $e) {
//
//            dd($e->getDescription(), $e->getErrorCode(), $e->getLastRequestInfo());
//        }
    }
}
