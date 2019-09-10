<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Storage;
use Image;

use App\Banner;

class BannerController extends Controller
{
    protected $request;
    protected $path;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->path = storage_path('app/banners');
    }

    public function index()
    {
        $banners = Banner::where(function ($query) {
            $query->byRegion()
                ->orWhere('region_id', null);
        })
        ->orderBy('active', 'desc')
        ->orderBy('id', 'desc')
        ->paginate(24);

        return view('admin.banner.index', compact(
            'banners'
        ));
    }

    public function create()
    {
        $options = [];

        $options['region'] = auth('officer')->user()->regions()
            ->select('name as label', 'id as value')
            ->orderBy('name')
            ->get();

        return view('admin.banner.create', compact(
            'options'
        ));
    }

    public function store()
    {
        $this->validate($this->request, [
            'image' => 'required',
            'name' => 'required'
        ]);

        if (!Storage::exists('banners')) {
            Storage::makeDirectory('banners', 0777, true, true);
        }

        $filename = time().str_random(20).'.'.$this->request->image->extension();

        Image::make($this->request->image->path())
            ->save($this->path.'/'.$filename);

        $banner = new Banner;
        $banner->region_id = $this->request->region;
        $banner->name = $this->request->name;
        $banner->description = $this->request->has('description') ? $this->request->description : null;
        $banner->file = $filename;
        $banner->target = $this->request->target;
        $banner->created_by = auth('officer')->user()->name.' :: '.$this->request->header('User-Agent');
        $banner->updated_by = auth('officer')->user()->name.' :: '.$this->request->header('User-Agent');
        $banner->save();

        session()->flash(
            'success',
            'New banner saved.'
        );

        return redirect('/admin/banners');
    }

    public function edit($id)
    {
        $options = [];

        $options['region'] = auth('officer')->user()->regions()
            ->select('name as label', 'id as value')
            ->orderBy('name')
            ->get();

        $banner = Banner::find($id);

        return view('admin.banner.edit', compact(
            'options',
            'banner'
        ));
    }

    public function update($id)
    {
        $this->validate($this->request, [
            'name' => 'required'
        ]);

        if ($this->request->hasFile('image')) {
            $filename = time().str_random(20).'.'.$this->request->image->extension();
    
            Image::make($this->request->image->path())
                ->save($this->path.'/'.$filename);
        }

        $banner = Banner::find($id);
        $banner->region_id = $this->request->region;
        $banner->name = $this->request->name;
        $banner->description = $this->request->has('description') ? $this->request->description : null;
        $banner->file = $this->request->hasFile('image') ? $filename : $banner->file;
        $banner->target = $this->request->target;
        $banner->active = $this->request->has('active') ? true : false;
        $banner->updated_by = auth('officer')->user()->name.' :: '.$this->request->header('User-Agent');
        $banner->save();

        session()->flash(
            'success',
            'Banner updated.'
        );

        return redirect('/admin/banners');
    }
}
