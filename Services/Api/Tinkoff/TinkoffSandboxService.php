<?php


namespace App\Services\Api\Tinkoff;


use jamesRUS52\TinkoffInvest\TIAccount;
use jamesRUS52\TinkoffInvest\TIClient;
use jamesRUS52\TinkoffInvest\TICurrencyEnum;
use jamesRUS52\TinkoffInvest\TISiteEnum;

class TinkoffSandboxService extends AbstractTinkoffService
{
    public function __construct()
    {
        $this->setClient(new TIClient(config('platforms.tinkoff.sandbox_key'), TISiteEnum::SANDBOX));

        $this->initSandbox();
    }

    /**
     * Если у данного пользователя нет брокерского аккаунта то создаем его и пополняем баланс
     * @throws \jamesRUS52\TinkoffInvest\TIException
     */
    protected function initSandbox(): void
    {
        if ($this->getAccount() === null) {
            $this->registerAccount();
        }

        $this->getClient()->sbCurrencyBalance(50000, TICurrencyEnum::USD);
        $this->getClient()->sbCurrencyBalance(1000000, TICurrencyEnum::RUB);
    }

    /**
     * Создаем аккаунт
     *
     * @return TIAccount
     * @throws \jamesRUS52\TinkoffInvest\TIException
     */
    public function registerAccount(): TIAccount
    {
        $this->setAccount($this->getClient()->sbRegister());

        return $this->getAccount();
    }
}
