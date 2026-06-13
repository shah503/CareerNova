@extends('layouts.app')

@section('title', 'Student Results')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h1 class="text-3xl font-bold mb-6">
                    <i class="fas fa-chart-bar"></i> Student Results
                </h1>

                @if ($results->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No student results yet. 
                        Students will appear here after taking your exams.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Student Name</th>
                                    <th>Subject</th>
                                    <th>Score</th>
                                    <th>Percentage</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($results as $result)
                                    <tr>
                                        <td>{{ $result->user->name ?? 'N/A' }}</td>
                                        <td>{{ $result->mcq->subject->name ?? $result->subject->name ?? 'N/A' }}</td>
                                        <td>{{ $result->correct_answers }}/{{ $result->total_questions }}</td>
                                        <td>
                                            <strong>{{ round($result->percentage, 2) }}%</strong>
                                        </td>
                                        <td>
                                            <span class="badge {{ $result->percentage >= 50 ? 'bg-success' : 'bg-danger' }}">
                                                {{ $result->percentage >= 50 ? 'Passed' : 'Failed' }}
                                            </span>
                                        </td>
                                        <td>{{ $result->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('teacher.result.show', $result->id) }}" class="btn btn-sm btn-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $results->links() }}
                @endif
            </div>
        </div>
    </div>
</div>
@endsection