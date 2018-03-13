<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    protected $fillable = [
        'code', 'name',
        'city_code', 'city_name',
        'country_name', 'country_code',
        'timezone', 'lat', 'lon',
        'total_airports', 'city',
    ];
    protected $hidden = [

    ];
    protected $guarded = [
        'id',
    ];
    protected $casts = [
        'id' => 'integer',
        'city' => 'boolean',
    ];
    public $timestamps = false;

    public function scopeCountry($query)
    {
        return $query->where('country_name', 'LIKE', "%$query%");
    }
}
