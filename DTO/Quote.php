<?php


namespace App\DTO;


use App\Enums\InstrumentType;

class Quote
{
    /**
     * @var Currency
     */
    public Currency $currency;

    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $ticker;

    /**
     * @var string
     */
    public string $price;

    /**
     * @var string
     */
    public string $change;

    /**
     * @var string
     */
    public string $type;

    /**
     * Quote constructor.
     * @param string $name
     * @param string $ticker
     * @param string $price
     * @param string $change
     * @param Currency $currency
     * @param string $type
     */
    public function __construct(
        string $name,
        string $ticker,
        string $price,
        string $change,
        Currency $currency,
        string $type = InstrumentType::STOCK
    )
    {
        $this->currency = $currency;
        $this->name = $name;
        $this->ticker = $ticker;
        $this->price = $price;
        $this->change = $change;
        $this->type = $type;
    }
}
