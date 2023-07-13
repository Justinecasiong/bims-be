<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChildInformation extends Model
{
    protected $guarded = [];
    
    public function household()
    {
        return $this->belongsTo(HouseholdHeadMember::class, 'household_head_member_id', 'id');
    }
}