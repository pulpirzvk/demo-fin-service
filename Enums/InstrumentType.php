<?php

namespace App\Enums;

use Konekt\Enum\Enum;

class InstrumentType extends Enum
{
    public const STOCK = 10;
    public const ETF = 20;
    public const BOND = 30;
    public const CURRENCY = 40;

    public static $labels = [
        self::STOCK => 'STOCK',
        self::ETF => 'ETF',
        self::BOND => 'BOND',
        self::CURRENCY => 'CURRENCY',
    ];
}
