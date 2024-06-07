<?php

namespace App\Http\Controllers;

use App\Models\ProcessedBatch;
use Illuminate\Http\Request;
use App\Services\FlaskApiService;
use Illuminate\Support\Facades\Storage;

class GeminiController extends Controller
{
    protected $flaskApiService;

    public function __construct(FlaskApiService $flaskApiService)
    {
        $this->flaskApiService = $flaskApiService;
    }

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

        // Send data to Flask endpoint using the FlaskApiService
        $response = $this->flaskApiService->processGemini($data);

        // Check for errors
        if ($response === null) {
            return redirect()->back()->with('error', 'Failed to summarize the top 3 CVs.');
        }

        // Get the summarization result
        $summarization = $response;

        // Pass the summarization result to the view
        return view('batches.summarization', compact('summarization'));
    }
}
