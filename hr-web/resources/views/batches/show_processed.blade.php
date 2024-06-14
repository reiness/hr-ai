@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <h2 class="text-3xl font-bold mb-8">Processed Batch: {{ $processedBatch->name }}</h2>

    @if (empty($sortedCvs))
        <p class="text-gray-600">No CVs found.</p>
    @else
    <div class="flex justify-between items-center mb-3">
        <h4 class="text-2xl font-semibold">Ranked CVs</h4>
        <div class="flex space-x-4">
            <a href="{{ route('processedBatches.downloadAll', $processedBatch->id) }}" class="gradient-btn2 text-white py-2 px-4 rounded-2xl hover:opacity-90 transition duration-300">Download All CVs</a>
            <a href="{{ route('processedBatches.summarize', $processedBatch->id) }}" class="text-[#38A5FF] py-2 px-4 border-2 rounded-2xl">Summarize Top 3</a>
        </div>
    </div>
        
        <form action="{{ route('processedBatches.deleteCVs', $processedBatch->id) }}" method="POST" id="delete-cvs-form">
            @csrf
            @method('DELETE')
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
                    <thead class="bg-gray-100">
                        <tr>
                            <!-- <th class="py-3 px-6 text-left text-gray-600 font-semibold">Rank</th> -->
                            <th class="py-3 px-6 text-left text-gray-600 font-semibold">CV</th>
                            <th class="py-3 px-6 text-left text-gray-600 font-semibold">Rank</th>
                            <th class="py-3 px-6 text-center text-gray-600 font-semibold">Select</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($sortedCvs as $index => $cv)
                            <tr>
                                <!-- <td class="py-4 px-6 text-gray-800 font-semibold">
                                    {{ $index + 1 }}
                                </td> -->
                                <td class="py-4 px-6 text-blue-600 hover:underline">
                                    <a href="{{ Storage::url($cv['file_path']) }}" target="_blank">{{ basename($cv['file_path']) }}</a>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 text-sm font-semibold rounded">{{ $cv['combined_score'] }}</span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <input type="checkbox" name="cvs[]" value="{{ $cv['id'] }}" class="cv-checkbox">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button type="submit" class="py-2 px-4 gradient-btn3 text-white rounded-2xl hover:opacity-90 transition duration-300 mt-4" id="delete-selected-cvs-btn">Delete Selected CVs</button>
        </form>
    @endif

    <a href="{{ route('batches.processed') }}" class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg mt-12 focus:outline-none focus:shadow-outline">
        Back to Processed Batches
    </a>
</div>
@endsection
