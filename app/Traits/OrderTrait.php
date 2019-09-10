<?php

namespace App\Traits;

use Validator;

use App\V1\Invoice;
use App\V1\Package;
use App\V1\Order;

use App\Traits\PromotionTrait;
use App\Traits\V1\TelegramTrait;

trait OrderTrait {
    use PromotionTrait, TelegramTrait;

    protected $invoice;
    protected $orderHasPromotion;

    public function user()
    {
        return $this->request->user();
    }

    public function isOfflineOrder()
    {
        return false;
    }

    public function rules()
    {
        $rules = [
            'orders' => 'required',
            'orders.*.region' => 'required|exists:regions,id',
            'orders.*.location' => 'required',
            'orders.*.items' => 'required'
        ];

        if ($this->request->has('orders')) {
            foreach ($this->request->orders as $i => $order) {
                $rules['orders.'.$i.'.package'] = 'required|package_in_region:'.$order['region'];
                $rules['orders.'.$i.'.date'] = 'required|date_format:Y-m-d H:i|can_order:'.$order['date'].'|open_at:'.$order['date'];

                foreach ($order['items'] as $j => $item) {
                    $rules['orders.'.$i.'.items.'.$j.'.id'] = 'required|item_in_package:'.$order['package'];
                    $rules['orders.'.$i.'.items.'.$j.'.quantity'] = 'required';
                }
            }
        }

        return $rules;
    }

    public function hiddenInvoiceFields()
    {
        return [];
    }

    public function store()
    {
        if ($this->request->has('orders')) {
            $orders = [];

            foreach ($this->request->orders as $order) {
                $_order = is_string($order) ? json_decode($order, true) : $order;

                if (!isset($_order['region'])) {
                    $_order['region'] = 1;
                }
                
                $orders[] = $_order;
            }

            $this->request->merge([
                'orders' => $orders
            ]);
        }

        $validator = Validator::make($this->request->all(), $this->rules());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ]);
        }

        $this->createMappingOrders();

        if ($this->request->has('promo')) {
            $promotionResult = $this->checkPromotion();
    
            if (isset($promotionResult->error)) {
                return response()->json($promotionResult);
            }

            $this->orderHasPromotion = $promotionResult->order;
        }

        $this->invoice = $this->createInvoice();

        $this->storeOrders();

        $this->sendTelegramNotif();

        $this->invoice->orders = $this->invoice->orders()->with('items')->get();

        return response()->json([
            'success' => 'Your order will be proccess.',
            'data' => collect($this->invoice)->except($this->hiddenInvoiceFields())
        ]);
    }

    public function createInvoice()
    {
        $invoice = new Invoice;
        $invoice->promotion_id = is_null($this->promotion) ? null : $this->promotion->id;
        $invoice->code = Invoice::generateCode();
        $invoice->payment = $this->request->has('payment') ? $this->request->payment : 'Cash';
        $invoice->offline = $this->isOfflineOrder() ? 1 : 0;
        $invoice->total = collect($this->request->mapping_orders)->sum('total');
        $invoice->discount = $this->getDiscount($this->orderHasPromotion);
        $invoice->created_by = $this->user()->name. ' :: '.$this->request->header('User-Agent');
        $invoice->updated_by = $this->user()->name. ' :: '.$this->request->header('User-Agent');

        if ($this->isOfflineOrder()) {
            $invoice->customer_id = $this->request->customer;
        }
        else if ($this->request->has('client')) {
            $invoice->customer_id = $this->user()->id;
        }
        else {
            $invoice->user_id = $this->user()->id;
        }

        $invoice->save();

        return $invoice;
    }

    public function storeOrders()
    {
        $code = Order::generateCode($this->isOfflineOrder());

        foreach ($this->request->mapping_orders as $order) {
            $package = Package::select('id', 'display_name')
                ->where('name', $order['package'])
                ->first();

            $_order = new Order;
            $_order->region_id = $order['region'];
            $_order->invoice_id = $this->invoice->id;
            $_order->package_id = $package->id;
            $_order->code = $code;
            $_order->date = date('Y-m-d H:i', strtotime($order['date']));
            $_order->location = $order['location'];
            $_order->detail = $order['detail'];
            $_order->note = is_null($order['note']) ? null : $order['note'];
            $_order->save();

            foreach ($order['items'] as $item) {
                $_order->items()->attach($item['id'], [
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'package' => $package->display_name,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }

    public function sendTelegramNotif()
    {
        if (env('TELEGRAM')) {
            $config = config('ultraklin');

            $user = $this->isOfflineOrder() ? $this->invoice->customer : $this->user();
            $phone = $user->phone;

            if (preg_match('/^(08|\+62)/', $phone)) {
                $phone = preg_replace('/^(08|\+628)/', '628', $phone);
            }
            else if (preg_match('/^(\+)/', $phone)) {
                $phone = preg_replace('/^(\+)/', '', $phone);
            }

            $message = '';

            $message .= '*NEW ORDER*';
            $message .= "\n\n";
            $message .= "\xF0\x9F\x93\x83  `".$this->invoice->code."`";
            $message .= "\n";
            $message .= "\xF0\x9F\x91\xA4  [".$user->name."](https://api.whatsapp.com/send?phone=".$phone.")";
            $message .= "\n\n";

            $regionOrders = collect($this->invoice->orders)
                ->groupBy(function ($order) {
                    return $order->region->name;
                });
           
            foreach ($regionOrders as $region => $orders) {
                $message .= "\n";
                $message .= "\xF0\x9F\x8F\xA0 `".strtoupper($region)."`";

                foreach ($orders as $order) {
                    $message .= "\n";
                    $message .= "- `".$order->package->display_name."`";
                }

                $message .= "\n";
            }

            $message .= "\n\n";
            $message .= $config['emoji']['wink'];

            $this->sendMessage($message);
        }
    }
}
