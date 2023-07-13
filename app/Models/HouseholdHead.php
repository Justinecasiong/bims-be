<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HouseholdHead extends Model
{
    protected $guarded = [];
    
    public function zones()
    {
        return $this->belongsTo(Zones::class, 'zone_id', 'id');
    }

    public function familyMembers()
    {
        return $this->hasMany(HouseholdHeadMember::class);
    }

    public function scopeSearch($query, $search_string)
    {
        $keywords = explode(" ", $search_string);

        $query->where(function($query) use ($keywords) {
            foreach($keywords as $index => $keyword){
                $index == 0
                    ? $query->where('first_name', 'like', '%' . $keyword . '%')
                    : $query->orWhere('first_name', 'like', '%' . $keyword . '%');
    
                $query->orWhere('middle_name', 'like', "%$keyword%");
                $query->orWhere('last_name', 'like', "%$keyword%");
                $query->orWhere('household_num', 'like', "%$keyword%");
                $query->orWhere('family_num', 'like', "%$keyword%");
            }
        });
       
        return $query;
    }
}