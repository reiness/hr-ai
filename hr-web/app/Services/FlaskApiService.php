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
        // dd($batch);
        try {
            $response = $this->client->post('/process', [
                'json' => $batch,
            ]);

            if ($response->getStatusCode() == 200) {
                $result = json_decode($response->getBody()->getContents(), true);
                // dd($result);
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

    public function processGemini($data)
    {
        try {
            $response = $this->client->post('/gemini', [
                'json' => $data,
                'timeout' => 300,
            ]);
    
            if ($response->getStatusCode() == 200) {
                $result = $response->getBody()->getContents();
                Log::debug('Flask API response: ' . $result);
                return $result;
            } else {
                Log::error('Flask API returned an error: ' . $response->getStatusCode());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Error processing Gemini request: ' . $e->getMessage());
            return null;
        }
    }
    
}

