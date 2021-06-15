<?php


namespace App\Services\Order;


use App\Enums\OrderStageStatus;
use App\Enums\OrderStageType;
use App\Enums\OrderStatus;
use App\Exceptions\OrderNotFoundException;
use App\Models\Order;
use App\Services\Api\IApiService;
use Illuminate\Support\Facades\DB;
use jamesRUS52\TinkoffInvest\TIException;

class OrderService
{
    protected IApiService $api;

    /**
     * OrderService constructor.
     * @param IApiService $api
     */
    public function __construct(IApiService $api)
    {
        $this->api = $api;
    }

    public function getApi(): IApiService
    {
        return $this->api;
    }

    public function send(Order $order): bool
    {
        if ($order->status->value() === OrderStatus::NEED_SEND) {

            DB::beginTransaction();
            try {
                $order->update(['status' => OrderStatus::SENDING]);
                $order->stages()->update(['status' => OrderStageStatus::SENDING]);

                foreach ($order->stages as $fip) {

                    $stageRialtoId = $this->api->createBuyLimitOrder(
                        $order->ticker,
                        1,
                        $fip->take_profit_price,
                    );

                    $fip->update([
                        'rialto_order_id' => $stageRialtoId,
                        'status' => OrderStageStatus::ON_MARKET,
                    ]);
                }

                $order->update(['status' => OrderStatus::ON_MARKET]);

                DB::commit();
            } catch (TIException $e) {
                DB::rollBack();
                throw $e;
            }

            return true;
        }

        return false;
    }

    public function update(Order $order)
    {
        if ($order->status->value() === OrderStatus::NEED_UPDATE) {

            DB::beginTransaction();
            try {
                $order->status = OrderStatus::NEED_UPDATE;
                $order->save();
                $order->stages()->where(['status' => OrderStageStatus::NEED_UPDATE])->update(['status' => OrderStageStatus::UPDATING]);
                $fips = $order->stages()->where(['status' => OrderStageStatus::UPDATING])->get();

                foreach($fips as $fip) {
                    $this->api->cancelOrder($fip->rialto_order_id);

                    $orderId = $this->api->createBuyLimitOrder(
                        $order->ticker,
                        1,
                        $fip->take_profit_price
                    );

                    $fip->rialto_order_id = $orderId;
                    $fip->status = OrderStageStatus::ON_MARKET;

                    $fip->save();
                }

                $order->status = OrderStatus::ON_MARKET;
                $order->save();

            } catch (TIException $e) {
                print_r('Ордер не найден на бирже');
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            return true;
        } else {
            return false;
        }
    }

    public function cancel(Order $order)
    {
        if ($order->status->value() === OrderStatus::NEED_CANCEL) {

            DB::beginTransaction();
            try {
                $order->status = OrderStatus::CANCELING;
                $order->save();

                $order->stages()->where('status', '=', OrderStageStatus::NEED_CANCEL)
                    ->update(['status' => OrderStageStatus::CANCELING]);

                $fips = $order->stages()->where(['status' => OrderStageStatus::CANCELING])->get();

                foreach($fips as $fip) {
                    $this->api->cancelOrder($fip->rialto_order_id);

                    $fip->rialto_order_id = null;
                    $fip->status = OrderStageStatus::CANCELLED;
                    $fip->save();
                }

                $order->status = OrderStatus::CANCELLED;
                $order->save();

                DB::commit();
            } catch (TIException $e) {
                DB::rollBack();
                print_r('Ордер не найден на бирже');
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            return true;
        } else {
            return false;
        }

    }

    public function sendSellLimitOrder(Order $order)
    {
        try {
            $rialtoOrderId = $this->api->createSellLimitOrder(
                $order->ticker,
                1,
                $order->stop_loss_price
            );

            $order->stages()->create([
                'num' => 1,
                'proportion' => 100,
                'take_profit_price' => $order->stop_loss_price,
                'type' => OrderStageType::SELL,
                'rialto_order_id' => $rialtoOrderId,
            ]);

        } catch (\Exception $e) {
            throw $e;
        }

    }

}
