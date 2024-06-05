@extends('layouts.app')

@section('content')
<h2>Processed Batches</h2>

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if ($processedBatches->isEmpty())
    <p>No processed batches found.</p>
@else
    <ul class="list-group">
        @foreach ($processedBatches as $processedBatch)
            <li class="list-group-item flex justify-between items-center">
                <a href="{{ route('processedBatches.show', $processedBatch->id) }}">
                    {{ $processedBatch->batch->name }}_{{ $processedBatch->user_input }}
                </a>
                <form action="{{ route('processedBatches.destroy', $processedBatch->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this processed batch?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </li>
        @endforeach
    </ul>
@endif

<a href="{{ route('dashboard') }}" class="btn btn-secondary mt-4">Back to Dashboard</a>
@endsection
