<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = ['province_id', 'name', 'code'];

    /**
     * Get the province this city belongs to
     */
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Get all barangays in this city
     */
    public function barangays()
    {
        return $this->hasMany(Barangay::class);
    }
}
