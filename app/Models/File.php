<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    public function barangay_official()
    {
        return $this->belongsTo(BarangayOfficials::class, 'official_id', 'id');
    }
}
