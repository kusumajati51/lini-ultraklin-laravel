<?php

namespace App\Http\Controllers\SystemTool;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use File;

class LoggerController extends Controller
{
    protected $filter = [];
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function setTimeFilter()
    {
        if ($this->request->has('date')) {
            $this->filter['date'] = $this->request->date;
        }
        else {
            $this->filter['date'] = date('Y-m-d');
        }
    }

    public function setFilter()
    {
        $this->setTimeFilter();
    }

    public function getApiLogs()
    {
        $this->setFilter();

        $filename = 'api__'.$this->filter['date'].'.log';
        $filePath = storage_path('logs/'.$filename);

        if (!File::exists($filePath)) {
            return response()->json([]);
        }

        $content = File::get($filePath);
        $contentSplited = explode("\n", $content);
        $contentArray = collect($contentSplited)->map(function ($content) {
            return json_decode($content);
        })
        ->filter(function ($content) {
            return !is_null($content);
        });

        return response()->json(
            $contentArray
        );
    }

    public function getErrorLogs()
    {
        $this->setFilter();

        $filename = 'error__'.$this->filter['date'].'.log';
        $filePath = storage_path('logs/'.$filename);

        if (!File::exists($filePath)) {
            return response()->json([]);
        }

        $content = File::get($filePath);
        $contentSplited = explode("\n", $content);
        $contentArray = collect($contentSplited)->map(function ($content) {
            return json_decode($content);
        })
        ->filter(function ($content) {
            return !is_null($content);
        });

        return response()->json(
            $contentArray
        );
    }
}
