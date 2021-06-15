<?php


namespace App\Services\Tinkoff;


use App\Enums\InstrumentType;
use App\Models\Instrument;
use App\Services\Api\IApiService;
use App\Services\Api\Tinkoff\TinkoffService;
use Illuminate\Support\Collection;
use jamesRUS52\TinkoffInvest\TIInstrument;
use jamesRUS52\TinkoffInvest\TIOrderBook;

class InstrumentService
{

    /** @var IApiService|TinkoffService */
    protected IApiService $api;

    /**
     * OrderService constructor.
     * @param IApiService $api
     */
    public function __construct(IApiService $api)
    {
        $this->api = $api;
    }

    /**
     * @param string $figi
     * @return TIOrderBook
     * @throws \jamesRUS52\TinkoffInvest\TIException
     */
    public function getInfo(string $figi): TIOrderBook
    {
        return $this->api->getClient()->getHistoryOrderBook($figi);
    }

    /**
     * @return int
     * @throws \jamesRUS52\TinkoffInvest\TIException
     */
    public function updateCurrencyList(): int
    {
        /** @var Collection|TIInstrument[] $items */
        $items = $this->api->getCurrencies();

        $items->each(function (TIInstrument $item) {
            $this->create($item, InstrumentType::CURRENCY);
        });

        return $items->count();
    }

    /**
     * @return int
     * @throws \jamesRUS52\TinkoffInvest\TIException
     */
    public function updateEtfList(): int
    {
        /** @var Collection|TIInstrument[] $items */
        $items = $this->api->getEtfs();

        $items->each(function (TIInstrument $item) {
            $this->create($item, InstrumentType::ETF);
        });

        return $items->count();
    }

    /**
     * @return int
     * @throws \jamesRUS52\TinkoffInvest\TIException
     */
    public function updateBondList(): int
    {
        /** @var Collection|TIInstrument[] $items */
        $items = $this->api->getBonds();

        $items->each(function (TIInstrument $item) {
            $this->create($item, InstrumentType::BOND);
        });

        return $items->count();
    }

    /**
     * @return int
     * @throws \jamesRUS52\TinkoffInvest\TIException
     */
    public function updateStockList(): int
    {
        /** @var Collection|TIInstrument[] $items */
        $items = $this->api->getStocks();

        $items->each(function (TIInstrument $item) {
            $this->create($item, InstrumentType::STOCK);
        });

        return $items->count();
    }

    /**
     * @param TIInstrument $item
     * @param string $type
     * @throws \Exception
     */
    protected function create(TIInstrument $item, string $type): void
    {
        Instrument::firstOrCreate(
            [
                'figi' => $item->getFigi(),
                'ticker' => $item->getTicker(),
                'isin' => $item->getIsin()
            ],
            [
                'type' => $type,
                'minPriceIncrement' => $item->getMinPriceIncrement(),
                'lot' => $item->getLot(),
                'currency' => $item->getCurrency(),
                'name' => $item->getName(),
                'price' => sprintf('%s.%s', random_int(0, 99), random_int(0, 9999)),
                'change' => sprintf('%s%s.%s', random_int(0, 1) ? '-' : '', random_int(0, 5), random_int(0, 99)),
            ]);
    }
}
