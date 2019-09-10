<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Package;
use App\Service;

class PackageController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $packages = Package::byRegion()
            ->paginate(24);

        return view('admin.package.index', compact(
            'packages'
        ));
    }

    public function create()
    {
        $options = [];

        $options['region'] = auth('officer')->user()->regions()
            ->select('id as value', 'name as label')
            ->orderBy('name')
            ->get();

        $options['service'] = Service::select('id as value', 'display_name as label')
            ->orderBy('display_name')
            ->get();

        return view('admin.package.create', compact(
            'options'
        ));
    }

    public function store()
    {
        $this->validate($this->request, [
            'region' => 'required|exists:regions,id',
            'service' => 'required|exists:services,id',
            'name' => 'required|unique:packages',
            'display_name' => 'required'
        ]);

        $service = Service::find($this->request->service);

        $package = new Package;
        $package->region_id = $this->request->region;
        $package->name = $this->request->name;
        $package->display_name = $this->request->display_name;
        $package->description = ($this->request->has('description')) ? $this->request->description : null;
        $package->service()->associate($service);
        $package->created_by = auth('officer')->user()->name.' :: '.$this->request->header('User-Agent');
        $package->updated_by = auth('officer')->user()->name.' :: '.$this->request->header('User-Agent');
        $package->save();

        session()->flash('success', 'New package added.');

        return redirect('/admin/packages');
    }

    public function edit($id)
    {
        $options = [];

        $options['region'] = auth('officer')->user()->regions()
            ->select('id as value', 'name as label')
            ->orderBy('name')
            ->get();

        $options['service'] = Service::select('id as value', 'display_name as label')
            ->orderBy('display_name')
            ->get();
        
        $package = Package::byRegion()
            ->find($id);

        return view('admin.package.edit', compact(
            'options',
            'package'
        ));
    }

    public function update($id)
    {
        $this->validate($this->request, [
            'service' => 'required|exists:services,id',
            'name' => 'required|unique:packages,name,'.$id,
            'display_name' => 'required'
        ]);

        $service = Service::find($this->request->service);

        $package = Package::byRegion()->find($id);
        $package->region_id = $this->request->region;
        $package->name = strtolower($this->request->name);
        $package->display_name = $this->request->display_name;
        $package->description = ($this->request->has('description')) ? $this->request->description : null;
        $package->active = $this->request->has('active') ? true : false;
        $package->service()->associate($service);
        $package->updated_by = auth('officer')->user()->name.' :: '.$this->request->header('User-Agent');
        $package->save();

        session()->flash('success', 'Package updated.');

        return redirect('/admin/packages');
    }

    public function showItems($id)
    {
        $package = Package::byRegion()->find($id);

        $items = $package->items()
            ->paginate(24);

        return view('admin.package.item', compact(
            'package',
            'items'
        ));
    }

    public function addItem($id)
    {
        $package = Package::byRegion()->find($id);

        return view('admin.package.item_add', compact(
            'package'
        ));
    }

    public function storeItem($id)
    {
        $this->validate($this->request, [
            'item' => 'required|exists:items,id',
            'price' => 'required|numeric'
        ]);

        $package = Package::byRegion()->find($id);

        $package->items()->attach($this->request->item, [
            'price' => $this->request->price,
            'created_by' => null,
            'updated_by' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        session()->flash('success', 'New item added.');

        return redirect('/admin/packages/'.$id.'/items');
    }

    public function editItem($id, $itemId)
    {
        $package = Package::byRegion()->find($id);

        $item = $package->items()
            ->where('id', $itemId)
            ->first();

        return view('admin.package.item_edit', compact(
            'package',
            'item'
        ));
    }

    public function updateItem($id, $itemId)
    {
        $this->validate($this->request, [
            'price' => 'required|numeric'
        ]);

        $package = Package::byRegion()->find($id);

        $package->items()->detach($itemId);

        $package->items()->attach($this->request->item, [
            'price' => $this->request->price,
            'created_by' => null,
            'updated_by' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        session()->flash('success', 'Item updated.');

        return redirect('/admin/packages/'.$id.'/items');
    }

    public function deleteItem($id, $itemId)
    {
        $package = Package::byRegion()->find($id);

        $package->items()->detach($itemId);

        session()->flash('success', 'Item deleted.');

        return redirect('/admin/packages/'.$id.'/items');
    }
}
