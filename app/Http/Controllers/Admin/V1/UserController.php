<?php

namespace App\Http\Controllers\Admin\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;

use App\User;

use App\Utils\FilterUtil;
use App\Utils\UserUtil; 
use App\Utils\TimeUtil;

class UserController extends Controller
{
    protected $filter;
    protected $request;

    protected $filterUtil;
    protected $userUtil;
    protected $timeUtil;

    public function __construct(
        Request $request,
        FilterUtil $filterUtil,
        UserUtil $userUtil,
        TimeUtil $timeUtil
    )
    {
        $this->request = $request;

        $this->filterUtil = $filterUtil;
        $this->userUtil = $userUtil;
        $this->timeUtil = $timeUtil;
    }

    public function getUsersByUpline($code)
    {
        $this->request->request->add([
            'ref_code' => $code
        ]);

        $this->filterUtil->setFilter('user', $this->request->all());

        $this->userUtil
            ->collect([
                '*',
                DB::raw(
                    '(
                        SELECT COUNT(*) FROM orders
                        WHERE EXISTS (
                            SELECT * FROM invoices
                            WHERE invoices.id = orders.invoice_id AND invoices.user_id = users.id
                        )
                    ) AS total_order'
                )
            ])
            ->filter($this->filterUtil->getFilter('user'));

        if ($this->request->has('export')) {
            $this->exportUsers($code, $this->userUtil->get());
        }

        return response()->json($this->userUtil->paginate());
    }

    public function exportUsers($code, $data)
    {
        if ($this->request->has('time')) {
            $filename = $code.'__User_Report__'.$this->request->time[0].'_'.$this->request->time[1];
        } else {
            $filename = $code.'__User_Report__'.date('Y-m-d');
        }

        $data->map(function ($item) {
            $item->phone = str_replace('+', '', $item->phone);

            return $item;
        });

        \Excel::create($filename, function ($excel) use ($data) {
            $excel->sheet('Sheet 1', function ($sheet) use ($data) {
                $sheet->row(1, [
                    'Name', 'Phone', 'Email', 'Total Order', 'Location', 'Register At'
                ]);

                $sheet->row(1, function ($row) {
                    $row->setFontWeight('bold');
                });

                $sheet->setColumnFormat([
                    'E' => 'yyyy-mm-dd HH:ii:ss'
                ]);

                foreach ($data as $i => $row) {
                    $sheet->row($i + 2, [
                        $row->name,
                        $row->phone,
                        $row->email,
                        $row->total_order,
                        $row->last_location,
                        $row->created_at
                    ]);
                }
            });
        })->export('xlsx');
    }
}
