<?php

namespace App\Http\Controllers\Admin\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\V1\Level;

use App\Interfaces\V1\LevelInterface;

use App\Traits\V1\LevelTrait;

class AgentLevelController extends Controller implements LevelInterface
{
    use LevelTrait;

    protected $filter;
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function levelType()
    {
        return config('ultraklin_const.LEVEL_AGENT');
    }
}
