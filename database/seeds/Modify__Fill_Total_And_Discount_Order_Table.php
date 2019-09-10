<?php

use Illuminate\Database\Seeder;

use App\V1\Order;

class Modify__Fill_Total_And_Discount_Order_Table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $year = 2018;
        $months = [
            5, 6, 7, 8, 9
        ];

        foreach ($months as $month) {
            $date = date('Y M', strtotime($year.'-'.$month));

            $this->command->info('************************************************************');
            $this->command->info('*                         '.$date.'                         *');
            $this->command->info('************************************************************');
            $this->command->info("ORDER CODE\t\t\tTOTAL\t\tDISCOUNT");
            $this->command->info('------------------------------------------------------------');

            $orders = Order::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->get();

            foreach ($orders as $order) {
                $order->total = $order->items->sum(function ($item) {
                    return $item->pivot->price * $item->pivot->quantity;
                });

                $invoice = $order->invoice;
                $promotion = $invoice->promotion;

                if (!is_null($promotion)) {
                    $promotionPackages = $promotion->packages->pluck('id')->toArray();
    
                    $cadidateOrdersWithPromotion = $invoice->orders->filter(function ($order) use ($promotionPackages) {
                        return in_array($order->package_id, $promotionPackages);
                    })
                    ->map(function ($order) {
                        $order->total = $order->items->sum(function ($item) {
                            return $item->pivot->price * $item->pivot->quantity;
                        });
    
                        return $order;
                    });
    
                    $orderWithPromotion = $cadidateOrdersWithPromotion->where('total', $cadidateOrdersWithPromotion->max('total'))
                        ->first();
    
                    if (!is_null($orderWithPromotion) && $orderWithPromotion->id == $order->id) {
                        $order->discount = $invoice->discount;
                    }
                }

                $order->save();

                $this->command->info($order->code."\t\t".$order->total."\t\t".$order->discount);
            }
        }
    }
}
