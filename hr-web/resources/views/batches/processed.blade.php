@extends('layouts.app')

@section('content')
<h2>Processed Batches</h2>
@if ($processedBatches->isEmpty())
    <p>No processed batches found.</p>
@else
    <ul class="list-group">
        @foreach ($processedBatches as $processedBatch)
            <li class="list-group-item">
                <a href="{{ route('processedBatches.show', $processedBatch->id) }}">
                    {{ $processedBatch->batch->name }}_{{ $processedBatch->user_input }}
                </a>
            </li>
        @endforeach
    </ul>
@endif

<a href="{{ route('dashboard') }}" class="btn btn-secondary mt-4">Back to Dashboard</a>
@endsection
