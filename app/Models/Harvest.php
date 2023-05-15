<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Harvest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'indonesia_village_id',
        'total',
        'price',
        'address',
        'longitude',
        'latitude',
        'status',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getFullAddressAttribute() {
        $location = \Indonesia::findVillage($this->indonesia_village_id, ['province', 'city', 'district']);
        return $this->address . ', Kel. ' . ucwords(strtolower($location->name)) . ', Kec. ' . ucwords(strtolower($location->district->name)) . ', ' . ucwords(strtolower($location->city->name)) . ', ' . ucwords(strtolower($location->province->name));
    }

    public function images(){
        return $this->hasMany(HarvestImage::class, 'harvest_id', 'id');
    }
}
