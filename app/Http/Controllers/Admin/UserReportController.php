<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;
use DB;
use Excel;

use App\User;

class UserReportController extends Controller
{
    protected $filter = [];
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function setTimeFilter()
    {
        if ($this->request->has('date_mode') && $this->request->date_mode == 'daily') {
            $this->filter['date_mode'] = 'daily';

            $this->filter['time'] = [
                $this->request->date,
                $this->request->date
            ];
        }
        else if ($this->request->has('date_mode') && $this->request->date_mode == 'range') {
            $this->filter['date_mode'] = 'range';

            $this->filter['time'] = [
                $this->request->d,
                $this->request->a
            ];
        }
        else {
            $this->filter['date_mode'] = 'daily';

            $this->filter['time'] = [
                Carbon::today()->toDateString(),
                Carbon::today()->toDateString(),
            ];
        }
    }

    public function setFilter()
    {
        $this->setTimeFilter();
    }

    public function index()
    {
        $this->setFilter();

        $users = User::select(
            'users.name', 'users.phone', 'users.email', 'users.created_at',
            DB::raw('
                (
                    SELECT MAX(invoices.id)
                    FROM invoices
                    WHERE invoices.user_id = users.id
                ) as last_invoice
            '),
            DB::raw('
                (
                    SELECT location
                    FROM orders
                    WHERE invoice_id = last_invoice
                    ORDER BY id
                    LIMIT 1
                ) as last_location
            ')
        )
        ->where(function ($query) {
            $query->whereBetween('created_at', $this->filter['time']);
        })
        ->orderBy('users.created_at', 'desc')
        ->get();

        if ($this->request->has('export') && ($this->request->export == 'xlsx' || $this->request->export == 'pdf')) {
            return $this->export($users, $this->request->export);
        }

        $filter = $this->filter;
        
        return view('admin.report.user', compact(
            'users',
            'filter'
        ));
    }

    public function export($data, $format)
    {
        if ($this->request->has('date_mode') && $this->filter['date_mode'] == 'daily') {
            $filename = 'User_Report__'.$this->request->date;
        }
        else if ($this->request->has('date_mode') && $this->filter['date_mode'] == 'range') {
            $filename = 'User_Report__'.$this->request->d.'_'.$this->request->a;
        }
        else {
            $filename = 'User_Report__'.date('Y-m-d');
        }

        Excel::create($filename, function ($excel) use ($data) {
            $excel->sheet('Sheet 1', function ($sheet) use ($data) {
                $sheet->row(1, [
                    'Name', 'Phone', 'Email', 'Location', 'Created At'
                ]);

                $sheet->row(1, function ($row) {
                    $row->setFontWeight('bold');
                });

                $sheet->setColumnFormat([
                    'E' => 'yyyy-mm-dd HH:ii:ss'
                ]);

                foreach ($data as $i => $row) {
                    $sheet->row($i + 2, [
                        $row->name, $row->phone, $row->email, $row->last_location, $row->created_at
                    ]);
                }
            });
        })->export($format);
    }
}
