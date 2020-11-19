<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApartmentBuilding extends Model
{
    protected $table = 'apartment_building';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'avatar',
        'address',
        'latitude',
        'longitude',
        'name',
        'name_en',
        'merchant_id',
        'city_id',
        'district_id',
    ];
}
