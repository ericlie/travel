<?php

namespace App\Observers;

use App\Quotation;

/**
*  Quotation Observer
*/
class QuotationObserver
{
    public function creating(Quotation $quotation)
    {
        if (!$quotation->originAirport || !$quotation->destinationAirport) {
            return true;
        }
        $quotation->distance = distance(
            $quotation->originAirport->lat,
            $quotation->originAirport->lon,
            $quotation->destinationAirport->lat,
            $quotation->destinationAirport->lon
        );
        $quotation->price_per_distance = $quotation->price / $quotation->distance;
        return true;
    }

    public function created(Quotation $quotation)
    {
        //
    }
}
