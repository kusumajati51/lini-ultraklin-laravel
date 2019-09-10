<?php

namespace App\Http\Controllers\Admin\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Traits\V1\UserLevelTrait;

use App\Interfaces\V1\LevelInterface;

class SalesController extends Controller implements LevelInterface
{
    use UserLevelTrait;

    protected $filter;
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function levelType()
    {
        return config('ultraklin_const.LEVEL_SALES');
    }
}
