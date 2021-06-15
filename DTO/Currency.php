<?php


namespace App\DTO;


class Currency
{
    /**
     * @var string
     */
    public string $code;

    /**
     * @var string
     */
    public string $sign;

    /**
     * Currency constructor.
     * @param string $code
     * @param string $sign
     */
    public function __construct(string $code, string $sign)
    {
        $this->code = $code;
        $this->sign = $sign;
    }
}
