<?php

namespace App\Console\Commands;

use App\Inquiry;
use Illuminate\Console\Command;

class BuildQuotation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'iata:quotation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make Quotation from Inquiry';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $inquiries = Inquiry::where('origin', 'SIN')->get();

        foreach ($inquiries as $inquiry) {
            $apiKey = '6e9f0b7bbd44031c19dc77144339b520';
            $endpoint = 'http://api.travelpayouts.com/v1/city-directions';
            $currency = 'SGD';
            $base = "Un";
            try {
                $client = new \GuzzleHttp\Client(['verify' => false]);
                $response = $client->request('GET', $endpoint, [
                    'query' => [
                        'token' => $apiKey,
                        'origin' => $inquiry->origin,
                        'currency' => $currency,
                    ],
                ]);
                if ($response->getStatusCode() != '200') {
                    $this->error($error);
                }

                $result = json_decode($response->getBody());
                foreach ($result->data as $code => $value) {
                    $response = $client->request('GET', 'http://api.travelpayouts.com/v1/prices/cheap', [
                        'query' => [
                            'token' => $apiKey,
                            'origin' => $inquiry->origin,
                            'destination' => $code,
                            'depart_date' => $inquiry->depart_date,
                            'return_date' => $inquiry->return_date,
                            'currency' => $currency,
                        ],
                    ]);

                    $this->info('Fetching information');
                    usleep(0.5 * 1000000);

                    if ($response->getStatusCode() != '200') {
                        $this->error($error);
                        continue;
                    }
                    $ticket = json_decode($response->getBody());
                    if (empty($ticket->data)) {
                        $this->error('Origin: '. $inquiry->origin. ' to : ' . $code . ' is not available');
                        continue;
                    }
                    foreach ($ticket->data->{$code} as $datum) {
                        $this->info('Origin: '. $inquiry->origin. ' to : ' . $code . ' is '.$datum->price.
                            ' - depart at: '. $datum->departure_at . ' return at :'. $datum->departure_at);
                    }
                }
                if (isset($result->error)) {
                    $this->error($error);
                }
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
    }
}
