<?php

namespace App\V1;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $casts = [
        'rule_data' => 'array'
    ];

    public function services()
    {
        return $this->belongsToMany(
            '\App\Service',
            'level_service',
            'level_id',
            'service_id'
        )->withPivot([
            'percent',
            'value',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at'
        ]);
    }
}
