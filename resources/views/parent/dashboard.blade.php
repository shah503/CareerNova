@extends('layouts.app')

@section('title', 'Parent Dashboard')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-3xl font-bold mb-6"><i class="fas fa-user-tie"></i> Parent Dashboard</h1>

                <!-- Alert: Feature Coming Soon -->
                <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4">
                    <p class="text-blue-800">
                        <strong><i class="fas fa-info-circle"></i> Note:</strong> Parent-Child relationship feature is currently in development. 
                        Soon you'll be able to monitor your child's exam progress and performance analytics.
                    </p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="text-gray-600 text-sm font-medium">Total Children</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['total_children'] ?? 0 }}</p>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h3 class="text-gray-600 text-sm font-medium">Avg Child Score</h3>
                        <p class="text-3xl font-bold text-green-600">{{ round($stats['average_child_score'] ?? 0, 1) }}%</p>
                    </div>
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <h3 class="text-gray-600 text-sm font-medium">Total Tests</h3>
                        <p class="text-3xl font-bold text-purple-600">{{ $stats['total_tests'] ?? 0 }}</p>
                    </div>
                </div>

                <!-- Message for Empty State -->
                @if(($stats['total_children'] ?? 0) === 0)
                <div class="bg-gray-50 border rounded-lg p-8 text-center">
                    <i class="fas fa-user-check text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600 text-lg font-semibold">
                        No children linked to your account
                    </p>
                    <p class="text-gray-500 text-sm mt-2">
                        Contact administration to link your children to your parent account.
                    </p>
                </div>
                @else
                <!-- Children List would go here -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Child cards will be rendered here when feature is implemented -->
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection