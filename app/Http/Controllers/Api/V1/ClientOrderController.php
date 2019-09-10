<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;

use App\V1\Customer;

use App\Traits\V1\OrderTrait;

class ClientOrderController extends Controller
{
    use OrderTrait;

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

    public function rules()
    {
        $rules = [
            'client' => 'required|in:ADG',
            'customerId' => 'required|customer_in_client:ADG',
            'orders' => 'required|array',
            'orders.*.regionId' => 'required|exists:regions,id',
            'orders.*.package' => 'required|exists:packages,name',
            'orders.*.date' => 'required|date_format:Y-m-d H:i',
            'orders.*.location' => 'required',
            'orders.*.items' => 'required|array',
            'orders.*.items.*.quantity' => 'required|numeric'
        ];

        if ($this->request->has('orders')) {
            foreach ($this->request->orders as $i => $order) {
                $rules['orders.'.$i.'.package'] = 'required|package_in_region:'.$order['regionId'];
                $rules['orders.'.$i.'.date'] = 'required|date_format:Y-m-d H:i|can_order:'.$order['date'].'|open_at:'.$order['date'];

                foreach ($order['items'] as $j => $item) {
                    $rules['orders.'.$i.'.items.'.$j.'.id'] = 'required|item_in_package:'.$order['package'];
                    $rules['orders.'.$i.'.items.'.$j.'.quantity'] = 'required';
                }
            }
        }

        return $rules;
    }

    public function  hiddenInvoiceFields()
    {
        return ['id', 'customer_id', 'created_by', 'updated_by'];
    }
}
