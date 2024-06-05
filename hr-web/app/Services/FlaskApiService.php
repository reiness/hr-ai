<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class FlaskApiService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => env('FLASK_API_BASE_URL'),
        ]);
    }

    public function processBatch($batch)
    {
        try {
            $response = $this->client->post('/process', [
                'json' => $batch,
            ]);

            if ($response->getStatusCode() == 200) {
                $result = json_decode($response->getBody()->getContents(), true);
                Log::debug('Flask API response: ' . json_encode($result));
                return $result;
            } else {
                Log::error('Flask API returned an error: ' . $response->getStatusCode());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Error processing batch: ' . $e->getMessage());
            return null;
        }
    }
    
}
