<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravolt\Indonesia\Models\Village;

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
        'phonenumber'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function category() {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function village() {
        return $this->belongsTo(Village::class, 'indonesia_village_id', 'id');
    }

    public function getFullAddressAttribute() {
        $location = \Indonesia::findVillage($this->indonesia_village_id, ['province', 'city', 'district']);
        return $this->address . ', Kel. ' . ucwords(strtolower($location->name)) . ', Kec. ' . ucwords(strtolower($location->district->name)) . ', ' . ucwords(strtolower($location->city->name)) . ', ' . ucwords(strtolower($location->province->name));
    }

    public function images(){
        return $this->hasMany(HarvestImage::class, 'harvest_id', 'id');
    }
}
