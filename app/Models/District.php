<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $table = 'districts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'city_id',
        'name',
        'name_en',
        'slug',
        'image',
        'short_name',
        'code',
        'kind_from',
        'kind_to',
        'hot',
        'priority',
        'status',
    ];
}
