<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Environment extends Model
{
    protected $guarded = [];
    
    public function householdHead()
    {
        return $this->belongsTo(HouseholdHead::class, 'household_head_id', 'id');
    }
}