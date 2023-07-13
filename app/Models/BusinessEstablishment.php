<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessEstablishment extends Model
{
    protected $guarded = [];

    public function scopeSearch($query, $search_string)
    {
        $keywords = explode(" ", $search_string);

        $query->where(function($query) use ($keywords) {
            foreach($keywords as $index => $keyword){
                $index == 0
                    ? $query->where('business_est_name', 'like', '%' . $keyword . '%')
                    : $query->orWhere('business_est_name', 'like', '%' . $keyword . '%');
    
                $query->orWhere('business_type', 'like', "%$keyword%");
                $query->orWhere('business_owner', 'like', "%$keyword%");
            }
        });
       
        return $query;
    }
}