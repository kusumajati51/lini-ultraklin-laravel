<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Officer;
use App\Region;
use App\Role;
use App\Traits\V1\FcmTokenTrait;

class OfficerController extends Controller
{
    use FcmTokenTrait;

    protected $filter = [];
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function setDefaultFilter()
    {
        if ($this->request->has('region')) {
            $this->filter['region'] = collect($this->request->region)->map(function ($val) {
                return (int) $val;
            });
        }
        else {
            $this->filter['region'] = Region::pluck('id');
        }
    }

    public function index()
    {
        $this->setDefaultFilter();

        $officers = Officer::where(function ($query) {
            $query->whereHas('regions', function ($region) {
                $region->whereIn('id', $this->filter['region'])
                    ->orWhereIn('code', $this->filter['region']);
            });
        })
        ->orderBy('name')
        ->paginate(24);

        $options = [];
        
        $options['region'] = Region::select('regions.id as value', 'regions.name as label')
            ->get();
        
        $filter = $this->filter;

        return view('admin.officer.index', compact(
            'officers',
            'options',
            'filter'
        ));
    }

    public function create()
    {
        $options = [];

        $options['region'] = Region::select('id as value', 'name as label')
            ->orderBy('name')
            ->get();

        $options['role'] = Role::select('id as value', 'display_name as label')
            ->orderBy('display_name')
            ->get();

        $options['gender'] = [
            [
                'value' => 'Male',
                'label' => 'Male'
            ],
            [
                'value' => 'Female',
                'label' => 'Female'
            ]
        ];

        return view('admin.officer.create', compact(
            'options'
        ));
    }

    public function store()
    {
        $this->request->merge([
            'region' => collect($this->request->region)->map(function ($val) {
                return (int) $val;
            })
        ]);

        $this->validate($this->request, [
            'name' => 'required',
            'gender' => 'required',
            'phone' => 'required|unique:officers',
            'email' => 'required|unique:officers',
            'password' => 'required|min:6',
            'role' => 'required|exists:roles,id',
            'region' => 'required'
        ]);

        $officer = new Officer;
        $officer->name = $this->request->name;
        $officer->gender = $this->request->gender;
        $officer->phone = $this->request->phone;
        $officer->email = strtolower($this->request->email);
        $officer->password = bcrypt($this->request->password);
        $officer->role_id = $this->request->role;
        $officer->save();

        if ($this->request->has('region')) {
            foreach ($this->request->region as $region) {
                $officer->regions()->attach($region);
            }
        }

        session()->flash(
            'success',
            'New officer saved.'
        );

        return redirect('/admin/officers');
    }

    public function edit($id)
    {
        $officer = Officer::find($id);
        
        $options = [];

        $options['region'] = Region::select('id as value', 'name as label')
            ->orderBy('name')
            ->get();

        $options['role'] = Role::select('id as value', 'display_name as label')
            ->orderBy('display_name')
            ->get();

        $options['gender'] = [
            [
                'value' => 'Male',
                'label' => 'Male'
            ],
            [
                'value' => 'Female',
                'label' => 'Female'
            ]
        ];

        return view('admin.officer.edit', compact(
            'officer',
            'options'
        ));
    }

    public function update($id)
    {
        $this->request->merge([
            'region' => collect($this->request->region)->map(function ($val) {
                return (int) $val;
            })
        ]);

        $this->validate($this->request, [
            'name' => 'required',
            'gender' => 'required',
            'phone' => 'required|unique:officers,phone,'.$id,
            'email' => 'required|unique:officers,email,'.$id,
            'role' => 'required|exists:roles,id',
            'region' => 'required'
        ]);

        $officer = Officer::find($id);
        $officer->name = $this->request->name;
        $officer->gender = $this->request->gender;
        $officer->phone = $this->request->phone;
        $officer->email = strtolower($this->request->email);
        if (!is_null($this->request->password)) {
            $officer->password = bcrypt($this->request->password);
        }
        $officer->role_id = $this->request->role;
        $officer->save();

        if ($this->request->has('region')) {
            $officer->regions()->detach();

            foreach ($this->request->region as $region) {
                $officer->regions()->attach($region);
            }
        }

        session()->flash(
            'success',
            'Officer updated.'
        );

        return redirect('/admin/officers');
    }
}
