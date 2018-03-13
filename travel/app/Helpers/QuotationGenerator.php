<?php

namespace App\Helpers;

use App\Inquiry;
use App\Quotation;
use Carbon\Carbon;
use GuzzleHttp\Client;

class QuotationGenerator
{
    protected $apiKey = '6e9f0b7bbd44031c19dc77144339b520';
    protected $endpoint = 'http://api.travelpayouts.com/v1/';
    protected $currency = 'SGD';
    /**
     * @var \GuzzleHttp\Client
     */
    protected $http;

    public $popular_destinations;

    public function __construct(Inquiry $inquiry)
    {
        $this->inquiry = $inquiry;
        $this->http = new Client(['verify' => false]);
        $this->getPopularDestinations();
    }

    public function getPopularDestinations()
    {
        $response = $this->http->request('GET', $this->getEndpointOf('city-directions'), [
            'query' => [
                'token' => $this->apiKey,
                'origin' => $this->inquiry->origin,
                'currency' => $this->currency,
            ],
        ]);
        if ($response->getStatusCode() != '200') {
            $this->error($error);
        }

        $result = json_decode($response->getBody());
        return $this->popular_destinations = $result->data;
    }

    public function getCheapTikets(string $destination)
    {
        $response = $this->http->request('GET', $this->getEndpointOf('prices/cheap'), [
            'query' => [
                'token' => $this->apiKey,
                'origin' => $this->inquiry->origin,
                'destination' => $destination,
                'depart_date' => $this->inquiry->depart_date,
                'return_date' => $this->inquiry->return_date,
                'currency' => $this->currency,
            ],
        ]);

        usleep(0.5 * 1000000);

        if ($response->getStatusCode() != '200') {
            throw new Exception('Error when getting cheap price for '. $destination);
        }

        $ticket = json_decode($response->getBody());
        if (empty($ticket->data)) {
            throw new Exception('Origin: '. $this->inquiry->origin. ' to : ' . $destination . ' is not available');
        }

        return $ticket->data->{$destination};
    }

    public function createQuotation(array $data)
    {
        $quotation = new Quotation([
            'origin' => $data['origin'],
            'destination' => $data['destination'],
            'airline_code' => $data['airline_code'],
            'flight_no' => $data['flight_no'],
            'transfer' => 0,
            'departure_at' => $data['departure_at'],
            'return_at' => $data['return_at'],
            'expired_at' => $data['expired_at'],
            'distance' => 0,
            'price' => $data['price'],
            'price_per_distance' => 0,
        ]);
        $quotation->inquiry()->associate($this->inquiry);
        $quotation->save();
    }

    public function generate()
    {
        if (empty($this->popular_destinations)) {
            return;
        }
        foreach ($this->popular_destinations as $code => $value) {
            try {
                $cheapTikets = $this->getCheapTikets($code);
                foreach ($cheapTikets as $ticket) {
                    $this->createQuotation([
                        'origin' => $this->inquiry->origin,
                        'destination' => $code,
                        'airline_code' => $ticket->airline,
                        'flight_no' => $ticket->flight_number,
                        'departure_at' => Carbon::parse($ticket->departure_at),
                        'return_at' => Carbon::parse($ticket->return_at),
                        'expired_at' => Carbon::parse($ticket->expires_at),
                        'price' => $ticket->price,
                    ]);
                }
            } catch (Exception $e) {
                continue;
            }
        }
    }

    protected function getEndpointOf(string $url): string
    {
        return $this->endpoint.$url;
    }
}
