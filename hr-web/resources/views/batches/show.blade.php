@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold mb-4">Batch: {{ $batch->name }}</h2>
        <h4 class="text-xl font-semibold mb-4">CVs</h4>
        @if ($batch->cvs->isEmpty())
            <p>No CVs found.</p>
        @else
            <ul class="list-group">
                @foreach ($batch->cvs as $cv)
                    <li class="list-group-item">
                        <a href="{{ Storage::url($cv->file_path) }}" target="_blank">{{ basename($cv->file_path) }}</a>
                    </li>
                @endforeach
            </ul>
        @endif

        <h2 class="text-2xl font-semibold mt-6 mb-4">Upload CVs</h2>
        <form action="{{ route('batches.upload', $batch->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="cvs">Select CVs (PDF only, max 100)</label>
                <input type="file" class="form-control-file" id="cvs" name="cvs[]" multiple required>
            </div>
            <button type="submit" class="btn btn-primary mt-4">Upload</button>
        </form>

        <a href="{{ route('batches.index') }}" class="btn btn-secondary mt-4">Back to Batches</a>
    </div>
</div>
@endsection
