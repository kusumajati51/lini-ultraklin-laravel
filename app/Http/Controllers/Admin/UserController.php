<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;

class UserController extends Controller
{
    protected $filter;
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function setDefaultFilter() {
        if ($this->request->has('sort')) {
            $this->filter = array_set($this->filter, 'sort', $this->request->sort);
        }
        else {
            $this->filter = array_set($this->filter, 'sort', ['id', 'desc']);
        }

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

    public function getUsersWithPagination()
    {
        $this->setDefaultFilter();

        $users = User::byRegion()
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

        $users->appends($this->filter);

        return $users;
    }

    public function index()
    {
        $users = $this->getUsersWithPagination();

        $filter = $this->filter;

        return view('admin.user.index', compact(
            'users',
            'filter'
        ));
    }

    public function jsonIndex()
    {
        $users = $this->getUsersWithPagination();

        return response()->json($users);
    }
}
