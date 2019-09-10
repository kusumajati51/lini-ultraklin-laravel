<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use Image;
use Storage;
use Validator;

use App\V1\StoreImage;
use App\V1\UserStore;

use App\Utils\LogUtil;
use App\Utils\StoreUtil;

use App\Traits\V1\UserTrait;

class StoreController extends Controller
{
    use UserTrait;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function findNearby()
    {
        $rules = [
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180'
        ];

        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => 1,
                'message' => 'Invalid lat or lng'
            ], 400);
        }

        $storeUtil = new StoreUtil;

        $stores = $storeUtil->findNearby($this->request->only([
            'lat', 'lng'
        ]));

        return response()->json($stores);
    }

    public function getStore()
    {
        $store = $this->user()->store;

        if (is_null($store)) {
            return response()->json([
                'error' => 1,
                'message' => 'You doesn\'t have a store.'
            ], 400);
        }

        return response()->json($store);
    }

    public function store()
    {
        $rules = [
            'name' => 'required',
            'phone' => 'required|min:10|unique:user_stores,phone',
            'email' => 'required|email|unique:user_stores,email',
            'owner' => 'required',
            'identity_card_number' => 'required|unique:user_stores,identity_card_number',
            'identity_card' => 'required|image:jpg,jpeg',
            'region' => 'required|exists:regions,id',
            'address' => 'required',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'user' => 'can_create_store'
        ];

        $this->request->request->add([
            'user' => $this->user()->id
        ]);

        if ($this->request->hasFile('images')) {
            $rules['images'] = 'required';
            $rules['images.*'] = 'required|image:jpg,jpeg';
        }

        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error_validation' => 1,
                'message' => 'Invalid data.',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $idFilename = 'UK_ID'.$this->request->identity_card_number.date('YmdHis').'.jpg';

            $store = UserStore::create([
                'region_id' => $this->request->region,
                'user_id' => $this->request->user,
                'code' => UserStore::generateCode($this->request->name),
                'name' => $this->request->name,
                'phone' => $this->request->phone,
                'email' => $this->request->email,
                'owner' => $this->request->owner,
                'identity_card_number' => $this->request->identity_card_number,
                'identity_card' => $idFilename,
                'address' => $this->request->address,
                'lat' => $this->request->lat,
                'lng' => $this->request->lng,
                'created_by' => $this->user()->name. ' :: '.$this->request->header('User-Agent'),
                'updated_by' => $this->user()->name. ' :: '.$this->request->header('User-Agent')
            ]);

            if (!Storage::exists('images/store')) {
                Storage::makeDirectory('images/store', 0777, true, true);
            }

            Image::make($this->request->identity_card)->save(storage_path('app/images/store/'.$idFilename));

            if ($this->request->hasFile('images')) {
                foreach ($this->request->file('images') as $image) {
                    $filename = 'UK_S'.date('YmdHis').str_random(10).'.jpg';

                    Image::make($image)->save(storage_path('app/images/store/'.$filename));

                    StoreImage::create([
                        'store_id' => $store->id,
                        'filename' => $filename,
                        'created_by' => $this->user()->name. ' :: '.$this->request->header('User-Agent'),
                        'updated_by' => $this->user()->name. ' :: '.$this->request->header('User-Agent')
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => 1,
                'message' => 'Your store has created, our team will be to contact.',
                'data' => $store
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(handle_error($this->request, $e));
        }
    }

    public function update()
    {
        $store = $this->user()->store;

        if (is_null($store)) {
            return response()->json([
                'error' => 1,
                'message' => 'You doesn\'t have a store.'
            ], 400);
        }

        if ($store->active) {
            return response()->json([
                'error' => 1,
                'message' => 'Sorry, you can\'t update your store, please contact our support.'
            ], 400);
        }

        $hasIdentity = $this->request->has('identity_card_number') && $this->request->hasFile('identity_card');

        $rules = [
            'name' => 'required',
            'phone' => 'required|min:10|unique:user_stores,phone,'.$store->id,
            'email' => 'required|email|unique:user_stores,email,'.$store->id,
            'owner' => 'required',
            'region' => 'required|exists:regions,id',
            'address' => 'required',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'user' => 'can_create_store'
        ];

        $this->request->request->add([
            'store' => $store->id
        ]);

        if ($hasIdentity) {
            $rules['identity_card_number'] ='required|unique:user_stores,identity_card_number,'.$store->identity_card_number;
            $rules['identity_card'] = 'required|image:jpg,jpeg';
        }

        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error_validation' => 1,
                'message' => 'Invalid data.',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $data = $this->request->except(['store', 'identity_card_number', 'identity_card']);

            if ($hasIdentity) {
                $idFilename = 'UK_ID'.$this->request->identity_card_number.date('YmdHis').'.jpg';

                Image::make($this->request->identity_card)->save(storage_path('app/images/store/'.$idFilename));

                $data['identity_card_number'] = trim($this->request->identity_card_number);
                $data['identity_card'] = $filename;
            }

            $store->update($data);

            DB::commit();

            return response()->json([
                'success' => 1,
                'message' => 'Your store has updated.',
                'data' => $store
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(handle_error($this->request, $e));
        }
    }
}
