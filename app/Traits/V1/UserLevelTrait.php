<?php

namespace App\Traits\V1;

use DB;
use Validator;

use App\User;
use App\V1\Order;

use App\Utils\LogUtil;
use App\Utils\TimeUtil;

trait UserLevelTrait {
    public function setDefaultFilter()
    {
        $this->filter = [
            'sort' => ['id', 'desc'],
            'search' => ''
        ];

        if ($this->request->has('sort')) {
            $this->filter['sort'] = $this->request->sort;
        }

        if ($this->request->has('search')) {
            $this->filter['search'] = $this->request->search;
        }
    }

    public function getUsersWithPagination()
    {
        $this->setDefaultFilter();

        $users = User::byRegion()
            ->where('status', $this->levelType())    
            ->where(function ($query) {
                $query->where('name', 'like', '%'.$this->filter['search'].'%')
                    ->orWhere('email', 'like', '%'.$this->filter['search'].'%')
                    ->orWhere('phone', 'like', '%'.$this->filter['search'].'%');
            })
            ->orderBy($this->filter['sort'][0], $this->filter['sort'][1])
            ->paginate(24);

        $users->map(function ($user) {
            $user->level = $user->levels()
                ->where('user_level.active', true)
                ->orderBy('user_level.created_at', 'desc')
                ->first();

            return $user;
        });

        $users->appends($this->filter);

        return $users;
    }

    public function jsonIndex()
    {
        $users = $this->getUsersWithPagination()->toArray();

        $users['filter'] = $this->filter;

        return response()->json($users);
    }

    public function jsonShow($code)
    {
        $user = User::where('id', $code)
            ->orWhere('code', $code)
            ->first();

        if (is_null($user)) {
            return response()->json([
                'error' => 1,
                'message' => 'Data not found.'
            ]);
        }

        return response()->json($user);
    }

    public function jsonStore()
    {
        $rules = [
            'id' => 'required|exists:users',
            'level' => 'required|exists:levels,id'
        ];

        $messages = [
            'id.required' => 'No user selected.'
        ];

        $validator = Validator::make($this->request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'error_validation' => 1,
                'message' => 'Invalid data.',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $user = User::find($this->request->id);
            $user->code = $user->generateAgentCode();
            $user->status = $this->levelType();
            $user->save();

            $user->levels()->attach($this->request->level, [
                'created_by' => auth('officer')->user()->name.' :: '.$this->request->header('User-Agent'),
                'updated_by' => auth('officer')->user()->name.' :: '.$this->request->header('User-Agent'),
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ]);

            DB::commit();

            return response()->json([
                'success' => 1,
                'message' => 'New '.ucwords($this->levelType()).' save.'
            ]);
        } catch(\Exception $e) {
            DB::rollback();

            $log = new LogUtil('error');
        
            $log->createWithException($this->request, $e);

            return response()->json([
                'error' => 1,
                'message' => 'Oops! Something went wrong.'
            ], 500);
        }
    }

    public function jsonEdit($id)
    {
        $user = User::find($id);

        $user['level'] = $user->levels()
            ->where('user_level.active', true)
            ->orderBy('user_level.created_at', 'desc')
            ->first();

        if (is_null($user)) {
            return response()->json([
                'error' => 1,
                'message' => 'Data not found.'
            ], 404);
        }

        return response()->json($user);
    }

    public function jsonUpdate($id)
    {
        $rules = [
            'level' => 'required|exists:levels,id'
        ];

        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error_validation' => 1,
                'message' => 'Invalid data.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('status', $this->levelType())
            ->find($id);

        if (is_null($user)) {
            return response()->json([
                'error' => 1,
                'message' => 'Data not found.'
            ], 404);
        }

        DB::beginTransaction();

        try {
            foreach ($user->levels()->allRelatedIds() as $service) {
                $user->levels()->updateExistingPivot($service, [
                    'active' => false
                ]);
            }

            $user->levels()->attach($this->request->level, [
                'created_by' => auth('officer')->user()->name.' :: '.$this->request->header('User-Agent'),
                'updated_by' => auth('officer')->user()->name.' :: '.$this->request->header('User-Agent'),
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ]);

            DB::commit();

            return response()->json([
                'success' => 1,
                'message' => ucwords($this->levelType()).' updated.'
            ]);
        } catch(\Exception $e) {
            DB::rollback();

            $log = new LogUtil('error');
        
            $log->createWithException($this->request, $e);

            return response()->json([
                'error' => 1,
                'message' => 'Oops! Something went wrong.',
                'trace' => $e->getTrace()
            ], 500);
        }
    }

    public function setFilter($keys)
    {
        $time = new TimeUtil;
        $timeRange = $time->createTimeRange(date('Y-m-d'), date('Y-m-d'));

        $filter = [
            'limit' => 25,
            'date' => [
                $timeRange->start,
                $timeRange->end
            ],
            'status' => [
                'Cancel',
                'Pending',
                'Confirm',
                'On The Way',
                'Done'
            ],
            'userStatus' => [
                'user'
            ],
            'region' => auth('officer')->user()->regions->pluck('code'),
            'search' => ''
        ];

        if ($this->request->has('date') && !is_null($this->request->date)) {
            $timeRange = $time->createTimeRange($this->request->date[0], $this->request->date[1]);

            $this->request->merge([
                'date' => [
                    $timeRange->start,
                    $timeRange->end
                ]
            ]);
        }

        foreach ($keys as $key) {
            if (isset($filter[$key])) {
                if ($this->request->has($key) && !is_null($this->request[$key])) {
                    $filter[$key] = $this->request[$key];
                }

                $this->filter[$key] = $filter[$key];
            }
        }
    }

    public function jsonGetDownline($code)
    {
        $this->setFilter([
            'limit'
        ]);

        $users = User::where('referral', $code)
            ->orderBy('id', 'desc')
            ->paginate($this->filter['limit']);

        return response()->json($users);
    }

    public function jsonGetDownlineOrders($code)
    {
        $this->setFilter([
            'limit',
            'date',
            'status',
            'userStatus',
            'region',
            'search'
        ]);

        $this->filter['code'] = $code;

        $orders = Order::select(
            'orders.*',
            'packages.display_name as package_display_name',
            'regions.code as region_code', 'regions.name as region_name',
            'users.name as user_name', 'users.phone as user_phone', 'users.status as user_status'
        )
        ->leftJoin('packages', 'packages.id', 'orders.package_id')
        ->leftJoin('invoices', 'invoices.id', 'orders.invoice_id')
        ->leftJoin('regions', 'regions.id', 'orders.region_id')
        ->leftJoin('users', 'users.id', 'invoices.user_id')
        ->where(function ($query) {
            $query->whereHas('invoice', function ($invoice) {
                $invoice->whereHas('user', function ($user) {
                    $user->where('referral', $this->filter['code']);
                });
            });
        })
        ->where(function ($query) {
            $query->whereBetween('orders.created_at', $this->filter['date'])
                ->whereIn('orders.status', $this->filter['status'])
                ->whereIn('regions.code', $this->filter['region'])
                ->whereIn('users.status', $this->filter['userStatus']);
        })
        ->orderBy('orders.created_at', 'desc')
        ->paginate($this->filter['limit']);

        $orders = $orders->toArray();
        $orders['filter'] = $this->filter;

        return response()->json($orders);
    }
}
