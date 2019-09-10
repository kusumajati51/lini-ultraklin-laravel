<?php

namespace App\Http\Controllers\Old;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Validator;

use App\User;
use App\Order;

class OrderController extends Controller
{
    use \App\Traits\Order;

    protected $promo;
    protected $request;
    protected $user;
    
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index($token)
    {
        $this->user = \App\User::where('token', $token)->first();

        if (is_null($this->user)) {
            return response()->json([
                'error' => 'Unauthenticated.'
            ]);
        }

        $orders = Order::whereHas('invoice', function ($query) {
            return $query->where('user_id', $this->user->id);
        })
        ->get();

        $orders = $orders->map(function ($order) {
            $item = [
                'produk' => strtoupper($order->package->service->display_name),
                'status' => $order->status,
                'date' => $order->date,
                'total_price' => 'Rp. '.number_format($order->total_price, 0, ',', '.'),
                'name' => null
            ];

            return $item;
        });

        return response()->json([
            'data' => $orders
        ]);
    }

    public function storeOrder()
    {
        if ($this->request->has('promo')) {
            $this->promo = $this->oldCheckPromotion();
    
            if (isset($this->promo['error'])) {
                return response()->json($this->promo);
            }
        }

        $this->user = User::where('token', $this->request->apiKey)->first();

        // Check service is closed
        if ($this->request->name == 'Cleaning Service') {
            $dateForCheck = Carbon::createFromFormat('Y-m-d H:i', date('Y-m-d H:i', strtotime($this->request->date.' '.$this->request->time)));
        }
        else if ($this->request->name == 'Laundry Pieces' || $this->request->name == 'Laundry Kilos' || $this->request->name == 'Laundry Pieces & Kilos' || $this->request->name == 'Laundry PiecesKilos') {
            $dateForCheck = Carbon::createFromFormat('Y-m-d H:i', date('Y-m-d H:i', strtotime($this->request->date_pickup.' '.$this->request->time_pickup)));
        }

        $result = $this->checkServiceClosed($dateForCheck);

        if (isset($result['error'])) {
            return response()->json($result);
        }

        switch ($this->request->name) {
            case 'Cleaning Service':
                return $this->storeCleaning();
                break;
            case 'Laundry Pieces':
                return $this->storeLaundryPieces();
                break;

            case 'Laundry Kilos':
                return $this->storeLaundryKilos();
                break;
            case 'Laundry Pieces & Kilos':
                return $this->storeLaundryPiecesKilos();
				break;
			case 'Laundry PiecesKilos':
				return $this->storeLaundryPiecesKilos();
				break;
            default:
                return response()->json([
                    'error' => 'Order failed.'
                ]);
                break;
        }
    }

