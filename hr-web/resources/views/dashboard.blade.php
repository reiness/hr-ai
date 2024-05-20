@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">Dashboard</h2>
            <p>Welcome to your dashboard!</p>
            <p>You can manage your batches and CVs here.</p>
            <a href="{{ route('batches.index') }}" class="mt-4 btn btn-primary">Manage Batches</a>
            <a href="{{ route('batches.processed') }}" class="mt-4 btn btn-primary">Processed Batches</a>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">Profile</h2>
            <p>Manage your profile and logout options.</p>
            <!-- Include Jetstream profile link here -->
            <a href="{{ route('profile.show') }}" class="mt-4 btn btn-secondary">Profile</a>
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </div>
    </div>
</div>
@endsection
