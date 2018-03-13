<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HotelQuotation extends Model
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
        'city_code',
        'name',
        'check_in',
        'check_out',
        'total_days',
        'total_rooms',
        'price',
        'total_price',
        'popularity',
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
        'check_in',
        'check_out',
    ];

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class, 'inquiry_id');
    }
}
