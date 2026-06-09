@extends('layouts.app')

@section('title', 'My MCQs')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">
                        <i class="fas fa-list"></i> My MCQs
                    </h1>
                    <a href="{{ route('teacher.mcqs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New MCQ
                    </a>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($mcqs->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> You haven't created any MCQs yet.
                        <a href="{{ route('teacher.mcqs.create') }}" class="btn btn-sm btn-primary ms-2">
                            Create your first MCQ
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Question</th>
                                    <th>Subject</th>
                                    <th>Difficulty</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mcqs as $mcq)
                                    <tr>
                                        <td>{{ Str::limit($mcq->question, 50) }}</td>
                                        <td>{{ $mcq->subject->name ?? 'N/A' }}</td>
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
                                        <td>{{ $mcq->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="#" method="POST" style="display:inline;" onsubmit="return confirm('Delete this MCQ?')">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $mcqs->links() }}
                @endif
            </div>
        </div>
    </div>
</div>
@endsection