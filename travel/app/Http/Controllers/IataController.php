<?php

namespace App\Http\Controllers;

use App\Airport;
use App\Inquiry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use DataTables;

class IataController extends Controller
{
    public function getAutocomplete()
    {
        $data = $this->validate(request(), [
            'query' => 'required|string',
        ]);
        $apiKey = '2e2b54e0-76bd-413e-a366-db2e3f8fb84b';
        $endpoint = 'https://iatacodes.org/api/v6/autocomplete';
        $query = '%'.$data['query'].'%';

        try {
            $airports = Airport::where('name', 'LIKE', $query)
                ->orWhere('city_name', 'LIKE', $query)
                ->orWhere('country_name', 'LIKE', $query)
                ->select([
                    'code', 'name', 'country_name', 'city_name',
                ])
                ->groupBy(['code', 'name', 'country_name', 'city_name'])
                ->get();
            // $client = new \GuzzleHttp\Client(['verify' => false]);
            // $response = $client->request('GET', $endpoint, [
            //     'query' => [
            //         'api_key' => $apiKey,
            //         'query' => $data['query'],
            //     ],
            // ]);
            // if ($response->getStatusCode() != '200') {
            //     return response()->json([
            //         'message' => 'error',
            //     ]);
            // }

            // $result = json_decode($response->getBody());
            // return response()->json($result->response->airports_by_cities);
            return response()->json($airports);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function recordInquiry()
    {
        $data = $this->validate(request(), [
            'budget' => 'required|numeric',
            'total_packs' => 'required|integer',
            'origin' => 'required|string|max:3',
            'daterange' => 'required',
            'email' => 'required|email',
        ]);
        $dates = explode(' - ', $data['daterange']);
        try {
            $inquiry = new Inquiry();
            $inquiry->depart_date = Carbon::parse($dates[0]);
            $inquiry->return_date = Carbon::parse($dates[1]);
            $inquiry->total_packs = $data['total_packs'];
            $inquiry->budget = $data['budget'];
            $inquiry->origin = $data['origin'];
            $inquiry->email = $data['email'];
            $inquiry->save();
            $inquiry->notify(new \App\Notifications\InquiryReceived());
            event(new \App\Events\IncomingInquiry($inquiry));

            return response()->json($inquiry);
        } catch (Exception $e) {
            logger($e);
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function inquiryList()
    {
        return DataTables::of(Inquiry::query())
        ->addColumn('date', function ($model) {
            return $model->depart_date->format('d-m-Y') . ' to '. $model->return_date->format('d-m-Y');
        })
        ->toJson();
    }

    public function getItinerary(Inquiry $inquiry)
    {
        return response()->json($inquiry->load('itinerary.hotelQuotation', 'itinerary.quotation'));
    }
}
