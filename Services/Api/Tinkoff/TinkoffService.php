<?php


namespace App\Services\Api\Tinkoff;


use jamesRUS52\TinkoffInvest\TIClient;
use jamesRUS52\TinkoffInvest\TISiteEnum;

class TinkoffService extends AbstractTinkoffService
{
    public function __construct(string $token)
    {
        $this->setClient(new TIClient($token, TISiteEnum::EXCHANGE));
    }
}
