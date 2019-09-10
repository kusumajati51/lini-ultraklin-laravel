<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;

use App\V1\Customer;
use App\V1\CustomerClient;

class ClientCustomerController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function register()
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:customers',
            'phone' => 'required|min:10|unique:customers',
            'customerId' => 'required|unique:customer_client,client_id',
            'client' => 'required|in:ADG'
        ];

        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error_validation' => 1,
                'message' => 'Invalid data.',
                'data' => $validator->errors()
            ]);
        }

        $customer = new Customer;
        $customer->name = $this->request->name;
        $customer->email = $this->request->email;
        $customer->phone = $this->request->phone;
        $customer->created_by = $this->request->client.' :: '.$this->request->header('User-Agent');
        $customer->updated_by = $this->request->client.' :: '.$this->request->header('User-Agent');
        $customer->save();

        $customer->client()->save(new CustomerClient([
            'client_id' => $this->request->customerId,
            'client' => $this->request->client
        ]));

        return response()->json([
            'success' => 1,
            'message' => 'Your customer saved.'
        ]);
    }
}
