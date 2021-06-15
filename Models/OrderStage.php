<?php

namespace App\Models;

use App\Enums\OrderStageStatus;
use App\Enums\OrderStageType;
use Illuminate\Database\Eloquent\Model;
use Konekt\Enum\Eloquent\CastsEnums;

/**
 * Class OrderStage
 * @package App\Models
 * @property OrderStageStatus $status
 * @property OrderStageType $type
 */
class OrderStage extends Model
{
    use CastsEnums;

    protected $casts = [
        'take_profit_price' => 'decimal:4',
    ];

    protected $fillable = [
        'num',
        'proportion',
        'take_profit_price',
        'status',
        'rialto_order_id',
        'type',
    ];

    protected $enums = [
        'status' => OrderStageStatus::class,
        'type' => OrderStageType::class
    ];
}
