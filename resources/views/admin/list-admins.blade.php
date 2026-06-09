@extends('layouts.app')

@section('title', 'Manage Admins')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">
                        <i class="fas fa-user-shield"></i> Manage Admins
                    </h1>
                    <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Admin
                    </a>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($admins->isEmpty())
                    <div class="alert alert-info">
                        No admin accounts found.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($admins as $admin)
                                    <tr>
                                        <td>{{ $admin->name }}</td>
                                        <td>{{ $admin->email }}</td>
                                        <td>{{ $admin->phone ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $admin->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                                {{ ucfirst($admin->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $admin->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @if ($admin->id !== auth()->id())
                                                <form action="{{ route('admin.admins.destroy', $admin) }}" 
                                                      method="POST" style="display:inline;" 
                                                      onsubmit="return confirm('Delete this admin? This action cannot be undone!')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge bg-info">You</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No admins found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $admins->links() }}
                @endif
            </div>
        </div>
    </div>
</div>
@endsection