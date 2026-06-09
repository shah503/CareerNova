@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-3xl font-bold mb-6"><i class="fas fa-chart-line"></i> Your Dashboard</h1>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="text-gray-600 text-sm font-medium">Total Tests</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['total_tests'] ?? 0 }}</p>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h3 class="text-gray-600 text-sm font-medium">Completed</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['completed_tests'] ?? 0 }}</p>
                    </div>
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <h3 class="text-gray-600 text-sm font-medium">Average Score</h3>
                        <p class="text-3xl font-bold text-purple-600">{{ round($stats['average_score'] ?? 0, 1) }}%</p>
                    </div>
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <h3 class="text-gray-600 text-sm font-medium">Tests Passed</h3>
                        <p class="text-3xl font-bold text-orange-600">{{ $stats['tests_passed'] ?? 0 }}</p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('exam.select-subject') }}" class="bg-blue-50 p-4 border border-blue-200 rounded-lg hover:shadow-md transition">
                        <h3 class="font-semibold text-blue-700"><i class="fas fa-pencil-alt"></i> Take a Test</h3>
                        <p class="text-gray-600 text-sm">Start a new exam</p>
                    </a>
                    <a href="{{ route('student.results') }}" class="bg-white p-4 border rounded-lg hover:shadow-md transition">
                        <h3 class="font-semibold"><i class="fas fa-list"></i> My Results</h3>
                        <p class="text-gray-600 text-sm">View past test results</p>
                    </a>
                    <a href="{{ route('student.analytics') }}" class="bg-white p-4 border rounded-lg hover:shadow-md transition">
                        <h3 class="font-semibold"><i class="fas fa-chart-bar"></i> Analytics</h3>
                        <p class="text-gray-600 text-sm">View your performance</p>
                    </a>
                </div>

                <!-- Recent Tests -->
                @if(isset($recentSessions) && $recentSessions->count() > 0)
                <div class="mt-8">
                    <h2 class="text-xl font-bold mb-4">Recent Tests</h2>
                    <div class="space-y-2">
                        @foreach($recentSessions as $session)
                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <div>
                                <p class="font-semibold">{{ $session->mcq->question ?? 'Test Session' }}</p>
                                <p class="text-gray-600 text-sm">{{ $session->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-blue-600">{{ round($session->percentage ?? 0, 1) }}%</p>
                                <p class="text-sm {{ ($session->is_passed ?? false) ? 'text-green-600' : 'text-red-600' }}">
                                    {{ ($session->is_passed ?? false) ? '✓ Passed' : '✗ Failed' }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="mt-8 text-center text-gray-500">
                    <p>No tests taken yet. Start with a test to see your progress here.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection