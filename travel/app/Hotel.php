<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
        'city_code', 'name', 'price',
        'popularity'
    ];
    protected $hidden = [

    ];
    protected $guarded = [
        'id',
    ];
    protected $casts = [
        'id' => 'integer',
    ];
    public $timestamps = true;
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function scopeCity($query, $value)
    {
        return $query->where('city_code', $value);
    }
}
