<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Item;

class ItemController extends Controller
{
    protected $request;
    protected $filter;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $items = Item::paginate(24);

        return view('admin.item.index', compact(
            'items'
        ));
    }

    public function create()
    {
        return view('admin.item.create');
    }

    public function store()
    {
        $this->validate($this->request, [
            'name' => 'required'
        ]);

        $item = new Item;
        $item->name = $this->request->name;
        $item->description = $this->request->has('description') ? $this->request->description : null;
        $item->created_by = null;
        $item->updated_by = null;
        $item->save();

        session()->flash(
            'success',
            'New item saved.'
        );

        return redirect('/admin/items');
    }

    public function edit($id)
    {
        $item = Item::find($id);

        if (is_null($item)) {
            session()->flash(
                'error',
                'Item not found.'
            );

            return redirect('/admin/items');
        }

        return view('admin.item.edit', compact(
            'item'
        ));
    }

    public function update($id)
    {
        $this->validate($this->request, [
            'name' => 'required'
        ]);

        $item = Item::find($id);

        if (is_null($item)) {
            session()->flash(
                'error',
                'Item not found.'
            );

            return redirect('/admin/items');
        }

        $item->name = $this->request->name;
        $item->description = $this->request->has('description') ? $this->request->description : $item->description;
        $item->created_by = null;
        $item->updated_by = null;
        $item->save();

        session()->flash(
            'success',
            'Item updated.'
        );

        return redirect('/admin/items');
    }

    public function getList()
    {
        if ($this->request->has('search')) {
            $this->filter = array_add($this->filter, 'search', $this->request->search);
        }

        $items = Item::select('*');

        if ($this->request->has('search')) {
            $items = $items->where('name', 'like', '%'.$this->filter['search'].'%');
        }

        $items = $items->get();

        return response()->json($items);
    }
}
