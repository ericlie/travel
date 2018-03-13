<?php

namespace App;

use App\Itinerary;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Inquiry extends Model
{
    use Notifiable;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
        'email', 'total_packs', 'depart_date',
        'return_date', 'budget', 'origin',
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
        'depart_date',
        'return_date',
    ];

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function airport()
    {
        return $this->hasOne(Airport::class, 'origin', 'code');
    }

    public function hotelQuotations()
    {
        return $this->hasMany(HotelQuotation::class);
    }

    public function itinerary()
    {
        return $this->hasOne(Itinerary::class);
    }
}
