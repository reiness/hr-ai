@extends('layouts.app')

@section('content')
<h2>Batch: {{ $batch->name }}</h2>
<h4>CVs</h4>
@if ($batch->cvs->isEmpty())
    <p>No CVs found.</p>
@else
<a href="{{ route('batches.downloadAll', $batch->id) }}" class="btn btn-info mt-4">Download All CVs</a>
    <form action="{{ route('cvs.delete', $batch->id) }}" method="POST" id="delete-cvs-form">
        @csrf
        @method('DELETE')
        <ul class="list-group">
            @foreach ($batch->cvs as $cv)
                <li class="list-group-item flex justify-between items-center">
                    <a href="{{ Storage::url($cv->file_path) }}" target="_blank">{{ basename($cv->file_path) }}</a>
                    <input type="checkbox" name="cvs[]" value="{{ $cv->id }}" class="cv-checkbox">
                </li>
            @endforeach
        </ul>
        <button type="submit" class="btn btn-danger mt-4" id="delete-selected-cvs-btn">Delete Selected CVs</button>
    </form>
@endif

<h2 class="mt-4">Upload CVs</h2>
<form action="{{ route('batches.upload', $batch->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label for="cvs">Select CVs (PDF only, max 100)</label>
        <input type="file" class="form-control-file" id="cvs" name="cvs[]" multiple required>
    </div>
    <button type="submit" class="btn btn-primary">Upload</button>
</form>

<h2 class="mt-4">Process Batch</h2>
<form action="{{ route('batches.process', $batch->id) }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="user_input">Processed Batch Name</label>
        <textarea class="form-control" id="user_input" name="user_input" rows="1" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Process Batch</button>
</form>

<a href="{{ route('batches.index') }}" class="btn btn-secondary mt-4">Back to Batches</a>
@endsection
