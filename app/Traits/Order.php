<?php

namespace App\Traits;

trait Order {
    use \App\Traits\Promotion;
    use \App\Traits\Setting;
    use \App\Traits\Telegram;

    public function user()
    {
        return $this->request->user();
    }

    public function isOfflineOrder()
    {
        return false;
    }

    public function newInvoice() {
        $invoice = new \App\Invoice;
        $invoice->code = \App\Invoice::generateCode();
        $invoice->payment = 'Cash';
        $invoice->offline = $this->isOfflineOrder() ? 1 : 0;
        $invoice->created_by = $this->user->name. ' :: '.$this->request->header('User-Agent');
        $invoice->updated_by = $this->user->name. ' :: '.$this->request->header('User-Agent');

        return $invoice;
    }

    public function createOrders()
    {
        $orders = json_decode(json_encode($this->request->orders));
        $orderCode = \App\Order::generateCode($this->isOfflineOrder());

        $result = [];

        foreach ($orders as $order) {
            $package = \App\Package::where('name', $order->package)->first();

            $_order = new \App\Order;
            $_order->region_id = isset($order->region) ? $order->region : 1; // default JKT
            $_order->invoice()->associate($this->request->invoice);
            $_order->package()->associate($package);
            $_order->code = $orderCode;
            $_order->date = date('Y-m-d H:i', strtotime($order->date));
            $_order->location = $order->location;
            $_order->note = array_has($order, 'note') ? $order->note : null;
            $_order->detail = $order->detail;
            $_order->created_at = date('Y-m-d H:i:s');
            $_order->updated_at = date('Y-m-d H:i:s');
            $_order->save();

            foreach ($order->items as $item) {
                $_item = \App\Item::find($item->id);

                $_package = $_item->packages()->where('name', $order->package)->first();

                $item_temp = [
                    'price' => $_package->pivot->price,
                    'quantity' => $item->quantity,
                    'package' => $_package->display_name,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $_order->items()->attach($item->id, $item_temp);
            }
        }
    }

    public function store()
    {
        $this->user = $this->user();

        $ordersArray = [];

        if ($this->request->has('orders')) {
            foreach ($this->request->orders as $order) {
                $ordersArray[] = is_array($order) ? $order : json_decode($order, true);
            }
    
            $this->request->merge([
                'orders' => $ordersArray
            ]);

            // Start # Check service is closed
            foreach ($this->request->orders as $order) {
                $result = $this->checkServiceClosed($order['date']);
        
                if (isset($result['error'])) {
                    return response()->json($result);
                }
            }
            // End # Check service is closed
        }

        $rules = [
            'orders' => 'required',
            'orders.*.package' => 'required|exists:packages,name',
            'orders.*.date' => 'required',
            'orders.*.location' => 'required',
            'orders.*.items' => 'required',
            'orders.*.detail' => 'required'
        ];

        if ($this->request->has('orders')) {
            foreach ($this->request->orders as $i => $order) {
                if (isset($order['region'])) {
                    $rules['orders.'.$i.'.region'] = 'exists:regions,id';
                }
            }
        }

        if ($this->isOfflineOrder()) {
            $rules['customer'] = 'required|exists:customers,id';
        }

        $validator = \Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ]);
        }
        
        if ($this->request->has('promo')) {
            $promotion = \App\Promotion::where('code', strtoupper($this->request->promo))->first();
    
            $result = $this->checkPromotion($promotion);
    
            if (isset($result->error)) {
                return response()->json($result);
            }
        }

        $this->request->request->add([
            'invoice' => $this->newInvoice()
        ]);

        if ($this->isOfflineOrder()) {
            $customer = \App\Customer::find($this->request->customer);

            $customer->invoices()->save($this->request->invoice);
        }
        else {
            $this->user->invoices()->save($this->request->invoice);
        }

        $this->createOrders();

        if (isset($result->data)) {
            $this->getDiscount($promotion, $result->data);
        }

        if (env('TELEGRAM')) {
            $config = config('ultraklin');

            $user = $this->isOfflineOrder() ? $this->request->invoice->customer->name : $this->user->name;

            $message = 
                '*NEW ORDER.*'
                ."\n\n_"
                ."Code : ".$this->request->invoice->code
                ."\nCustomer : ".$user
                ."_\n\n"
                .$config['emoji']['wink'];

            $this->sendMessage($message);
        }

        $invoice = \App\Invoice::where('id', $this->request->invoice->id)
            ->with('orders.items')
            ->first();

        return response()->json([
            'success' => 'Your order will be proccess.',
            'data' => $invoice
        ]);
    }

    public function getDiscount($promotion, $order)
    {
        $order = json_decode(json_encode($order));

        if ($promotion->target == null || $promotion->target == 'the-first-2-hours') {
            $discount = $promotion->value;    
        }
        else if ($promotion->target == 'item') {
            $discount = collect($order->items)->sum(function ($item) use ($promotion) {
                return $item->quantity * $promotion->value;
            });
        }
        else {
            return;
        }
        // add discount to invoice
        $invoice = $this->request->invoice;
        $invoice->promotion()->associate($promotion);
        $invoice->discount = $discount;
        $invoice->save();
    }

    public function oldCreateOrder()
    {
        $__order = $this->request->order;
        $__package = \App\Package::where('name', $__order['package'])->first();
        $__service = $__package->service;
        $__items = $__order['items'];

        if (count($__items) == 0) return null;
        
        $order = new \App\Order;
        $order->invoice()->associate($this->request->invoice);
        $order->package()->associate($__package);
        $order->code = $__order['code'];
        $order->location = $this->request->address;
        $order->note = $this->request->has('note') ? $this->request->note : null;
        $order->created_at = date('Y-m-d H:i:s');
        $order->updated_at = date('Y-m-d H:i:s');

        if ($__service->name == 'cleaning') {
            $order->date = date('Y-m-d H:i', strtotime($this->request->date.' '.$this->request->time));
            $order->detail = [
                'building_type' => $this->request->typeGedung,
                'cso_gender' => $this->request->gender,
                'pets' => $this->request->pet,
                'total_cso' => $this->request->has('total_cso') ? $this->request->total_cso : 1
            ];
        }
        else if ($__service->name == 'laundry' || $__service->name == 'laundry-pieces' || $__service->name == 'laundry-kilos') {
            $order->date = date('Y-m-d H:i', strtotime($this->request->date_pickup.' '.$this->request->time_pickup));
            $order->detail = [
                'fragrance' => $this->request->fragrance,
                'total_items' => $this->request->listKiloan
            ];
        }

        $order->save();

        $this->oldStoreItems($order, $__order['package'], $__items);

        return $order;
    }

    public function oldStoreItems($order, $package, $items)
    {
        foreach ($items as $item) {
            if (isset($item->id)) {
                $__item = \App\Item::find($item->id);

                if ($item->quantity < 1) continue;
            }
            else {
                $name = preg_replace('/\s\([^)]+\)/', '', $item->satuan_name);
    
                $__item = \App\Item::where('name', $name)->first();

                if (is_null($__item)) continue;
            }

            $__package = $__item->packages()->where('name', $package)->first();

            $item_temp = [
                'price' => $__package->pivot->price,
                'quantity' => isset($item->quantity) ? $item->quantity : $item->satuan_value,
                'package' => $__package->display_name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $order->items()->attach($__item->id, $item_temp);
        }
    }
}