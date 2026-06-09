@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-3xl font-bold mb-6"><i class="fas fa-chalkboard-user"></i> Teacher Dashboard</h1>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="text-gray-600 text-sm font-medium">MCQs Created</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['total_mcqs'] ?? 0 }}</p>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h3 class="text-gray-600 text-sm font-medium">Approved</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['approved_mcqs'] ?? 0 }}</p>
                    </div>
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <h3 class="text-gray-600 text-sm font-medium">Pending</h3>
                        <p class="text-3xl font-bold text-orange-600">{{ $stats['pending_review'] ?? 0 }}</p>
                    </div>
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <h3 class="text-gray-600 text-sm font-medium">Students Taught</h3>
                        <p class="text-3xl font-bold text-purple-600">{{ $stats['total_students_taught'] ?? 0 }}</p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('teacher.mcqs.create') }}" class="bg-blue-50 p-4 border border-blue-200 rounded-lg hover:shadow-md transition">
                        <h3 class="font-semibold text-blue-700"><i class="fas fa-plus"></i> Create MCQ</h3>
                        <p class="text-gray-600 text-sm">Add new question</p>
                    </a>
                    <a href="{{ route('teacher.mcqs') }}" class="bg-white p-4 border rounded-lg hover:shadow-md transition">
                        <h3 class="font-semibold"><i class="fas fa-list"></i> My MCQs</h3>
                        <p class="text-gray-600 text-sm">View all your MCQs</p>
                    </a>
                    <a href="{{ route('teacher.results') }}" class="bg-white p-4 border rounded-lg hover:shadow-md transition">
                        <h3 class="font-semibold"><i class="fas fa-chart-bar"></i> Results</h3>
                        <p class="text-gray-600 text-sm">View student results</p>
                    </a>
                </div>

                <!-- Recent MCQs -->
                @if(isset($recentMcqs) && $recentMcqs->count() > 0)
                <div class="mt-8">
                    <h2 class="text-xl font-bold mb-4">Recent MCQs</h2>
                    <div class="space-y-2">
                        @foreach($recentMcqs as $mcq)
                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <div>
                                <p class="font-semibold">{{ Str::limit($mcq->question, 60) }}</p>
                                <p class="text-gray-600 text-sm">
                                    {{ $mcq->subject->name ?? 'N/A' }} • 
                                    <span class="capitalize">{{ $mcq->difficulty }}</span>
                                </p>
                            </div>
                            <div>
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    @if($mcq->status === 'active') bg-green-100 text-green-800
                                    @elseif($mcq->status === 'pending_review') bg-orange-100 text-orange-800
                                    @else bg-gray-100 text-gray-800
                                    @endif
                                ">
                                    {{ ucfirst(str_replace('_', ' ', $mcq->status)) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="mt-8 text-center text-gray-500">
                    <p>No MCQs created yet. Click "Create MCQ" to add your first question.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection