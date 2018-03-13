<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
        'origin', 'destination', 'airline_code',
        'flight_no', 'transfer', 'departure_at',
        'return_at', 'expired_at', 'distance',
        'price', 'price_per_distance',
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
        'return_at',
        'expired_at',
        'departure_at'
    ];

    public function originAirport()
    {
        return $this->belongsTo(Airport::class, 'origin', 'code');
    }

    public function destinationAirport()
    {
        return $this->belongsTo(Airport::class, 'destination', 'code');
    }

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class, 'inquiry_id');
    }
}
