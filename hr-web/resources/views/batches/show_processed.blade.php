@extends('layouts.app')

@section('content')
<h2>Processed Batch: {{ $processedBatch->name }}</h2>
<h4>Sorted CVs</h4>
@if (empty($sortedCvs))
    <p>No CVs found.</p>
@else
    <ul class="list-group">
        @foreach ($sortedCvs as $cv)
            <li class="list-group-item">
                <a href="{{ Storage::url($cv['file_path']) }}" target="_blank">{{ basename($cv['file_path']) }}</a>
                <span class="badge badge-info">Score: {{ $cv['combined_score'] }}</span>
            </li>
        @endforeach
    </ul>
@endif

<a href="{{ route('batches.processed') }}" class="btn btn-secondary mt-4">Back to Processed Batches</a>
@endsection
