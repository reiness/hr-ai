@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-12">
    <h1 class="text-3xl font-bold mb-6">Batch: {{ $batch->name }}</h1>

    <!-- CVs Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">CVs</h2>
        
        @if ($batch->cvs->isEmpty())
            <p>No CVs found.</p>
        @else
            <div class="flex justify-between items-center mb-4">
                <a href="{{ route('batches.downloadAll', $batch->id) }}" class="py-2 px-4 gradient-btn2 text-white rounded-2xl hover:opacity-90 transition duration-300">Download All CVs</a>
                <button type="submit" form="delete-cvs-form" class="py-2 px-4 gradient-btn3 text-white rounded-2xl hover:opacity-90 transition duration-300">Delete Selected CVs</button>
            </div>

            <form action="{{ route('cvs.delete', $batch->id) }}" method="POST" id="delete-cvs-form">
                @csrf
                @method('DELETE')
                <div class="overflow-x-auto rounded-lg">
                    <table class="w-full table-auto border-collapse border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-3 px-6 text-left text-gray-600 font-semibold">File Name</th>
                                <th class="py-3 px-6 text-left text-gray-600 font-semibold">Select</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($batch->cvs as $cv)
                                <tr class="border-b border-gray-200">
                                    <td class="px-4 py-2">
                                        <a href="{{ Storage::url($cv->file_path) }}" target="_blank">{{ basename($cv->file_path) }}</a>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="checkbox" name="cvs[]" value="{{ $cv->id }}" class="cv-checkbox">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        @endif
    </div>

    <!-- Upload CVs Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">Upload CVs</h2>
        <form action="{{ route('batches.upload', $batch->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="cvs" class="block text-sm font-medium text-gray-700">Select CVs (PDF only, max 100)</label>
                <input type="file" id="cvs" name="cvs[]" multiple required class="mt-1 block w-full px-3 py-2 border border-gray-300 shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
            </div>
            <button type="submit" class="py-2 px-6 gradient-btn2 text-white rounded-2xl hover:opacity-90 transition duration-300">Upload</button>
        </form>
    </div>

    <!-- Process Batch Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">Rank Your CVs</h2>
        <form action="{{ route('batches.process', $batch->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="user_input" class="block text-sm font-medium text-gray-700">Input Keyword as your Rank base (1 word only!)</label>
                <input type="text" id="user_input" name="user_input" required class="mt-1 block w-full px-3 py-2 border border-gray-300 shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
            </div>
            <button type="submit" class="py-2 px-6 gradient-btn2 text-white rounded-2xl hover:opacity-90 transition duration-300">Process Batch</button>
        </form>
    </div>

    <!-- Back to Batches Link -->
    <a href="{{ route('batches.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded-2xl inline-block">Back to Batches</a>
</div>
@endsection
