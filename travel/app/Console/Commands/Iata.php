<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Iata extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'iata:airport {query : The query for lookup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get IATA Aiport Code';

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
        $this->info('Running IATA Code search for Airport');
        $apiKey = '2e2b54e0-76bd-413e-a366-db2e3f8fb84b';
        $endpoint = 'https://iatacodes.org/api/v6/autocomplete';
        $base = "Un";
        try {
            $client = new \GuzzleHttp\Client(['verify' => false]);
            $response = $client->request('GET', $endpoint, [
                'query' => [
                    'api_key' => $apiKey,
                    'query' => $this->argument('query'),
                ],
            ]);
            if ($response->getStatusCode() != '200') {
                $this->error($error);
            }

            $result = json_decode($response->getBody());
            dd($result->response);
            if (isset($result->error)) {
                $this->error($error);
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
