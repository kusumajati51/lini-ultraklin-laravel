<?php

namespace App\Traits\V1;

use Carbon\Carbon;

use App\V1\Package;
use App\V1\Promotion;
use App\V1\UserPromotionHistory;

trait PromotionTrait {
    protected $promotion;
    protected $lastDigit;

    public function user()
    {
        return $this->request->user();
    }

    public function createMappingOrders()
    {
        $mappingOrders = [];

        foreach ($this->request->orders as $index => $order) {
            if (isset($order['regionId'])) {
                $order['region'] = $order['regionId'];
            }

            $order['items'] = collect($order['items'])->map(function ($item) use ($order) {
                $_item = Package::where('name', $order['package'])
                    ->first()
                    ->items()
                    ->find($item['id']);

                $item['name'] = $_item->name;
                $item['price'] = $_item->pivot->price;
                $item['sub_total'] = $item['price'] * $item['quantity'];

                return $item;
            });

            $order['key'] = bcrypt($index);
            $order['total'] = collect($order['items'])->sum('sub_total');
            $order['discount'] = 0;

            $mappingOrders[] = $order;
        }

        $this->request->merge([
            'mapping_orders' => $mappingOrders
        ]);
    }

    public function checkPromotion()
    {
        // @start check promotion exists by code and exists package
        $this->promotion = Promotion::whereHas('packages', function ($packages) {
            $packages->whereIn('name', collect($this->request->orders)->pluck('package'));
        })
        ->where('active', true)
        ->where('code', strtoupper($this->request->promo))
        ->first();

        if (is_null($this->promotion)) {
            return (object) [
                'error' => 1,
                'message' => 'Invalid promotion code.'
            ];
        }
        // @end check promotion exists by code and exists package

        // @start check is new user
        if ($this->isNewUserPromotion() && !$this->isNewUser()) {
            return (object) [
                'error' => 1,
                'message' => 'This promotion just for new user.'
            ];
        }
        // @end check is new user

        // @start check promotion has been used
        if ($this->promotionHasBeenUsed()) {
            return (object) [
                'error' => 1,
                'message' => 'Promotion has been used.'
            ];
        }
        // @end check promotion has been used

        if ($this->isOrderHas()) {
            return (object) [
                    'error' => 1,
                    'remaining' => $this->lastDigit < 4 ? 4 - $this->lastDigit : 9 - $this->lastDigit,
                    'message' => 'Promo invalid'
                ];
        }

        // @start get candidate order for get discount / promotion
        $ordersFiltered = collect($this->request->mapping_orders)
            ->filter(function ($order) {
                return in_array($order['package'], $this->promotion->packages->pluck('name')->toArray());
            })
            ->filter(function ($order) {
                $promotionResult = $this->checkPromotionTime($order);

                return isset($promotionResult->success);
            });
        // @end get candidate order for get discount / promotion

        if (count($ordersFiltered) < 1) {
            return (object) [
                'error' => 1,
                'message' => 'Promo can\'t applied.'
            ];
        }

        $orderWithPromotion = $ordersFiltered->where('total', $ordersFiltered->max('total'))
            ->first();
        
        // set discount
        $orderWithPromotion['discount'] = $this->getDiscount($orderWithPromotion);

        // @start check minimum order
        if ($orderWithPromotion['total'] < $this->promotion->min_order) {
            return (object) [
                'error' => 1,
                'message' => 'You not have minimum order.',
                'order' => $orderWithPromotion
            ];
        }
        // @end check minimum order

        return (object) [
            'success' => 1,
            'message' => 'Promotion code valid.',
            'order' => $orderWithPromotion
        ];
    }

    public function checkPromotionTime($order)
    {
        $promo = $this->promotion;
        $orderDate = Carbon::parse($order['date']);

        if (($promo->daily && is_null($promo->day)) || ($promo->daily && in_array($orderDate->format('D'), $promo->day))) {
            $date = $orderDate;

            $start = Carbon::parse($date->toDateString().' '.$promo->time[0]);
            $end = Carbon::parse($date->toDateString().' '.$promo->time[1]);

            $startTimeRemaing = $date->diffInMinutes($start, false);
            $endTimeRemaing = $date->diffInMinutes($end, false);

            if ($startTimeRemaing > 0 || $endTimeRemaing < 0) {
                return (object) [
                    'error' => 1,
                    'message' => 'Promo can\'t applied. [1]',
                    'data' => $order
                ];
            }
        }
        else if ($promo->daily && !in_array($orderDate->format('D'), $promo->day)) {
            return (object) [
                'error' => 1,
                'message' => 'Promo can\'t applied. [2]',
                'order' => $order
            ];
        }
        else if (!$promo->daily) {
            $date = $orderDate;
            
            $start = Carbon::parse($promo->time[0]);
            $end = Carbon::parse($promo->time[1]);

            $startTimeRemaing = $date->diffInDays($start, false);
            $endTimeRemaing = $date->diffInDays($end, false);

            if ($startTimeRemaing == 0 && $endTimeRemaing == 0) {
                $start = $start->setTimeFromTimeString('00:00');
                $end = $end->setTimeFromTimeString('23:59');

                $startTimeRemaing = $date->diffInMinutes($start, false);
                $endTimeRemaing = $date->diffInMinutes($end, false);
            }

            if ($startTimeRemaing > 0 || $endTimeRemaing < 0) {
                return (object) [
                    'error' => 1,
                    'message' => 'Promo can\'t applied. [3]',
                    'order' => $order
                ];
            }
        }

        return (object) [
            'success' => 1,
            'message' => 'Promotion code valid.',
            'order' => $order
        ];
    }

    public function isOrderHas()
    {
        $promotionTagets = collect(config('ultraklin.promotion_targets'));

        $target = $promotionTagets->where('name', $this->promotion->target)->first();

        if ($target['category'] != 'order-count') return false;
        
        $invoices = $this->user()->invoices;
        
        $this->lastDigit = (int)substr((String)$invoices->count(), -1);

        if ($this->lastDigit == 4 || $this->lastDigit == 9) return false;

        return true;
    }

    public function isNewUserPromotion()
    {
        $promotionTagets = collect(config('ultraklin.promotion_targets'));

        $target = $promotionTagets->where('name', $this->promotion->target)->first();

        if ($target['category'] != 'new-user') return false;

        return true;
    }

    public function isNewUser()
    {
        $userRegisteredAt = Carbon::parse($this->user()->created_at);
        $promotionCreatedAt = Carbon::parse($this->promotion->created_at);

        if ($userRegisteredAt->diffInMinutes($promotionCreatedAt, false) > 0) return false;

        return true;
    }

    public function promotionHasBeenUsed()
    {
        $promotionTagets = collect(config('ultraklin.promotion_targets'));

        $target = $promotionTagets->where('name', $this->promotion->target)->first();

        if ($target['category'] != 'new-user') return false;

        $histories = $this->user()->promotionHistories()
            ->where('code', $this->promotion->code)
            ->get();

        if (count($histories) == 0) return false;

        return true;
    }

    public function getDiscount($order = null)
    {
        $discount = 0;

        if (is_null($this->promotion) || is_null($order)) {
            $discount = 0;
        }
        else if (is_null($this->promotion->target) || ($this->promotion->target == 'total-prices' || $this->promotion->target == 'new-user__total-prices') || ($this->promotion->target == 'the-first-2-hours' || $this->promotion->target == 'new-user__the-first-2-hours')) {
            $discount = $this->promotion->value;    
        }
        else if ($this->promotion->target == 'item' || $this->promotion->target == 'new-user__item') {
            $discount = collect($order['items'])->map(function ($item) {
                return $item['quantity'] * $this->promotion->value;
            })->sum();
        }
        else if (is_null($this->promotion->target) || ($this->promotion->target == 'fifth-order' || $this->promotion->target == 'new-user__total-prices') || ($this->promotion->target == 'the-first-2-hours' || $this->promotion->target == 'new-user__the-first-2-hours')) {
            $discount = $this->promotion->value;
        }

        return $discount;
    }
}