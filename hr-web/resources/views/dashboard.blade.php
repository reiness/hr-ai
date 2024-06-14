@extends('layouts.app')

@section('content')
<div class="container mx-auto py-12">

  <!-- Welcome Section -->
  <div class="mb-12 text-center">
    <h1 class="text-4xl font-bold mb-1 text-gray-800">Welcome to your <span class="gradient-text">Dashboard!</span></h1>
    <p class="text-xl font-medium text-gray-600">You can manage your batches and projects here.</p>
  </div>

  <!-- Action Cards Section -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 flex items-center hover:shadow-lg transition-shadow duration-200 ease-in-out">
      <div class="mr-4 flex-shrink-0">
        <svg class="w-12 h-12 text-pink-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.75v14.5m7.25-7.25H4.75"></path>
        </svg>
      </div>
      <div>
        <h2 class="text-2xl font-semibold mb-1 text-gray-800">Create New Batch</h2>
        <p class="text-gray-600 mb-4">Start a new batch by adding CVs here.</p>
        <a href="{{ route('batches.index') }}" class="inline-block w-full gradient-btn text-white py-2 px-3 rounded-2xl shadow-lg hover:opacity-90 transition duration-300 text-center">Create New Batch</a>
      </div>
    </div>
    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 flex items-center hover:shadow-lg transition-shadow duration-200 ease-in-out">
      <div class="mr-4 flex-shrink-0">
        <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
        </svg>
      </div>
      <div>
        <h2 class="text-2xl font-semibold mb-1 text-gray-800">Rank CVs from Existing Batch</h2>
        <p class="text-gray-600 mb-4">Rank and manage CVs from your existing batches.</p>
        <a href="{{ route('batches.processed') }}" class="inline-block w-full  py-2 px-3 gradient-btn2 text-white rounded-2xl shadow-lg hover:opacity-90 transition duration-300 text-center">Rank CVs</a>
      </div>
    </div>
  </div>

  <!-- Previous Batches Section -->
  <div class="mb-12">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Previous Batches</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <!-- Example Batch Card -->
      <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow duration-200 ease-in-out">
        <h3 class="text-xl font-semibold mb-2 text-gray-800">Batch #1</h3>
        <p class="text-gray-600 mb-4">Processed on: 2023-06-01</p>
        <a href="{{ route('batches.processed') }}" class="text-blue-500 hover:underline">View Details</a>
      </div>
      <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow duration-200 ease-in-out">
        <h3 class="text-xl font-semibold mb-2 text-gray-800">Batch #2</h3>
        <p class="text-gray-600 mb-4">Processed on: 2023-06-01</p>
        <a href="{{ route('batches.processed') }}" class="text-blue-500 hover:underline">View Details</a>
      </div>
      <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow duration-200 ease-in-out">
        <h3 class="text-xl font-semibold mb-2 text-gray-800">Batch #3</h3>
        <p class="text-gray-600 mb-4">Processed on: 2023-06-01</p>
        <a href="{{ route('batches.processed') }}" class="text-blue-500 hover:underline">View Details</a>
      </div>
    </div>
  </div>

  <!-- Previous Processed Batches Section -->
  <div class="mb-12">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Previous Processed Batches</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <!-- Example Processed Batch Card -->
      <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow duration-200 ease-in-out">
        <h3 class="text-xl font-semibold mb-2 text-gray-800">Processed Batch 1</h3>
        <p class="text-gray-600 mb-4">Processed on: 2023-06-01</p>
        <a href="{{ route('batches.processed') }}" class="text-blue-500 hover:underline">View Details</a>
      </div>
      <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow duration-200 ease-in-out">
        <h3 class="text-xl font-semibold mb-2 text-gray-800">Processed Batch 2</h3>
        <p class="text-gray-600 mb-4">Processed on: 2023-06-01</p>
        <a href="{{ route('batches.processed') }}" class="text-blue-500 hover:underline">View Details</a>
      </div>
      <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow duration-200 ease-in-out">
        <h3 class="text-xl font-semibold mb-2 text-gray-800">Processed Batch 3</h3>
        <p class="text-gray-600 mb-4">Processed on: 2023-06-01</p>
        <a href="{{ route('batches.processed') }}" class="text-blue-500 hover:underline">View Details</a>
      </div>
    </div>
  </div>

</div>
@endsection
