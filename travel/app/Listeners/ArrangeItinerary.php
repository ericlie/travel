<?php

namespace App\Listeners;

use App\Events\IncomingInquiry;
use App\Helpers\QuotationGenerator;
use App\Hotel;
use App\HotelQuotation;
use App\Itinerary;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ArrangeItinerary
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  IncomingInquiry  $event
     * @return void
     */
    public function handle(IncomingInquiry $event)
    {
        $profit = 0.9; // we take 10% profit :D
        $inquiry = $event->inquiry;
        (new QuotationGenerator($inquiry))->generate();
        $quotations = $inquiry->quotations;
        foreach ($quotations as $quotation) {
            $budget = ($inquiry->budget - $quotation->price) * $profit;
            $room = ceil($inquiry->total_packs / 2);
            $totalDays = $inquiry->return_date->diffInDays($inquiry->depart_date);
            $hotelBudget = $budget / $totalDays * $room;
            $hotels = Hotel::city($quotation->destination)->where('price', '<', $hotelBudget)->get();
            if (count($hotels) <= 0) {
                continue;
            }
            foreach ($hotels as $hotel) {
                $hotelQuote = new HotelQuotation([
                    'city_code' => $quotation->destination,
                    'name' => $hotel->name,
                    'check_in' => $inquiry->depart_date,
                    'check_out' => $inquiry->return_date,
                    'total_days' => $totalDays,
                    'total_rooms' => $room,
                    'price' => $hotel->price,
                    'total_price' => $hotel->price * $room * $totalDays,
                    'popularity' => $hotel->popularity,
                ]);
                $hotelQuote->inquiry()->associate($inquiry);
                $hotelQuote->save();
            }
        }
        $found = [];
        do {
            $cheapestQuote = $inquiry->quotations()->whereNotIn('id', $found)->orderBy('price', 'ASC')->first();
            $hotelQuote = $inquiry->hotelQuotations()
                ->where('city_code', $cheapestQuote->destination)
                ->orderBy('total_price', 'ASC')->orderBy('popularity', 'DESC')->first();
            $found[] = $cheapestQuote->id;
        } while (!$cheapestQuote || !$hotelQuote);

        $itinerary = new Itinerary();
        $itinerary->inquiry()->associate($inquiry);
        $itinerary->hotelQuotation()->associate($hotelQuote);
        $itinerary->quotation()->associate($cheapestQuote);
        $itinerary->save();
    }
}
