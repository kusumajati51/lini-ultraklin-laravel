<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

use Validator;

use App\Customer;

class CustomerController extends Controller
{
    protected $filter;
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function setDefaultFilter() {
        $this->filter = [
            'sort' => ['id', 'desc']
        ];

        if ($this->request->has('status')) {
            $this->filter = array_set($this->filter, 'status', $this->request->status);
        }
        else {
            $this->filter = array_set($this->filter, 'status', ['user']);
        }

        if ($this->request->has('search')) {
            $this->filter = array_set($this->filter, 'search', $this->request->search);
        }
        else {
            $this->filter = array_set($this->filter, 'search', '');
        }
    }

    public function index()
    {
        $this->setDefaultFilter();

        $customers = Customer::byRegion()
        ->where(function ($query) {
            return $query->whereIn('status', $this->filter['status']);
        })
        ->where(function ($query) {
            return $query->where('name', 'like', '%'.$this->filter['search'].'%')
                ->orWhere('email', 'like', '%'.$this->filter['search'].'%')
                ->orWhere('phone', 'like', '%'.$this->filter['search'].'%');
        })
        ->orderBy($this->filter['sort'][0], $this->filter['sort'][1])
        ->paginate(24);

        $customers->appends($this->filter);

        $filter = $this->filter;

        return view('admin.customer.index', compact(
            'customers',
            'filter'
        ));
    }

    /**
     * @queryString region, search
     */
    public function jsonIndex()
    {
        $customers = Customer::where(function ($query) {
            if ($this->request->has('region')) {
                $query->whereHas('region', function ($region) {
                        $region->where('id', $this->request->region)
                            ->orWhere('code', $this->request->region);
                    });
            }
        })
        ->where(function ($query) {
            $query->where('name', 'like', '%'.$this->request->search.'%')
                ->orWhere('email', 'like', '%'.$this->request->search.'%')
                ->orWhere('phone', 'like', '%'.$this->request->search.'%');
        })
        ->orderBy('name')
        ->limit(10)
        ->get();

        return response()->json($customers);
    }

    public function jsonStore()
    {
        $rules = [
            'name' => 'required',
            'phone' => [
                'required',
                Rule::unique('customers')->where(function ($query) {
                    $query->where('region_id', $this->request->region);
                })
            ],
            'email' => [
                'required',
                Rule::unique('customers')->where(function ($query) {
                    $query->where('region_id', $this->request->region);
                })
            ],
            'region' => 'required|exists:regions,id'
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
        $customer->region_id = $this->request->region;
        $customer->name = $this->request->name;
        $customer->phone = $this->request->phone;
        $customer->email = $this->request->email;
        $customer->created_by = auth('officer')->user()->name.' :: '.$this->request->header('User-Agent');
        $customer->updated_by = auth('officer')->user()->name.' :: '.$this->request->header('User-Agent');
        $customer->save();

        return response()->json([
            'success' => 1,
            'message' => 'Customer saved.',
            'data' => $customer
        ]);
    }
}
