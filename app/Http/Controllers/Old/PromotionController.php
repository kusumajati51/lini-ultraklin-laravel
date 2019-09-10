<?php

namespace App\Http\Controllers\Old;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\Package;
use App\Promotion;

class PromotionController extends Controller
{
    use \App\Traits\Promotion;

    protected $request;
    protected $user;
    
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getPromo()
    {
        $promo = $this->oldCheckPromotion();

        if (isset($promo['error'])) {
            return response()->json($promo);
        }

        if ($this->request->name == 'Cleaning Service' || ($this->request->has('amount_bath') && $this->request->has('amount_bed') && $this->request->has('amount_other'))) {
            if (is_null($promo->target) || $promo->target == 'the-first-2-hours') {
                $totalCSO = $this->request->has('total_cso') && $this->request->total_cso > 0 ? $this->request->total_cso : 1;
                $totalPrice = $this->request->total * $totalCSO;

                return response()->json([
                    'promo' => $promo->code,
					'discount' => $promo->value,
					'sub Total' => $totalPrice,
                    'Total Payment' => $totalPrice - $promo->value
                ]);
            }
        }

        else if ($this->request->name == 'Laundry Kilos' || $this->request->name == 'Laundry Pieces & Kilos') {
            if ($promo->target == 'item') {
                $package = Package::where('name', 'laundry-kilos-regular')->first();

                $item = $package->items->first();

                $total = $this->request->estimateWeight * $item->pivot->price;
                $discount = $this->request->estimateWeight * $promo->value;

                return response()->json([
                    'promo' => $promo->code,
					'discount' => $discount,
					'sub Total' => $this->request->total,
                    'Total Payment' => $total - $discount
                ]);
            }
        }
        
        return response()->json([
            'error' => 'Promo can\'t applied.'
        ]);
    }
}
