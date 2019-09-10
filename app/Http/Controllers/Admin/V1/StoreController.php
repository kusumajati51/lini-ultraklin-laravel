<?php

namespace App\Http\Controllers\Admin\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use Validator;

use App\V1\UserStore;

use App\Traits\V1\OfficerTrait;

class StoreController extends Controller
{
    use OfficerTrait;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $stores = UserStore::orderBy('id', 'desc')
            ->paginate(24);

        $result = $stores->toArray();
        $result['data'] = collect($stores->items())->map(function ($store) {
            $store->owner = $store->user->name;
            $store->region_name = $store->region->name;
            $store->images;
            $store->packages;

            return $store;
        });

        return response()->json($stores);
    }

    public function changeStatus($id)
    {
        $rules = [
            'status' => 'required|in:accepted,rejected'
        ];

        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error_validation' => 1,
                'message' => 'Invalid status.',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $store = UserStore::find($id);

            if (is_null($store)) {
                return response()->json([
                    'error' => '1',
                    'message' => 'Data not found.',
                ], 400);
            }

            $store->status = $this->request->status;

            if ($store->status == 'accepted') {
                $store->active = true;
                $store->actived_at = date('Y-m-d H:i:s');
                $store->actived_by = date('Y-m-d H:i:s');
                $store->updated_by = $this->officer()->name. ' :: '.$this->request->header('User-Agent');

                $store->user->update([
                    'status' => 'partner',
                    'updated_by' => $this->officer()->name. ' :: '.$this->request->header('User-Agent')
                ]);
            }

            $store->save();

            $store->owner = $store->user->name;
            $store->region_name = $store->region->name;
            $store->packages;

            DB::commit();

            return response()->json([
                'success' => 1,
                'message' => $store->name.' has '.$store->status.'.',
                'data' => $store
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(handle_error($this->request, $e));
        }
    }

    public function updatePackages($id)
    {
        $rules = [
            'packages' => 'required|array'
        ];

        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error_validation' => 1,
                'message' => 'Invalid status.',
                'errors' => $validator->errors()
            ], 422);
        }

        $store = UserStore::find($id);

        if (is_null($store)) {
            return response()->json([
                'error' => '1',
                'message' => 'Data not found.'
            ], 400);
        }

        DB::beginTransaction();

        try {
            $store->packages()->detach();

            foreach ($this->request->packages as $package) {
                $store->packages()->attach($package);
            }

            DB::commit();

            $store->owner = $store->user->name;
            $store->region_name = $store->region->name;
            $store->packages;

            return response()->json([
                'success' => 1,
                'message' => 'Packages of '.$store->name.' updated.',
                'data' => $store
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(handle_error($this->request, $e));
        }
    }
}
