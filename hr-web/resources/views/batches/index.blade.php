@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Left Column: List of Batches -->
        <div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Batches</h2>

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

    @if ($batches->isEmpty())
        <p>No batches found.</p>
    @else
        <div class="overflow-x-auto rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 ">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Batch Name</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Select</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($batches as $batch)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $batch->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                <a href="{{ route('batches.show', $batch->id) }}" class="text-blue-600 hover:underline">View</a>
                                <form action="{{ route('batches.destroy', $batch->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this batch?');" class="ml-2 text-red-500 hover:text-red-700">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

        <!-- Right Column: Create New Batch Form -->
        <div class="bg-white p-6 border-2 border-dashed border-gray-300 rounded-lg">
            <h2 class="text-2xl font-bold mb-4">Create New Batch</h2>
            <form action="{{ route('batches.create') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="batchName" class="block text-sm font-medium text-gray-700 ">Batch Name</label>
                    <input type="text" id="batchName" name="name" required class="mt-1 block w-full px-3 py-2 border rounded-xl border-gray-300 shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                </div>
                <button type="submit" class="inline-block w-full  py-2 px-3 gradient-btn2 text-white rounded-2xl shadow-lg hover:opacity-90 transition duration-300 text-center">
                    Create
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
