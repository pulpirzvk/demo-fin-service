<?php

namespace App\Http\Controllers\Cabinet\Orders;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UpdateController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        if ($user = \Auth::user()){
            $order = Order::where('id', '=', $request->route('order'))
                ->where('user_id', '=', $user->id)
                ->first();

            if ($order) {
                $validation = Validator::make($request->all(), ['stop_loss_price' => 'numeric']);
                if (!$validation->fails()) {
                    if ($order->update($validation->getData())){
                        return response()->json(['success' => true], 200);
                    } else $error = __('При сохранении данных произошла ошибка');
                } else {
                    $error = $validation->errors()->first();
                }
            } else $error = __('Заявка не найдена');
        } else $error = __('Доступ запрещен');

        return response()->json(['success' => false, 'message' => $error], 200);
    }
}
