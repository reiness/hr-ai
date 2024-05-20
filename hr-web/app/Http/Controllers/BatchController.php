<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Cv;
use App\Models\ProcessedBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Services\FlaskApiService;
use Illuminate\Support\Facades\Log; 

class BatchController extends Controller
{
    protected $flaskApiService;

    public function __construct(FlaskApiService $flaskApiService)
    {
        $this->flaskApiService = $flaskApiService;
    }

    public function index()
    {
        $batches = Auth::user()->batches ?? collect();
        return view('batches.index', compact('batches'));
    }

    public function createBatch(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $batch = Auth::user()->batches()->create([
            'name' => $request->name,
        ]);

        return redirect()->route('batches.index');
    }

    public function uploadCVs(Request $request, $batchId)
    {
        $request->validate([
            'cvs.*' => 'required|file|mimes:pdf|max:102400 ', //100mb 
        ]);

        $batch = Auth::user()->batches()->findOrFail($batchId);

        if ($batch->cvs()->count() + count($request->file('cvs')) > 100) {
            return redirect()->back()->withErrors(['error' => 'Batch can only contain up to 100 CVs']);
        }

        foreach ($request->file('cvs') as $cv) {
            $path = $cv->store('cvs/' . $batch->id, 'public');

            Cv::create([
                'batch_id' => $batch->id,
                'file_path' => $path,
            ]);
        }

        return redirect()->route('batches.show', $batchId);
    }

    public function showBatch($batchId)
    {
        $batch = Auth::user()->batches()->with('cvs')->findOrFail($batchId);
        return view('batches.show', compact('batch'));
    }

    public function processedBatches()
    {
        $processedBatches = ProcessedBatch::with('batch')->whereHas('batch', function ($query) {
            $query->where('user_id', Auth::id());
        })->get();

        return view('batches.processed', compact('processedBatches'));
    }

    public function showProcessedBatch($processedBatchId)
    {
        $processedBatch = ProcessedBatch::with('batch')->findOrFail($processedBatchId);
        $sortedCvs = json_decode($processedBatch->result, true);
        
        return view('batches.show_processed', compact('processedBatch', 'sortedCvs'));
    }

    public function processBatch(Request $request, $batchId)
    {
        $batch = Batch::with('cvs')->findOrFail($batchId);
    
        // Prepare batch data for processing
        $batchData = [
            'batch_id' => $batch->id,
            'cvs' => $batch->cvs->map(function ($cv) {
                return [
                    'id' => $cv->id,
                    'file_path' => $cv->file_path, // Send relative path
                ];
            })->toArray(),
            'user_input' => $request->input('user_input'), // Use user input as processed batch name
        ];
    
        // Debugging: Log the batch data being sent to Flask
        Log::debug('Batch data being sent to Flask:', $batchData);
    
        // Call Flask API to process the batch
        $result = $this->flaskApiService->processBatch($batchData);
    
        if ($result) {
            // Save processed results
            ProcessedBatch::create([
                'batch_id' => $batch->id,
                'name' => $batch->name . '_' . $request->input('user_input'), // Combine original batch name and user input
                'user_input' => $request->input('user_input'), // Save the user input
                'status' => 'processed',
                'result' => json_encode($result['sorted_cvs']),
            ]);
    
            return redirect()->route('batches.processed')->with('success', 'Batch processed successfully.');
        } else {
            return redirect()->route('batches.show', $batchId)->with('error', 'Failed to process batch.');
        }
    }
    

}
