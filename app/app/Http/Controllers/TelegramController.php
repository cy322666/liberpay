<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class TelegramController extends Controller
{
    protected Api $client;

    public function __construct(Api $telegram)
    {
        $this->client = $telegram;
    }

    /**
     * @throws TelegramSDKException
     */
    public function report()
    {
        $response = $this->client->getMe();

        return $response;
    }
}
