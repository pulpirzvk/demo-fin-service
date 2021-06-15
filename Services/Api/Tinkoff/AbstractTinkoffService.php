<?php


namespace App\Services\Api\Tinkoff;


use App\Models\Instrument;
use App\Services\Api\IApiService;
use Illuminate\Support\Collection;
use jamesRUS52\TinkoffInvest\TIAccount;
use jamesRUS52\TinkoffInvest\TIClient;
use jamesRUS52\TinkoffInvest\TIException;
use jamesRUS52\TinkoffInvest\TIInstrument;
use jamesRUS52\TinkoffInvest\TIInstrumentInfo;
use jamesRUS52\TinkoffInvest\TIOperationEnum;
use jamesRUS52\TinkoffInvest\TIOrder;

abstract class AbstractTinkoffService implements IApiService
{
    private TIClient $client;

    private ?TIAccount $account = null;

    /**
     * Методы реализующие интерфейс IApiService
     */

    /**
     * Создать заявку на покупку по указанной цене (лимитный ордер)
     *
     * @param string $ticker
     * @param int $count
     * @param float $price
     * @return object
     * @throws TIException
     */
    public function createBuyLimitOrder(string $ticker, int $count, float $price): string
    {
        $figi = $this->getFigiByTicker($ticker);

        $order = $this->getClient()->sendOrder($figi, $count, TIOperationEnum::BUY, $price);

        return $order->getOrderId();
    }

    /**
     * Создать заявку на продажу по указанной цене (лимитный ордер)
     *
     * @param string $ticker
     * @param int $count
     * @param float $price
     * @return string
     * @throws TIException
     */
    public function createSellLimitOrder(string $ticker, int $count, float $price): string
    {
        $figi = $this->getFigiByTicker($ticker);

        $order = $this->getClient()->sendOrder($figi, $count, TIOperationEnum::SELL, $price);

        return $order->getOrderId();
    }

    /**
     * Создать заявку на покупку по цене рынка (рыночный ордер)
     *
     * @param string $ticker
     * @param int $count
     * @return string
     * @throws TIException
     */
    public function createBuyMarketOrder(string $ticker, int $count): string
    {
        $figi = $this->getFigiByTicker($ticker);

        $order = $this->getClient()->sendOrder($figi, $count, TIOperationEnum::BUY);

        return $order->getOrderId();
    }

    /**
     * Создать заявку на продажу по цене рынка (рыночный ордер)
     *
     * @param string $ticker
     * @param int $count
     * @return string
     * @throws TIException
     */
    public function createSellMarketOrder(string $ticker, int $count): string
    {
        $figi = $this->getFigiByTicker($ticker);

        $order = $this->getClient()->sendOrder($figi, $count, TIOperationEnum::SELL);

        return $order->getOrderId();
    }

    /**
     * Отменить заявку по ее ID в системе брокера
     *
     * @param string $orderId
     * @throws TIException
     */
    public function cancelOrder(string $orderId): void
    {
        $this->getClient()->cancelOrder($orderId);
    }

    /**
     * Методы индивидуальные для Тинькофф API
     */

    /**
     * Получить FIGI по имени тикера
     *
     * @param string $ticker
     * @return string
     * @throws TIException
     */
    public function getFigiByTicker(string $ticker): string
    {
        $figi = Instrument::query()->where(['ticker' => $ticker])->pluck('figi')->first();

        if (!is_null($figi)) {
            return $figi;
        }

        $instrument = $this->getClient()->getInstrumentByTicker($ticker);

        return $instrument->getFigi();
    }

    /**
     * Получить информацию о инструменте по его тикеру
     *
     * @param string $tiker
     * @return TIInstrumentInfo
     * @throws TIException
     */
    public function getInfo(string $tiker): TIInstrumentInfo
    {
        return $this->getClient()->getInstrumentInfo($this->getFigiByTicker($tiker));
    }

    /**
     * Получить информацию о статусе торгов по инструменту
     *
     * normal_trading - идет нормальная торговля
     *
     * @param string $tiker
     * @return string
     * @throws TIException
     */
    public function getTradeStatus(string $tiker): string
    {
        return $this->getInfo($tiker)->getTrade_status();
    }

    /**
     * Получить список нереализованных заявок
     *
     * Весь или по списку id на стороне биржи
     *
     * @param array|null $ids
     * @return Collection|TIOrder[]
     * @throws TIException
     */
    public function getOrders(array $ids = null): Collection
    {
        return collect($this->getClient()->getOrders($ids));
    }

    /**
     * Получить нереализованную заявку по ее id на стороне биржи
     *
     * @param string $orderId
     * @return TIOrder
     * @throws TIException
     */
    public function getOrder(string $orderId): TIOrder
    {
        return $this->getOrders([$orderId])->first();
    }

    /**
     * Получить список всех торгуемых у Тинькова акций
     *
     * @param array|null $tickers
     * @return Collection|TIInstrument[]
     * @throws TIException
     */
    public function getStocks(array $tickers = null): Collection
    {
        return collect($this->getClient()->getStocks($tickers));
    }

    /**
     * Получить список всех торгуемых у Тинькова облигаций
     *
     * @param array|null $tickers
     * @return Collection|TIInstrument[]
     * @throws TIException
     */
    public function getBonds(array $tickers = null): Collection
    {
        return collect($this->getClient()->getBonds($tickers));
    }

    /**
     * Получить список всех торгуемых у Тинькова ETF
     *
     * @param array|null $tickers
     * @return Collection|TIInstrument[]
     * @throws TIException
     */
    public function getEtfs(array $tickers = null): Collection
    {
        return collect($this->getClient()->getEtfs($tickers));
    }

    /**
     * Получить список всех торгуемых у Тинькова валюты
     *
     * @param array|null $tickers
     * @return Collection|TIInstrument[]
     * @throws TIException
     */
    public function getCurrencies(array $tickers = null): Collection
    {
        return collect($this->getClient()->getCurrencies($tickers));
    }

    /**
     * Получить акцию по ее тикеру
     *
     * @param string $ticker
     * @return TIInstrument
     * @throws TIException
     */
    public function getStock(string $ticker): TIInstrument
    {
        return $this->getClient()->getInstrumentByTicker($ticker);
    }

    /**
     * Получит клиент для работы с API
     *
     * @return TIClient
     */
    public function getClient(): TIClient
    {
        return $this->client;
    }

    /**
     * Установить клиент для работы с API
     *
     * @param TIClient $client
     * @return AbstractTinkoffService
     */
    public function setClient(TIClient $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Получить аккаунт пользователя у Тинькова
     *
     * @return TIAccount|null
     * @throws TIException
     */
    public function getAccount(): ?TIAccount
    {
        if (empty($this->account)) {
            $accounts = $this->getClient()->getAccounts();

            if (!empty($accounts)) {
                $this->setAccount($accounts[0]);
            }
        }

        return $this->account;
    }

    /**
     * @param TIAccount $account
     */
    protected function setAccount(TIAccount $account): void
    {
        $this->account = $account;
    }

}
