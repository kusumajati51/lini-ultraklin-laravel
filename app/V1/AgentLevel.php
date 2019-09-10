<?php

namespace App\V1;

use Illuminate\Database\Eloquent\Model;

class AgentLevel extends Model
{
    public function services()
    {
        return $this->belongsToMany(
            '\App\Service',
            'agent_level_service',
            'agent_level_id',
            'service_id'
        )->withPivot([
            'percent', 'value', 'created_by', 'updated_by', 'created_at', 'updated_at'
        ]);
    }
}
