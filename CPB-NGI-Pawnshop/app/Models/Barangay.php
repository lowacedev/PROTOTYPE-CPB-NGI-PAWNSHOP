<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    use HasFactory;

    protected $fillable = ['city_id', 'name', 'code'];

    /**
     * Get the city this barangay belongs to
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
