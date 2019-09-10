<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;

use App\Traits\V1\PromotionTrait;

class PromotionController extends Controller
{
    use PromotionTrait;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function check()
    {
        $rules = [
            'promo' => 'required',
            'orders' => 'required',
            'orders.*.package' => 'required|exists:packages,name',
            'orders.*.date' => 'required|date_format:Y-m-d H:i',
            'orders.*.items' => 'required'
        ];

        if ($this->request->has('orders')) {
            $orders = [];

            foreach ($this->request->orders as $i => $order) {
                $order = is_string($order) ? json_decode($order, true) : $order;
                
                if (is_null($order)) continue;
                
                foreach ($order['items'] as $j => $item) {
                    $rules['orders.'.$i.'.items.'.$j.'.id'] = 'required|item_in_package:'.$order['package'];
                    $rules['orders.'.$i.'.items.'.$j.'.quantity'] = 'required';
                }
                
                $orders[] = $order;
            }

            $this->request->merge([
                'orders' => $orders
            ]);
        }

        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error_validation' => 1,
                'message' => 'Invalid data.',
                'data' => $validator->errors()
            ]);
        }

        $this->createMappingOrders();

        $promotionResult = $this->checkPromotion();

        if (isset($promotionResult->success)) {
            $promotionResult->discount = $this->getDiscount($promotionResult->order);
        }

        return response()->json($promotionResult);
    }
}
