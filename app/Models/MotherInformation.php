<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotherInformation extends Model
{
    protected $guarded = [];
    
    public function household()
    {
        return $this->belongsTo(HouseholdHeadMember::class, 'household_head_member_id', 'id');
    }

    public function householdHead()
    {
        return $this->belongsTo(HouseholdHead::class, 'household_head_id', 'id');
    }
}
