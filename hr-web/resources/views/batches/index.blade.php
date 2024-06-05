@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">Batches</h2>
            
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

            @if ($batches->isEmpty())
                <p>No batches found.</p>
            @else
                <ul class="list-group">
                    @foreach ($batches as $batch)
                        <li class="list-group-item flex justify-between items-center">
                            <a href="{{ route('batches.show', $batch->id) }}">{{ $batch->name }}</a>
                            <form action="{{ route('batches.destroy', $batch->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this batch?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">Create New Batch</h2>
            <form action="{{ route('batches.create') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="batchName">Batch Name</label>
                    <input type="text" class="form-control" id="batchName" name="name" required>
                </div>
                <button type="submit" class="btn btn-primary mt-4">Create</button>
            </form>
        </div>
    </div>
</div>
@endsection
