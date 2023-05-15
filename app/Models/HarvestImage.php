<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HarvestImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'harvest_id',
        'path',
    ];

    public function harvest()
    {
        return $this->belongsTo(Harvest::class, 'harvest_id', 'id');
    }

    public function getPictAttribute()
    {
        return asset('storage/' . $this->path);
    }
}
