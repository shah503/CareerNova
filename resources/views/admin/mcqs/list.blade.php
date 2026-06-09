@extends('layouts.app')

@section('title', 'Manage MCQs')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold"><i class="fas fa-file-alt"></i> Manage MCQs</h1>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

                <!-- MCQs Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Question</th>
                                <th>Subject</th>
                                <th>Created By</th>
                                <th>Difficulty</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mcqs as $mcq)
                                <tr>
                                    <td>{{ Str::limit($mcq->question, 50) }}</td>
                                    <td>{{ $mcq->subject->name ?? 'N/A' }}</td>
                                    <td>{{ $mcq->creator->name ?? 'System' }}</td>
                                    <td>
                                        <span class="badge
                                            @if($mcq->difficulty === 'easy') bg-success
                                            @elseif($mcq->difficulty === 'medium') bg-warning
                                            @else bg-danger
                                            @endif
                                        ">
                                            {{ ucfirst($mcq->difficulty) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge
                                            @if($mcq->status === 'active') bg-success
                                            @elseif($mcq->status === 'pending_review') bg-warning
                                            @else bg-secondary
                                            @endif
                                        ">
                                            {{ ucfirst(str_replace('_', ' ', $mcq->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($mcq->status === 'pending_review')
                                        <form action="{{ route('admin.mcqs.verify', $mcq) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success" title="Verify">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        @endif
                                        <form action="{{ route('admin.mcqs.delete', $mcq) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this MCQ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No MCQs found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $mcqs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection