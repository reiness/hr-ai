@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <h2 class="text-3xl font-bold mb-1 text-center">Summarized <span class="gradient-text">Top 3 CVs</span></h2>

    @if (empty($summarization) || empty($summarization['evaluation']))
        <div class="bg-gray-100 border border-gray-200 px-4 py-3 rounded mb-4 text-center">
            <p class="text-gray-700">No summaries available.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white mt-8 p-6 shadow-md rounded-lg">
                <thead class="gradient-bg text-white rounded-lg">
                    <tr>
                        <th class="py-2 px-4 text-left">Category</th>
                        @foreach ($summarization['evaluation'] as $name => $details)
                            <th class="py-2 px-4 text-left">{{ $name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($summarization['evaluation'][array_key_first($summarization['evaluation'])] as $category => $data)
                        <tr class="border-t">
                            <td class="py-2 px-4 font-medium text-gray-800">{{ str_replace('_', ' ', ucfirst($category)) }}</td>
                            @foreach ($summarization['evaluation'] as $name => $details)
                                <td class="py-2 px-4 text-gray-700">
                                    <strong>Score:</strong> {{ $details[$category]['score'] }}<br>
                                    <strong>Feedback:</strong> {{ $details[$category]['feedback'] }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if (!empty($summarization['comparison']) || !empty($summarization['overall_comparison']))
            <div class="mt-8 p-6 bg-white shadow-md rounded-lg">
                <h3 class="text-2xl font-semibold mb-4 text-gray-700">Comparison Summary</h3>
                
                @if (!empty($summarization['comparison']['strengths']) && is_array($summarization['comparison']['strengths']))
                    <div class="mb-4">
                        <h4 class="text-xl font-medium text-gray-800">Strengths</h4>
                        <ul class="list-disc pl-5 text-gray-700">
                            @foreach ($summarization['comparison']['strengths'] as $name => $strength)
                                <li><strong>{{ $name }}:</strong> {{ $strength }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (!empty($summarization['comparison']['areas_for_improvement']) && is_array($summarization['comparison']['areas_for_improvement']))
                    <div class="mb-4">
                        <h4 class="text-xl font-medium text-gray-800">Areas for Improvement</h4>
                        <ul class="list-disc pl-5 text-gray-700">
                            @foreach ($summarization['comparison']['areas_for_improvement'] as $name => $improvement)
                                <li><strong>{{ $name }}:</strong> {{ $improvement }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (!empty($summarization['comparison']['overall_comparison']))
                    <div class="mb-4">
                        <h4 class="text-xl font-medium text-gray-800">Overall Comparison</h4>
                        <p class="text-gray-700">{{ $summarization['comparison']['overall_comparison'] }}</p>
                    </div>
                @elseif (!empty($summarization['overall_comparison']))
                    <div class="mb-4">
                        <h4 class="text-xl font-medium text-gray-800">Overall Comparison</h4>
                        <p class="text-gray-700">{{ $summarization['overall_comparison'] }}</p>
                    </div>
                @endif
            </div>
        @endif
    @endif

    <div class="mt-4 mb-2 p-2 gradient-btn2 py-2 px-4 rounded-lg text-center transition duration-300 ease-in-out">
    <a href="https://forms.gle/pnBMCMKxc62cUR8q9" target="_blank" class="block text-white no-underline">
        Kindly rate our website performance
    </a>
</div>

    <a href="{{ route('batches.processed') }}" class="inline-block mt-6 gradient-btn text-white py-2 px-4 rounded-lg transition duration-300 ease-in-out">
        Back to Processed Batches
    </a>
</div>
@endsection
