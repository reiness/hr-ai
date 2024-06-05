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
use ZipArchive;

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
            'cvs.*' => 'required|file|mimes:pdf|max:102400', // 100mb
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

    public function destroy($id)
    {
        $batch = Auth::user()->batches()->with('cvs')->findOrFail($id);
    
        if ($batch->cvs->isNotEmpty()) {
            return redirect()->route('batches.index')->with('error', 'Cannot delete batch with CVs.');
        }
    
        $batch->delete();
    
        return redirect()->route('batches.index')->with('success', 'Batch deleted successfully.');
    }

    public function deleteCVs(Request $request, $batchId)
    {
        $batch = Auth::user()->batches()->findOrFail($batchId);

        $cvIds = $request->input('cvs');
        if (!$cvIds) {
            return redirect()->route('batches.show', $batchId)->with('error', 'No CVs selected.');
        }

        foreach ($cvIds as $cvId) {
            $cv = Cv::where('batch_id', $batchId)->findOrFail($cvId);
            Storage::disk('public')->delete($cv->file_path);
            $cv->delete();
        }

        return redirect()->route('batches.show', $batchId)->with('success', 'Selected CVs deleted successfully.');
    }

    public function downloadAll($batchId)
    {
        $batch = Auth::user()->batches()->with('cvs')->findOrFail($batchId);
    
        if ($batch->cvs->isEmpty()) {
            return redirect()->route('batches.show', $batchId)->with('error', 'No CVs found in this batch.');
        }
    
        // Sanitize the batch name for use as a filename
        $safeBatchName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $batch->name);
        $zipFileName = $safeBatchName . '_cvs.zip';
        $zipPath = storage_path('app/public/' . $zipFileName);
    
        $zip = new ZipArchive;
    
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($batch->cvs as $cv) {
                $zip->addFile(storage_path('app/public/' . $cv->file_path), basename($cv->file_path));
            }
            $zip->close();
        }
    
        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    // New function to delete processed batches
    public function destroyProcessedBatch($id)
    {
        $processedBatch = ProcessedBatch::with('batch')->findOrFail($id);
    
        // Check if the processed batch has associated files (based on your application logic)
        // For example, checking if the result is not empty
        if (!empty($processedBatch->result)) {
            return redirect()->route('batches.processed')->with('error', 'Cannot delete processed batch with files.');
        }
    
        $processedBatch->delete();
    
        return redirect()->route('batches.processed')->with('success', 'Processed batch deleted successfully.');
    }

    // Function to delete selected CVs from a processed batch
    public function deleteProcessedCVs(Request $request, $processedBatchId)
    {
        $processedBatch = ProcessedBatch::with('batch')->findOrFail($processedBatchId);

        $cvIds = $request->input('cvs');
        if (!$cvIds) {
            return redirect()->route('processedBatches.show', $processedBatchId)->with('error', 'No CVs selected.');
        }

        $sortedCvs = json_decode($processedBatch->result, true);
        $remainingCvs = [];

        foreach ($sortedCvs as $cv) {
            if (!in_array($cv['id'], $cvIds)) {
                $remainingCvs[] = $cv;
            } else {
                Storage::disk('public')->delete($cv['file_path']);
            }
        }

        // Re-save the sorted CVs without the deleted ones
        $processedBatch->result = json_encode($remainingCvs);
        $processedBatch->save();

        return redirect()->route('processedBatches.show', $processedBatchId)->with('success', 'Selected CVs deleted successfully.');
    }

    // Function to download all CVs from a processed batch
    public function downloadAllProcessed($processedBatchId)
    {
        $processedBatch = ProcessedBatch::with('batch')->findOrFail($processedBatchId);
        $sortedCvs = json_decode($processedBatch->result, true);

        if (empty($sortedCvs)) {
            return redirect()->route('processedBatches.show', $processedBatchId)->with('error', 'No CVs found in this batch.');
        }

        // Sanitize the processed batch name for use as a filename
        $safeBatchName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $processedBatch->name);
        $zipFileName = $safeBatchName . '_cvs.zip';
        $zipPath = storage_path('app/public/' . $zipFileName);

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($sortedCvs as $cv) {
                $zip->addFile(storage_path('app/public/' . $cv['file_path']), basename($cv['file_path']));
            }
            $zip->close();
        }

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }
}
