<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Cv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class BatchController extends Controller
{
    public function index()
    {
        $batches = Auth::user()->batches ?? collect(); // Ensure $batches is a collection
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
            'cvs.*' => 'required|file|mimes:pdf|max:2048',
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
}
