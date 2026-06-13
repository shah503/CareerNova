@extends('layouts.app')

@section('title', 'Analytics')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-3xl font-bold mb-6"><i class="fas fa-chart-line"></i> Your Performance Analytics</h1>

                {{-- Analytics Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 border rounded-lg p-4">
                        <h3 class="text-sm text-gray-600">Total Tests</h3>
                        <p class="text-3xl font-bold text-blue-600">
                            {{ $analytics['total_tests'] ?? 0 }}
                        </p>
                    </div>

                <div class="bg-green-50 border rounded-lg p-4">
                        <h3 class="text-sm text-gray-600">Completed Tests</h3>
                        <p class="text-3xl font-bold text-green-600">
                            {{ $analytics['completed_tests'] ?? 0 }}
                        </p>
                    </div>

                    <div class="bg-purple-50 border rounded-lg p-4">
                        <h3 class="text-sm text-gray-600">Average Score</h3>
                        <p class="text-3xl font-bold text-purple-600">
                            {{ round($analytics['average_score'] ?? 0, 1) }}%
                        </p>
                    </div>

                    <div class="bg-orange-50 border rounded-lg p-4">
                        <h3 class="text-sm text-gray-600">Tests Passed</h3>
                        <p class="text-3xl font-bold text-orange-600">
                            {{ $analytics['tests_passed'] ?? 0 }}
                        </p>
                    </div>
                </div>

                {{-- Subject Performance --}}
                <div class="bg-white border rounded-lg p-4 mb-6">
                    <h2 class="text-xl font-bold mb-4">Subject Performance</h2>

                    @if(!empty($analytics['subject_performance']))
                        <table class="table-auto w-full">
                            <thead>
                                <tr>
                                    <th class="text-left">Subject</th>
                                    <th class="text-left">Average %</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($analytics['subject_performance'] as $subjectId => $performance)
                                <tr>
                                    <td>
                                        {{ \App\Models\Subject::find($subjectId)?->name }}
                                    </td>
                                    <td>
                                        {{ $performance['average'] }}%
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            </table>
                    @endif
                </div>

                {{-- Performance Trend --}}
                <div class="bg-white border rounded-lg p-4">
                    <h2 class="text-xl font-bold mb-4">Recent Test Performance</h2>

                    @if($performanceTrend->count())
                        <table class="table-auto w-full">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Subject</th>
                                    <th>Score</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($performanceTrend as $session)
                                <tr>
                                        <td>{{ $session->created_at->format('M d, Y') }}</td>
                                    <td>{{ $session->subject->name ?? 'N/A' }}</td>
                                        <td>{{ $session->correct_answers }}/{{ $session->total_questions }}</td>
                                    <td>{{ round($session->percentage, 1) }}%</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500">No performance data available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection