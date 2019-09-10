<?php

namespace App\Traits;

use Carbon\Carbon;

trait Promotion {
    public function checkPromotion($promo)
    {
        $orders = json_decode(json_encode($this->request->orders));

        if (is_null($promo)) {
            return (object) [
                'error' => 'Promotion not found.'
            ];
        }

        // Get order by promo
        $order = collect($orders)->filter(function ($order) use ($promo) {
            return $order->package == $promo->package->name;
        });

        if ($order->count() == 0) {
            return (object) [
                'error' => 'Promo can\'t applied.'
            ];
        }
        
        // Get total price per order
        $order = $order->map(function ($order) {
            $order->total = collect($order->items)->sum(function ($item) use ($order) {
                $__item = \App\Item::find($item->id);
                $__package = $__item->packages()->where('name', $order->package)->first();

                return $__package->pivot->price * $item->quantity;
            });

            return $order;
        });

        // Get order by max total price
        $order = $order->where('total', $order->max('total'))->first();
        $orderDate = Carbon::parse($order->date);

        if ($order->total < $promo->min_order) {
            return (object) [
                'error' => 'You not have minimum order.'
            ];
        }

        else if ($promo->daily && is_null($promo->day)) {
            $date = Carbon::now();
            $date->hour = $orderDate->hour;
            $date->minute = $orderDate->minute;

            $start = Carbon::parse($promo->time[0]);
            $end = Carbon::parse($promo->time[1]);

            $startTimeRemaing = $date->diffInMinutes($start, false);
            $endTimeRemaing = $date->diffInMinutes($end, false);

            if ($startTimeRemaing > 0 || $endTimeRemaing <= 0) {
                return (object) [
                    'error' => 'Promo can\'t applied.',
                    'state' => 1
                ];
            }
        }

        else if ($promo->daily && in_array($orderDate->format('D'), $promo->day)) {
            $date = Carbon::now();
            $date->hour = $orderDate->hour;
            $date->minute = $orderDate->minute;

            $start = Carbon::parse($promo->time[0]);
            $end = Carbon::parse($promo->time[1]);

            $startTimeRemaing = $date->diffInMinutes($start, false);
            $endTimeRemaing = $date->diffInMinutes($end, false);

            if ($startTimeRemaing > 0 || $endTimeRemaing <= 0) {
                return (object) [
                    'error' => 'Promo can\'t applied.',
                    'state' => 2
                ];
            }
        }

        else if ($promo->daily && !in_array($orderDate->format('D'), $promo->day)) {
            return (object) [
                'error' => 'Promo can\'t applied.',
                'state' => 3
            ];
        }

        else if (!$promo->daily) {
            $date = $orderDate;
            
            $start = Carbon::parse($promo->time[0]);
            $end = Carbon::parse($promo->time[1]);

            $startTimeRemaing = $date->diffInDays($start, false);
            $endTimeRemaing = $date->diffInDays($end, false);

            if ($startTimeRemaing > 0 || $endTimeRemaing <= 0) {
                return (object) [
                    'error' => 'Promo can\'t applied.',
                    'state' => 4
                ];
            }
        }

        return (object) [
            'data' => collect($order)->toArray()
        ];
    }

    public function oldCheckPromotion()
    {
        if ($this->request->name == 'Cleaning Service') {
            $orderDate = Carbon::parse($this->request->date.' '.$this->request->time);

            $items = json_decode(json_encode([
                [
                    'id' => 1,
                    'quantity' => $this->request->amount_bath
                ],
                [
                    'id' => 2,
                    'quantity' => $this->request->amount_bed
                ],
                [
                    'id' => 3,
                    'quantity' => $this->request->amount_other
                ]
            ]));

            $total = collect($items)->sum(function ($item) {
                $__package = \App\Package::where('name', 'cleaning-regular')->first();
                $__item = $__package->items()->find($item->id);

                return $item->quantity * $__item->pivot->price;
            });
        }

        else if ($this->request->name == 'Laundry Kilos' || $this->request->name == 'Laundry Pieces & Kilos' || $this->request->name == 'Laundry PiecesKilos' ) {
            $orderDate = Carbon::parse($this->request->date_pickup.' '.$this->request->time_pickup);

            $config = config('ultraklin');

            $total = $this->request->estimateWeight * $config['old']['perKilo'];
        }

        else {
            return [
                'error' => 'Invalid service.'
            ];
		}

		$this->request->merge(['total' => $total]);

        $promo = \App\Promotion::where('code', strtoupper($this->request->promo))
            ->where('active', true)
            ->first();
            
        if (is_null($promo)) {
            return [
                'error' => 'Promo not found.'
            ];
        }

        else if ($total < $promo->min_order) {
            return [
                'error' => 'You not have minimum order.'
            ];
        }

        else if ($promo->daily && is_null($promo->day)) {
            $date = Carbon::now();
            $date->hour = $orderDate->hour;
            $date->minute = $orderDate->minute;

            $start = Carbon::parse($promo->time[0]);
            $end = Carbon::parse($promo->time[1]);

            $startTimeRemaing = $date->diffInMinutes($start, false);
            $endTimeRemaing = $date->diffInMinutes($end, false);

            if ($startTimeRemaing > 0 || $endTimeRemaing <= 0) {
                return [
                    'error' => 'Promo can\'t applied.',
                    'state' => 1
                ];
            }
        }

        else if ($promo->daily && in_array($orderDate->format('D'), $promo->day)) {
            $date = Carbon::now();
            $date->hour = $orderDate->hour;
            $date->minute = $orderDate->minute;

            $start = Carbon::parse($promo->time[0]);
            $end = Carbon::parse($promo->time[1]);

            $startTimeRemaing = $date->diffInMinutes($start, false);
            $endTimeRemaing = $date->diffInMinutes($end, false);

            if ($startTimeRemaing > 0 || $endTimeRemaing <= 0) {
                return [
                    'error' => 'Promo can\'t applied.',
                    'state' => 2
                ];
            }
        }

        else if ($promo->daily && !in_array($orderDate->format('D'), $promo->day)) {
            return [
                'error' => 'Promo can\'t applied.',
                'state' => 3
            ];
        }

        else if (!$promo->daily) {
            $date = $orderDate;
            
            $start = Carbon::parse($promo->time[0]);
            $end = Carbon::parse($promo->time[1]);

            $startTimeRemaing = $date->diffInDays($start, false);
            $endTimeRemaing = $date->diffInDays($end, false);

            if ($startTimeRemaing > 0 || $endTimeRemaing <= 0) {
                return [
                    'error' => 'Promo can\'t applied.',
                    'state' => 4
                ];
            }
        }

        return $promo;
    }
}