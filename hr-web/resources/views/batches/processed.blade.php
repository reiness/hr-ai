@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <h2 class="text-3xl font-bold mb-6">Processed Batches</h2>

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if ($processedBatches->isEmpty())
        <div class="bg-gray-100 border border-gray-200 px-4 py-3 rounded mb-4">
            <p class="text-gray-700">No processed batches found.</p>
        </div>
    @else
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <ul class="divide-y divide-gray-200">
                @foreach ($processedBatches as $processedBatch)
                    <li class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-blue-600 font-semibold mr-2">{{ $processedBatch->batch->name }}_{{ $processedBatch->user_input }}</span>
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('processedBatches.show', $processedBatch->id) }}" class="text-blue-600 hover:underline">Details</a>
                            <form action="{{ route('processedBatches.destroy', $processedBatch->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this processed batch?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <a href="{{ route('dashboard') }}" class="inline-block mt-6 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-300 ease-in-out">
        Back to Dashboard
    </a>
</div>
@endsection
