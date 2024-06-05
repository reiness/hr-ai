@extends('layouts.app')

@section('content')
<h2>Summarized Top 3 CVs</h2>

@if (empty($summarization['summaries']))
    <p>No summaries available.</p>
@else
    <ul class="list-group">
        @foreach ($summarization['summaries'] as $summary)
            <li class="list-group-item">
                <p>{{ $summary }}</p>
            </li>
        @endforeach
    </ul>
@endif

<a href="{{ route('batches.processed') }}" class="btn btn-secondary mt-4">Back to Processed Batches</a>
@endsection
