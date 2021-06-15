<?php

namespace App\Http\Controllers\Cabinet\Orders;

use App\Enums\OrderStageType;
use App\Events\Notifications\OrderCreated;
use App\Exceptions\Handler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cabinet\Orders\StoreRequest;
use App\Models\Order;
use App\Models\User;
use App\Services\Order\OrderService;
use Illuminate\Http\RedirectResponse;

class StoreController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param StoreRequest $request
     * @param OrderService $service
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function __invoke(StoreRequest $request, OrderService $service)
    {

        try {
            $messages = \DB::transaction(static function () use ($request, $service) {

                /** @var User $user */
                $user = $request->user();

                $fipData = collect($request->input('fips'));

                if ($fipData->isEmpty()) {
                    $fipData->add([
                        'percent' => 100,
                        'take_profit_price' => $request->input('exit_price'),
                    ]);
                }

                /** @var Order $order */
                $order = $user->orders()->create($request->validated());

                foreach ($fipData->toArray() as $key => $value) {

                    $order->stages()->create([
                        'num' => $key + 1,
                        'proportion' => (float)$value['percent'],
                        'take_profit_price' => (float)$value['take_profit_price'],
                        'type' => OrderStageType::TAKE_PROFIT,
                    ]);
                }

                event(new OrderCreated($order, $user));

                return [
                    'success' => trans('Заявка создана и готовиться к отправке на биржу.'),
                ];
            });
        } catch (\Exception $e) {
            $messages = [
                'danger' => trans('Упс! Произошла ошибка при добавление заявки.'),
            ];

            (new Handler(app()))->report($e);
        }

        return response()->redirectTo(route('cabinet.dashboard'))->with('alerts', $messages);
    }
}
