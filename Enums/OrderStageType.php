<?php

namespace App\Enums;

use Konekt\Enum\Enum;

class OrderStageType extends Enum
{
    public const SELL = 10;
    public const TAKE_PROFIT = 14;
    public const STOP_LOSS = 16;
    public const BUY = 20;
}
