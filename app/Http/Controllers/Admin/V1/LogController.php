<?php

namespace App\Http\Controllers\Admin\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use File;

class LogController extends Controller
{
    protected $filter, $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function createFilter()
    {
        $this->filter = [
            'date' => date('Y-m-d')
        ];

        if ($this->request->has('date')) {
            $this->filter['date'] = date('Y-m-d', strtotime($this->request->date));
        }
    }

    public function getErrorLogs()
    {
        $this->createFilter();

        $filename = 'error__'.$this->filter['date'].'.log';
        $filePath = storage_path('logs/'.$filename);

        if (!File::exists($filePath)) return response()->json([]);

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
