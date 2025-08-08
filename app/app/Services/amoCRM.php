<?php

namespace App\Services;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Client\LongLivedAccessToken;

class amoCRM
{
    public static function init(): AmoCRMApiClient
    {
        $apiClient = new AmoCRMApiClient();

        $longLivedAccessToken = new LongLivedAccessToken(env('KOMMO_LONG'));

        $apiClient->setAccessToken($longLivedAccessToken)
            ->setAccountBaseDomain('liberpay.amocrm.com');

        return $apiClient;
    }
}
