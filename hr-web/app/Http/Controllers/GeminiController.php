<?php

namespace App\Http\Controllers;

use App\Models\ProcessedBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GeminiController extends Controller
{
    public function summarizeTop3($id)
    {
        $processedBatch = ProcessedBatch::with('batch')->findOrFail($id);
        $sortedCvs = json_decode($processedBatch->result, true);

        // Get the top 3 CVs
        $top3Cvs = array_slice($sortedCvs, 0, 3);

        // Prepare data to send to Flask
        $data = [
            'cvs' => $top3Cvs,
            'api_key' => env('GEMINI_API_KEY')
        ];

        // Send data to Flask endpoint
        // $response = Http::post('http://localhost:5000/gemini', $data);
        $response = Http::timeout(300)->post('http://localhost:5000/gemini', $data);

        // Check for errors
        if ($response->failed()) {
            return redirect()->back()->with('error', 'Failed to summarize the top 3 CVs.');
        }

        // Get the summarization result
        $summarization = $response->json();

        // Pass the summarization result to the view
        return view('batches.summarization', compact('summarization'));
    }
}
