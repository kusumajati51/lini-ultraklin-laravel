<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;

use App\Traits\PromotionTrait;

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
                'error' => $validator->errors()
            ]);
        }

        $this->createMappingOrders();

        $promotionResult = $this->checkPromotion();

        if (isset($promotionResult->success)) {
            $promotionResult->discount = $this->getDiscount($promotionResult->order);
        }

        return response()->json($promotionResult);
    }

    public function __check()
    {
        $promo = Promotion::where('code', strtoupper($this->request->promo))
            ->where('active', true)
            ->first();

        if (is_null($promo)) {
            return response()->json([
                'error' => 'Promo not found.'
            ]);
        }

        $ordersArray = [];

        if ($this->request->has('orders')) {
            foreach ($this->request->orders as $order) {
                $ordersArray[] = json_decode($order, true);
            }
    
            $this->request->merge([
                'orders' => $ordersArray
            ]);
        }

        $result = $this->checkPromotion($promo);

        if (isset($result->error)) {
            return response()->json($result);
        }

        if ($promo->target == null || $promo->target == 'the-first-2-hours') {
            $discount = $promo->value;    
        }
        else if ($promo->target == 'item') {
            $discount = collect($result->data['items'])->sum(function ($item) use ($promo) {
                return $item->quantity * $promo->value;
            });
        }

        return response()->json([
            'success' => 'Promo applied.',
            'discount' => $discount,
            'order_with_promo' => $result->data
        ]);
    }
}
