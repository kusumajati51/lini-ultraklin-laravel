<?php

namespace App\Traits\V1;

use DB;
use Validator;

use App\V1\Level;

use App\Utils\LogUtil;

trait LevelTrait
{
    public function jsonIndex()
    {
        $levels = Level::where('type', $this->levelType())
            ->orderBy('active', 'desc')
            ->orderBy('name', 'asc')
            ->paginate(24);

        return response()->json($levels);
    }

    public function jsonStore()
    {
        $isLevelSales = $this->levelType() == config('ultraklin_const.LEVEL_SALES');

        $rules = [
            'name' => 'required',
            'services' => 'required|array'
        ];

        if (count($this->request->services) > 0) {
            foreach ($this->request->services as $i => $service) {
                $rules['services.'. $i .'.id'] = 'required';

                if ($service['percent']) {
                    $rules['services.'. $i .'.value'] = 'required|numeric|min:0|max:100';
                } else {
                    $rules['services.'. $i .'.value'] = 'required|numeric|min:0';
                }
            }
        }

        if ($isLevelSales) {
            $rules['orderCounter'] = 'required';
        }

        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error_validation' => 1,
                'message' => 'Invalid data.',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        
        try {
            $level = new Level;
            $level->type = $this->levelType();
            $level->name = $this->request->name;
            $level->description = $this->request->description;
            $level->created_by = auth('officer')->user()->name.' :: '.$this->request->header('User-Agent');
            $level->updated_by = auth('officer')->user()->name.' :: '.$this->request->header('User-Agent');

            if ($isLevelSales) {
                $level->rule = 'order_counter';
                $level->rule_data = collect(explode(',', $this->request->orderCounter))->map(function ($item) {
                    return (int) $item;
                });
            }

            $level->save();

            foreach ($this->request->services as $service) {
                $level->services()->attach($service['id'], [
                    'percent' => $service['percent'],
                    'value' => $service['value'],
                    'created_by' => auth('officer')->user()->name.' :: '.$this->request->header('User-Agent'),
                    'updated_by' => auth('officer')->user()->name.' :: '.$this->request->header('User-Agent'),
                    'created_at' => date('Y-m-d h:i:s'),
                    'updated_at' => date('Y-m-d h:i:s')
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => 1,
                'message' => 'New '.$this->levelType().' level saved.'
            ]);
        } catch(\Exception $e) {
            DB::rollback();

            $log = new LogUtil('error');
        
            $log->createWithException($this->request, $e);

            return response()->json([
                'error' => 1,
                'message' => 'Oops! Something went wrong.'
            ], 500);
        }
    }

    public function jsonShow($id)
    {
        $level = Level::with('services')
            ->find($id);

        if (is_null($level)) {
            return response()->json([
                'error' => 1,
                'message' => 'Data not found.'
            ], 404);
        }

        return response()->json($level);
    }

    public function jsonEdit($id)
    {
        $isLevelSales = $this->levelType() == config('ultraklin_const.LEVEL_SALES');

        $level = Level::with('services')
            ->find($id);

        if ($isLevelSales) {
            $level->rule_data_string = implode(', ', $level->rule_data);
        }

        if (is_null($level)) {
            return response()->json([
                'error' => 1,
                'message' => 'Data not found.'
            ], 404);
        }

        return response()->json($level);
    }

    public function jsonUpdate($id)
    {
        $isLevelSales = $this->levelType() == config('ultraklin_const.LEVEL_SALES');
        
        $rules = [
            'name' => 'required',
            'services' => 'required|array'
        ];

        if (count($this->request->services) > 0) {
            foreach ($this->request->services as $i => $service) {
                $rules['services.'. $i .'.id'] = 'required';

                if ($service['percent']) {
                    $rules['services.'. $i .'.value'] = 'required|numeric|min:0|max:100';
                } else {
                    $rules['services.'. $i .'.value'] = 'required|numeric|min:0';
                }
            }
        }

        if ($isLevelSales) {
            $rules['orderCounter'] = 'required';
        }

        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error_validation' => 1,
                'message' => 'Invalid data.',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $level = Level::find($id);

        if (is_null($level)) {
            return response()->json([
                'error' => 1,
                'message' => 'Data not found.'
            ], 404);
        }

        DB::beginTransaction();
        
        try {
            $level->name = $this->request->name;
            $level->description = $this->request->description;
            $level->created_by = auth('officer')->user()->name.' :: '.$this->request->header('User-Agent');
            $level->updated_by = auth('officer')->user()->name.' :: '.$this->request->header('User-Agent');

            if ($isLevelSales) {
                $level->rule = 'order_counter';
                $level->rule_data = collect(explode(',', $this->request->orderCounter))->map(function ($item) {
                    return (int) $item;
                });
            }

            $level->save();

            $level->services()->detach();

            foreach ($this->request->services as $service) {
                $level->services()->attach($service['id'], [
                    'percent' => $service['percent'],
                    'value' => $service['value'],
                    'created_by' => auth('officer')->user()->name.' :: '.$this->request->header('User-Agent'),
                    'updated_by' => auth('officer')->user()->name.' :: '.$this->request->header('User-Agent'),
                    'created_at' => date('Y-m-d h:i:s'),
                    'updated_at' => date('Y-m-d h:i:s')
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => 1,
                'message' => ucwords($this->levelType()).' level updated.'
            ]);
        } catch(\Exception $e) {
            DB::rollback();

            $log = new LogUtil('error');
        
            $log->createWithException($this->request, $e);

            return response()->json([
                'error' => 1,
                'message' => 'Oops! Something went wrong.'
            ], 500);
        }
    }

    public function jsonList()
    {
        $levels = Level::where('type', $this->levelType())
            ->orderBy('name', 'asc')
            ->get();

        return response()->json($levels);
    }
}
