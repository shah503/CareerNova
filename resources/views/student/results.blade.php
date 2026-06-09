@extends('layouts.app')

@section('title', 'My Results')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-3xl font-bold mb-6"><i class="fas fa-list"></i> My Results</h1>

                @if(isset($sessions) && $sessions->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Test</th>
                                    <th>Date</th>
                                    <th>Score</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sessions as $session)
                                <tr>
                                    <td>{{ $session->mcq->question ?? 'Test' }}</td>
                                    <td>{{ $session->created_at->format('M d, Y') }}</td>
                                    <td>{{ round($session->percentage ?? 0, 1) }}%</td>
                                    <td>
                                        <span class="badge {{ ($session->is_passed ?? false) ? 'bg-success' : 'bg-danger' }}">
                                            {{ ($session->is_passed ?? false) ? 'Passed' : 'Failed' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $sessions->links() }}
                @else
                    <div class="alert alert-info">No results yet. Take a test to see your results here.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection