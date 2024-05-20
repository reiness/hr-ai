@extends('layouts.app')

@section('content')
<h2>Processed Batches</h2>
@if ($batches->isEmpty())
    <p>No processed batches found.</p>
@else
    <ul class="list-group">
        @foreach ($batches as $batch)
            <li class="list-group-item">
                <a href="{{ route('batches.show', $batch->id) }}">{{ $batch->name }}</a>
            </li>
        @endforeach
    </ul>
@endif

<a href="{{ route('dashboard') }}" class="btn btn-secondary mt-4">Back to Dashboard</a>
@endsection
