<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

class TestingController extends Controller
{
    public function predict(Request $request)
    {
        // Extract data from request
        $data = $request->all();

        // Prepare data for sending
        $formattedData = [
            'A' => $data['A'],
            'B' => $data['B'],
            'C' => $data['C']
        ];

        
        // dd($data['pdfs']);

        // Send request to Python script
        $client = new Client();
        $response = $client->post('http://localhost:5000/predict', [
            'json' => $formattedData,
        ]);

        // Check for successful response
        if ($response->getStatusCode() === 200) {
            $responseData = json_decode($response->getBody(), true);
            // Process and display results
            return view('sum', ['prediction' => $responseData['prediction']]);
        } else {
            // Handle error
            return abort(500, 'Error communicating with ML model');
        }
    }
}