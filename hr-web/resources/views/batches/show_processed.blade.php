@extends('layouts.app')

@section('content')
<h2>Processed Batch: {{ $processedBatch->name }}</h2>
<h4>Sorted CVs</h4>
@if (empty($sortedCvs))
    <p>No CVs found.</p>
@else
<a href="{{ route('processedBatches.downloadAll', $processedBatch->id) }}" class="btn btn-info mt-4">Download All CVs</a>
    <form action="{{ route('processedBatches.deleteCVs', $processedBatch->id) }}" method="POST" id="delete-cvs-form">
        @csrf
        @method('DELETE')
        <ul class="list-group">
            @foreach ($sortedCvs as $cv)
                <li class="list-group-item flex justify-between items-center">
                    <a href="{{ Storage::url($cv['file_path']) }}" target="_blank">{{ basename($cv['file_path']) }}</a>
                    <span class="badge badge-info">Score: {{ $cv['combined_score'] }}</span>
                    <input type="checkbox" name="cvs[]" value="{{ $cv['id'] }}" class="cv-checkbox">
                </li>
            @endforeach
        </ul>
        <button type="submit" class="btn btn-danger mt-4" id="delete-selected-cvs-btn">Delete Selected CVs</button>
    </form>
@endif

<a href="{{ route('batches.processed') }}" class="btn btn-secondary mt-4">Back to Processed Batches</a>

@endsection
