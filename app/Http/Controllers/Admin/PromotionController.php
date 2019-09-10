<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;

use App\V1\Promotion;
use App\Package;

class PromotionController extends Controller
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $promotions = Promotion::orderBy('id', 'desc')
            ->with([
                'packages' => function ($query) {
                    $query->orderBy('display_name', 'asc');
                }
            ])
            ->paginate(24);

        return view('admin.promotion.index', compact(
            'promotions'
        ));
    }

    public function show($id)
    {
        $promotion = Promotion::find($id);

        return view('admin.promotion.show', compact(
            'promotion'
        ));
    }

    public function create()
    {
        $optionGroups = [];

        $optionGroups['package'] = Package::byRegion()
            ->select(
                'packages.id as value', 'packages.display_name as label',
                'regions.code as region_code', DB::raw("UPPER(regions.name) as region_name")
            )
            ->leftJoin('regions', 'packages.region_id', 'regions.id')
            ->orderBy('regions.name', 'asc')
            ->orderBy('label', 'asc')
            ->get()
            ->groupBy('region_name')
            ->map(function ($val, $key) {
                $item = [
                    'label' => $key,
                    'options' => $val
                ];

                return $item;
            })
            ->values();

        $optionGroups['target'] = collect(config('ultraklin.promotion_targets'))
            ->groupBy('category_name')
            ->map(function ($val, $key) {
                $item = [
                    'label' => $key,
                    'options' => collect($val)->map(function ($target) {
                        $_item = [
                            'label' => $target['display_name'],
                            'value' => $target['name']
                        ];

                        return $_item;
                    })
                ];

                return $item;
            });

        return view('admin.promotion.create', compact(
            'optionGroups'
        ));
    }

    public function store()
    {
        
        $this->request->merge([
            'packages' => collect($this->request->packages)->map(function ($packageId) {
                return (int) $packageId;
            })
        ]);

        $this->validate($this->request, [
            'packages' => 'required',
            'code' => 'required|unique:promotions',
            'name' => 'required',
            'value' => 'required|numeric',
            't' => 'required',
            'i' => 'required',
            'target' => 'required'
        ]);

        $promotion = new Promotion;
        $promotion->code = strtoupper($this->request->code);
        $promotion->name = $this->request->name;
        $promotion->min_order = $this->request->has('min_order') ? $this->request->min_order : null;
        $promotion->value = $this->request->value;
        $promotion->day = $this->request->has('day') ? $this->request->day : null;
        $promotion->time = [$this->request->t, $this->request->i];
        $promotion->target = $this->request->target;
        $promotion->description = $this->request->has('description') ? $this->request->description : null;
        $promotion->created_by = auth('officer')->user()->name.' :: '.$this->request->header('User-Agent');;
        $promotion->updated_by = auth('officer')->user()->name.' :: '.$this->request->header('User-Agent');;
        $promotion->save();

        $promotion->packages()->attach($this->request->packages);

        session()->flash('success', 'New promotion added.');

        return redirect('/admin/promotions');
    }

    public function edit($id)
    {
        $optionGroups = [];

        $optionGroups['package'] = Package::byRegion()
            ->select(
                'packages.id as value', 'packages.display_name as label',
                'regions.code as region_code', DB::raw("UPPER(regions.name) as region_name")
            )
            ->leftJoin('regions', 'packages.region_id', 'regions.id')
            ->orderBy('regions.name', 'asc')
            ->orderBy('label', 'asc')
            ->get()
            ->groupBy('region_name')
            ->map(function ($val, $key) {
                $item = [
                    'label' => $key,
                    'options' => $val
                ];

                return $item;
            })
            ->values();

        $optionGroups['target'] = collect(config('ultraklin.promotion_targets'))
            ->groupBy('category_name')
            ->map(function ($val, $key) {
                $item = [
                    'label' => $key,
                    'options' => collect($val)->map(function ($target) {
                        $_item = [
                            'label' => $target['display_name'],
                            'value' => $target['name']
                        ];

                        return $_item;
                    })
                ];

                return $item;
            });

        $promotion = Promotion::find($id);
        $promotion->packages = $promotion->packages()->pluck('id');

        return view('admin.promotion.edit',  compact(
            'optionGroups',
            'promotion'
        ));
    }

    public function update($id)
    {
        $this->request->merge([
            'packages' => collect($this->request->packages)->map(function ($packageId) {
                return (int) $packageId;
            })
        ]);

        $this->validate($this->request, [
            'packages' => 'required',
            'code' => 'required|unique:promotions,code,'.$id,
            'name' => 'required',
            'value' => 'required|numeric',
            't' => 'required',
            'i' => 'required',
            'target' => 'required'
        ]);

        $promotion = Promotion::find($id);
        $promotion->code = strtoupper($this->request->code);
        $promotion->name = $this->request->name;
        $promotion->min_order = $this->request->has('min_order') ? $this->request->min_order : null;
        $promotion->value = $this->request->value;
        $promotion->day = $this->request->has('day') ? $this->request->day : null;
        $promotion->time = [$this->request->t, $this->request->i];
        $promotion->target = !$this->request->target ? null : $this->request->target;
        $promotion->description = $this->request->has('description') ? $this->request->description : null;
        $promotion->active = $this->request->has('active') ? true : false;
        $promotion->updated_by = auth('officer')->user()->name.' :: '.$this->request->header('User-Agent');
        $promotion->save();

        $promotion->packages()->sync($this->request->packages);

        session()->flash('success', 'Promotion updated.');

        return redirect('/admin/promotions');
    }
}
