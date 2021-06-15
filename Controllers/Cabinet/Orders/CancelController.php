<?php

namespace App\Http\Controllers\Cabinet\Orders;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class CancelController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function __invoke(Request $request)
    {
        if ($user = \Auth::user()){
            $model = Order::where('id', '=', $request->route('order'))
                ->where('user_id', '=', $user->id)
                ->first();

            if ($model) {
                $model->status = OrderStatus::NEED_CANCEL;
                $result = $model->save();
                if ($request->ajax()) {
                    return response()->json(['success' => $result], 200);
                }

                return redirect()->route('cabinet.dashboard');
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => __('Доступ запрещен')], 200);
        }

        abort(403, __('Доступ запрещен'));
    }
}