    public function storeCleaning()
    {
        $rules = [
            'name' => '',
            'date' => 'required',
            'time' => 'required',
            'promo' => '',
            'note' => '',
            'address' => 'required',
            'typeGedung' => 'required',
            'gender' => 'required',
            'pet' => 'required',
            'amount_bath' => 'required',
            'amount_bed' => 'required',
            'amount_other' => 'required',
            'total' => ''
        ];

        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ]);
        }

        $this->request->request->add([
            'invoice' => $this->newInvoice(),
            'order' => [
                'code' => Order::generateCode(),
                'package' => 'cleaning-regular',
                'items' => json_decode(json_encode([
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
                ]))
            ]
        ]);
                
        $this->user->invoices()->save($this->request->invoice);

        $order = $this->oldCreateOrder();

        // get discount by code
        if ($this->request->has('promo')) {
            if (is_null($this->promo->target) || $this->promo->target == 'the-first-2-hours') {
                // add discount to invoice
                $invoice = $this->request->invoice;
                $invoice->promotion()->associate($this->promo);
                $invoice->discount = $this->promo->value;
                $invoice->save();
            }
        }

        $this->sendNotification();

        return response()->json([
            'success' => 'Your order will be process.',
            'idOrder' => $this->request->invoice->code
        ]);
    }

    public function storeLaundryPieces()
    {
        $rules = [
            'name' => '',
            'date_pickup' => 'required',
            'time_pickup' => 'required',
            'address' => 'required',
            'services' => '',
            'fragrance' => 'required',
            'listSatuan' => 'required',
            'promo' => '',
        ];

        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ]);
        }
        
        $this->request->request->add([
            'invoice' => $this->newInvoice(),
            'order' => [
                'code' => Order::generateCode(),
                'package' => 'laundry-pieces-regular',
                'items' => json_decode($this->request->listSatuan)
            ]
        ]);
                
        $this->user->invoices()->save($this->request->invoice);

        $order = $this->oldCreateOrder();

        $this->sendNotification();

        return response()->json([
            'success' => 'Your order will be process.',
            'idOrder' => $this->request->invoice->code
        ]);
    }

    public function storeLaundryKilos()
    {
        $rules = [
            'name' => '',
            'date_pickup' => 'required',
            'time_pickup' => 'required',
            'address' => 'required',
            'services' => '',
            'fragrance' => 'required',
            'estimateWeight' => 'required',
            'listKiloan' => 'required',
            'promo' => '',
        ];

        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ]);
        }

        $this->request->request->add([
            'invoice' => $this->newInvoice(),
            'order' => [
                'code' => Order::generateCode(),
                'package' => 'laundry-kilos-regular',
                'items' => json_decode(json_encode([
                    [
                        'satuan_name' => 'Per 1 Kg',
                        'satuan_value' => $this->request->estimateWeight
                    ]
                ]))
            ]
        ]);
                
        $this->user->invoices()->save($this->request->invoice);

        $order = $this->oldCreateOrder();

        // get discount by code
        if ($this->request->has('promo')) {
            if ($this->promo->target == 'item') {

                $discount = $order->items->sum(function ($item) {
                    return $item->pivot->quantity * $this->promo->value;
                });

                // add discount to invoice
                $invoice = $this->request->invoice;
                $invoice->promotion()->associate($this->promo);
                $invoice->discount = $discount;
                $invoice->save();
            }
        }

        $this->sendNotification();

        return response()->json([
            'success' => 'Your order will be process.',
            'idOrder' => $this->request->invoice->code
        ]);
    }

    public function storeLaundryPiecesKilos()
    {
        $rules = [
            'name' => '',
            'date_pickup' => 'required',
            'time_pickup' => 'required',
            'address' => 'required',
            'services' => '',
            'fragrance' => 'required',
            'listSatuan' => 'required',
            'estimateWeight' => 'required',
            'listKiloan' => 'required',
            'promo' => '',
        ];

        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ]);
        }

        $this->request->request->add([
            'invoice' => $this->newInvoice(),
        ]);

        $this->user->invoices()->save($this->request->invoice);

        $code = Order::generateCode();

        $this->request->request->add([
            'order' => [
                'code' => $code,
                'package' => 'laundry-pieces-regular',
                'items' => json_decode($this->request->listSatuan)
            ]
        ]);

        $order = $this->oldCreateOrder();

        $this->request->request->add([
            'order' => [
                'code' => $code,
                'package' => 'laundry-kilos-regular',
                'items' => json_decode(json_encode([
                    [
                        'satuan_name' => 'Per 1 Kg',
                        'satuan_value' => $this->request->estimateWeight
                    ]
                ]))
            ]
        ]);

        $order = $this->oldCreateOrder();

        // get discount by code
        if ($this->request->has('promo')) {
            if ($this->promo->target == 'item') {

                $discount = $order->items->sum(function ($item) {
                    return $item->pivot->quantity * $this->promo->value;
                });

                // add discount to invoice
                $invoice = $this->request->invoice;
                $invoice->promotion()->associate($this->promo);
                $invoice->discount = $discount;
                $invoice->save();
            }
        }

        $this->sendNotification();

        return response()->json([
            'success' => 'Your order will be process.',
            'idOrder' => $this->request->invoice->code
        ]);
    }

    public function sendNotification()
    {
        if (env('TELEGRAM')) {
            $config = config('ultraklin');

            $message = 
                '*NEW ORDER.*'
                ."\n\n_"
                ."Code : ".$this->request->invoice->code
                ."\nCustomer : ".$this->user->name
                ."_\n\n"
                .$config['emoji']['wink'];

            $this->sendMessage($message);
        }
    }

    public function getByInvoice() {
        $this->user = User::where('token', $this->request->token)->first();

        $invoice = $this->user->invoices()
            ->where('code', $this->request->idOrder)
            ->first();

        if (is_null($invoice)) {
            return response()->json([
                'error' => 'Data not found.'
            ]);
        }

        if ($invoice->orders->count() == 1) {
            $order = $invoice->orders()->first();
    
            if ($order->package->service->name == 'cleaning') {
                $data = [
                    'id' => $order->id,
                    'status' => $order->status,
                    'date' => $order->date,
                    'produk' => strtoupper($order->package->service->name),
                    'crew' => null,
                    'name' => null,
					'detail' => [
						$this->getCleaningDetail($order)
					]
                ];
                
            }
            else if ($order->package->service->name == 'laundry-pieces') {
                $data = [
                    'id' => $order->id,
                    'status' => $order->status,
                    'date' => $order->date,
                    'produk' => strtoupper($order->package->service->name),
                    'crew' => null,
                    'name' => null,
					'detail' => [
						$this->getLaundryPiecesDetail($order)
					]
                ];
            }
            else if ($order->package->service->name == 'laundry-kilos') {
                $data = [
                    'id' => $order->id,
                    'status' => $order->status,
                    'date' => $order->date,
                    'produk' => strtoupper($order->package->service->name),
                    'crew' => null,
                    'name' => null,
					'detail' => [
						$this->getLaundryKilosDetail($order)
					]
                ];
            }
            else {
                $data = [];
            }
        }
        else if ($invoice->orders->count() > 1) {
            $orders = $invoice->orders;

            $detail = [];

            foreach ($orders as $order) {
                if ($order->package->service->name == 'cleaning') {
                    $detail[] = $this->getCleaningDetail($order);
                }
                else if ($order->package->service->name == 'laundry-pieces') {
                    $detail[] = $this->getLaundryPiecesDetail($order);
                }
                else if ($order->package->service->name == 'laundry-kilos') {
                    $detail[] = $this->getLaundryKilosDetail($order);
                }
            }

            $data = [
                'id' => $orders[0]->id,
                'status' => $orders[0]->status,
                'date' => $orders[0]->date,
                'produk' => strtoupper('Multi Service'),
                'crew' => null,
                'name' => null,
                'detail' => $detail
            ];
        }
        else {
            $data = [];
        }

        return response()->json([
			'data' => [
				$data
			]
        ]);
    }

    public function getCleaningDetail($order)
    {
        $data = [];

        $subDetail['data'] = collect($order->items)->map(function ($item) {
            $data = [
                'id_service' => $item->id,
                'sequence' => $item->pivot->quantity
            ];

            return $data;
        });

        $detail = [
            'id' => $order->id,
            'id_order' => null,
            'id_cso' => null,
            'total_cso' => 1,
            'product' => $order->package->service->display_name,
            'total_duration' => $order->items->count() * 0.5,
            'total_price' => $order->total_price,
            'note' => $order->note,
            'gender' => $order->detail->cso_gender,
            'pets' => $order->detail->pets,
            'date' => date('Y-m-d', strtotime($order->date)),
            'time' => date('H:s', strtotime($order->date)),
            'payment' => $order->invoice->payment,
            'building' => $order->detail->building_type,
            'location' => $order->location,
            'paid_date' => '',
            'created_by' => $order->created_by,
            'updated_by' => $order->updated_by,
            'status' => $order->status,
            'detail' => $subDetail,
            'created_date' => $order->created_at->toDateTimeString(),
            'updated_date' => $order->updated_at->toDateTimeString(),
        ];

        return $detail;
    }

    public function getLaundryPiecesDetail($order)
    {
        $data = [];

        $subDetail['data'] = collect($order->items)->map(function ($item) {
            $data = [
                'satuan_value' => $item->pivot->quantity,
                'satuan_name' => $item->name,
                'satuan_price' => $item->pivot->price
            ];

            return $data;
        });

        $detail = [
            'id' => $order->id,
            'id_order' => null,
            'total_price' => $order->total_price,
            'jenis' => '',
            'service' => $order->package->display_name,
            'package' => '',
            'fragrance' => $order->detail->fragrance,
            'shipping' => '',
            'address' => $order->location,
            'pick_up_date' => date('Y-m-d', strtotime($order->date)),
            'pick_up_time' => date('H:i', strtotime($order->date)), 
            'delivery_date' => '',
            'shipper' => '',
            'estimateWeight' => $order->items()->first()->pivot->quantity,
            'amount' => '',
            'detail' => $subDetail,
            'status' => $order->status,
            'created_by' => $order->invoice->user->name,
            'updated_by' => $order->invoice->user->name,
            'created_date' => $order->created_at->toDateTimeString(),
            'updated_date' => $order->created_at->toDateTimeString()
        ];

        return $detail;
    }

    public function getLaundryKilosDetail($order)
    {
        $data = [];

        $subDetail['data'] = collect($order->items)->map(function ($item) {
            $data = [
                'satuan_value' => $item->pivot->quantity,
                'satuan_name' => $item->name,
                'satuan_price' => $item->pivot->price
            ];

            return $data;
        });

        $detail = [
            'id' => $order->id,
            'id_order' => null,
            'total_price' => $order->total_price,
            'jenis' => '',
            'service' => $order->package->display_name,
            'package' => '',
            'fragrance' => $order->detail->fragrance,
            'shipping' => '',
            'address' => $order->location,
            'pick_up_date' => date('Y-m-d', strtotime($order->date)),
            'pick_up_time' => date('H:i', strtotime($order->date)), 
            'delivery_date' => '',
            'shipper' => '',
            'estimateWeight' => $order->items()->first()->pivot->quantity,
            'amount' => '',
            'detail' => $subDetail,
            'status' => $order->status,
            'created_by' => $order->invoice->user->name,
            'updated_by' => $order->invoice->user->name,
            'created_date' => $order->created_at->toDateTimeString(),
            'updated_date' => $order->created_at->toDateTimeString()
        ];

        return $detail;
    }
}
