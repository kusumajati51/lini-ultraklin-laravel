<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;

use App\V1\Customer;

use App\Traits\V1\PromotionTrait;

class ClientPromotionController extends Controller
{
    use PromotionTrait;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function user()
    {
        return Customer::whereHas('client', function ($query) {
            $query->where('client', $this->request->client)
                ->where('client_id', $this->request->customerId);
        })
        ->first();
    }

    public function check()
    {
        $rules = [
            'client' => 'required|in:ADG',
            'customerId' => 'required|customer_in_client:ADG',
            'orders' => 'required|array',
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
