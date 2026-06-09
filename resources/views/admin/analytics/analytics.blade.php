@extends('layouts.app')

@section('title', 'Analytics')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-3xl font-bold mb-6"><i class="fas fa-chart-bar"></i> System Analytics</h1>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="text-gray-600 text-sm font-medium">Total Tests</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['total_tests'] ?? 0 }}</p>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h3 class="text-gray-600 text-sm font-medium">Total Students</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['total_students'] ?? 0 }}</p>
                    </div>
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <h3 class="text-gray-600 text-sm font-medium">Average Score</h3>
                        <p class="text-3xl font-bold text-purple-600">{{ round($stats['avg_score'] ?? 0, 1) }}%</p>
                    </div>
                </div>

                <div class="mt-8 text-center text-gray-500">
                    <p>Detailed charts and graphs will be added here.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection