<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    /**
     * Get all provinces in this region
     */
    public function provinces()
    {
        return $this->hasMany(Province::class);
    }
}
