@extends('layouts.app')

@section('title', 'Manage Subjects')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-book"></i> Manage Subjects</h2>
                <a href="/admin/subjects/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Subject
                </a>
            </div>
        </div>
    </div>

    <!-- Subjects Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>MCQs</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subjects as $subject)
                            <tr>
                                <td>
                                    <i class="fas fa-bookmark text-primary me-2"></i>
                                    {{ $subject->name }}
                                </td>
                                <td>{{ substr($subject->description, 0, 50) ?? 'N/A' }}...</td>
                                <td>
                                    <span class="badge bg-{{ $subject->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($subject->status) }}
                                    </span>
                                </td>
                                <td>{{ $subject->mcqs_count ?? 0 }}</td>
                                <td>{{ $subject->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="/admin/subjects/{{ $subject->id }}/edit" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="/admin/subjects/{{ $subject->id }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    No subjects found. <a href="/admin/subjects/create">Create one now</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $subjects->links() }}
    </div>
</div>
@endsection