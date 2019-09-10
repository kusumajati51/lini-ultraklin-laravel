<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Image;
use Storage;
use Validator;

use App\Menu;
use App\MenuItem;

class MenuController extends Controller
{
    protected $filter = [];
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $menu = Menu::orderBy('active', 'desc')
            ->paginate(24);

        return view('admin.menu.index', compact(
            'menu'
        ));
    }

    public function show($name)
    {
        $menu = Menu::where('name', $name)->first();
        $items = $menu->items()->orderBy('order', 'asc')->get();

        if (is_null($menu)) {
            session()->flash('error', 'Data not found.');

            return redirect('/admin/menu');
        }

        return view('admin.menu.show', compact(
            'menu',
            'items'
        ));
    }

    public function create()
    {
        return view('admin.menu.create');
    }

    public function store()
    {
        $rules = [
            'name' => 'required|alpha_dash|unique:menu',
            'display_name' => 'required',
            'items' => 'required',
            'items.*.icon' => 'required|image',
            'items.*.label' => 'required',
            'items.*.link' => 'required'
        ];

        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return redirect('/admin/menu/create')
                ->withInput()
                ->withErrors($validator->errors());
        }

        $menu = new Menu;
        $menu->name = strtolower($this->request->name);
        $menu->display_name = $this->request->display_name;
        $menu->description = $this->request->description;
        $menu->target = strtolower($this->request->target);
        $menu->save();

        if (!Storage::exists('images/menu')) {
            Storage::makeDirectory('images/menu', 0777, true, true);
        }

        foreach ($this->request->items as $i => $item) {
            $filename = strtoupper('ICON_'.time().str_random(20)).'.'.$item['icon']->extension();
            $filePath = storage_path('app/images/menu/'.$filename);

            Image::make($item['icon'])->save($filePath);

            $menu->items()->save(new MenuItem([
                'label' => $item['label'],
                'icon' => $filename,
                'link' => $item['link'],
                'order' => $i + 1
            ]));
        }

        session()->flash('success', 'New menu saved.');

        return redirect('admin/menu');
    }

    public function edit($id)
    {
        $menu = Menu::find($id);

        if (is_null($menu)) {
            session()->flash('error', 'Data not found.');

            return redirect('/admin/menu');
        }

        $menu->items = $menu->items()
            ->select('id', 'label', 'icon', 'link')
            ->get();

        return view('admin.menu.edit', compact(
            'menu'
        ));
    }

    public function update($id)
    {
        $rules = [
            'name' => 'required|alpha_dash|unique:menu,name,'.$id.',id',
            'display_name' => 'required',
            'items' => 'required',
            'items.*.id' => 'required',
            'items.*.label' => 'required',
            'items.*.link' => 'required'
        ];

        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return redirect('/admin/menu/'.$id.'/edit')
                ->withInput()
                ->withErrors($validator->errors());
        }

        $menu = Menu::find($id);

        if (is_null($menu)) {
            session()->flash('error', 'Data not found.');

            return redirect('/admin/menu');
        }

        $menu->name = strtolower($this->request->name);
        $menu->display_name = $this->request->display_name;
        $menu->description = $this->request->description;
        $menu->target = strtolower($this->request->target);
        $menu->save();

        if (!Storage::exists('images/menu')) {
            Storage::makeDirectory('images/menu', 0777, true, true);
        }

        foreach ($this->request->items as $i => $item) {
            if ($item['id'] == 0) {
                $menuItem = new MenuItem;
                $menuItem->menu_id = $menu->id;
            }
            else {
                $menuItem = MenuItem::find($item['id']);
            }

            if ($this->request->hasFile('items.'.$i.'.icon')) {
                $filename = strtoupper('ICON_'.time().str_random(20)).'.'.$item['icon']->extension();
                $filePath = storage_path('app/images/menu/'.$filename);
    
                Image::make($item['icon'])->save($filePath);

                $menuItem->icon = $filename;
            }
            
            $menuItem->label = $item['label'];
            $menuItem->link = $item['link'];
            $menuItem->order = $i + 1;
            $menuItem->save();
            
        }

        session()->flash('success', 'Menu update.');

        return redirect('admin/menu');
    }

    public function jsonSortItems($menuId)
    {
        foreach ($this->request->items as $item) {
            $menuItem = MenuItem::where('menu_id', $menuId)
                ->where('id', $item['id'])
                ->first();

            $menuItem->order = $item['order'];
            $menuItem->save();
        }

        return response()->json([
            'success' => 1,
            'message' => 'Item sorted.'
        ]);
    }

    public function jsonDestroyItem($menuId, $itemId)
    {
        $item = MenuItem::where('menu_id', $menuId)
            ->where('id', $itemId)
            ->first();

        if (is_null($item)) {
            return response()->json([
                'error' => 1,
                'message' => 'Item not found.'
            ]);
        }

        $item->delete();

        return response()->json([
            'success' => 1,
            'message' => 'Item deleted.'
        ]);
    }
}
