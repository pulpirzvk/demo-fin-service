<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Konekt\Enum\Eloquent\CastsEnums;

/**
 * Class Order
 * @package App\Models
 * @property OrderStatus $status
 */
class Order extends Model
{
    use SoftDeletes,
        CastsEnums;

    protected $dates = [
        'deleted_at',
    ];

    protected $casts = [
        'enter_price' => 'decimal:4',
        'exit_price' => 'decimal:4',
        'stop_loss_price' => 'decimal:4',
        'amount' => 'decimal:2',
    ];

    protected $enums = [
        'status' => OrderStatus::class,
    ];

    protected $fillable = [
        'ticker',
        'amount',
        'enter_price',
        'exit_price',
        'platform_id',
        'stop_loss_price',
        'status',
    ];

    /**
     * Все заявки по сделке
     *
     * @return HasMany|OrderStage|OrderStage[]
     */
    public function stages(): HasMany
    {
        return $this->hasMany(OrderStage::class);
    }

    /**
     * @return HasMany|OrderTakeProfitStage[]
     */
    public function takeProfitStages(): HasMany
    {
        return $this->hasMany(OrderTakeProfitStage::class);
    }

    /**
     * @return HasOne|OrderStopLossStage
     */
    public function stopLossStage(): HasOne
    {
        return $this->hasOne(OrderStopLossStage::class);
    }

    /**
     * @return BelongsTo|Instrument
     */
    public function instrument(): BelongsTo
    {
        return $this->belongsTo(Instrument::class, 'ticker', 'ticker');
    }

    /**
     * @return HasOne|User
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
