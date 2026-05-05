<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $fillable = ['region_id', 'name', 'code'];

    /**
     * Get the region this province belongs to
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get all cities in this province
     */
    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
