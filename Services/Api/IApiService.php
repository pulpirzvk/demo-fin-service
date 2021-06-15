<?php


namespace App\Services\Api;


interface IApiService
{
    /**
     * Создать заявку на покупку по указанной цене (лимитный ордер)
     *
     * @param string $ticker
     * @param int $count
     * @param float $price
     * @return string
     */
    public function createBuyLimitOrder(string $ticker, int $count, float $price): string;

    /**
     * Создать заявку на продажу по указанной цене (лимитный ордер)
     *
     * @param string $ticker
     * @param int $count
     * @param float $price
     * @return string
     */
    public function createSellLimitOrder(string $ticker, int $count, float $price): string;

    /**
     * Создать заявку на покупку по цене рынка (рыночный ордер)
     *
     * @param string $ticker
     * @param int $count
     * @return string
     */
    public function createBuyMarketOrder(string $ticker, int $count): string;

    /**
     * Создать заявку на продажу по цене рынка (рыночный ордер)
     *
     * @param string $ticker
     * @param int $count
     * @return string
     */
    public function createSellMarketOrder(string $ticker, int $count): string;

    /**
     * Отменить заявку по ее ID в системе брокера
     *
     * @param string $orderId
     */
    public function cancelOrder(string $orderId): void;
}
