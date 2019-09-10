<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;

use App\User;

class AgentController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function setDefaultFilter()
    {
        if ($this->request->has('sort')) {
            $this->filter['sort'] = $this->request->sort;
        } else {
            $this->filter['sort'] = ['id', 'desc'];
        }

        if ($this->request->has('search')) {
            $this->filter['search'] = $this->request->search;
        } else {
            $this->filter['search'] = '';
        }
    }

    public function getUsersWithPagination()
    {
        $this->setDefaultFilter();

        $users = User::byRegion()
            ->where('status', 'agent')    
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

        return view('admin.agent.index', compact(
            'users', 'filter'
        ));
    }

    public function create()
    {
        return view('admin.agent.create');
    }

    public function store()
    {
        $rules = [
            'id' => 'required|exists:users'
        ];

        $messages = [
            'id.required' => 'No user selected.'
        ];

        $validator = Validator::make($this->request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator->errors());
        }

        $user = User::find($this->request->id);

        if (is_null($user)) {
            return back()
                ->withErrors($validator->errors()->add('id', 'No user selected.'));
        }

        $user->code = $user->generateAgentCode();
        $user->status = 'agent';
        $user->save();

        session()->flash('success', 'New agent created.');

        return redirect('/admin/agents');
    }
}
