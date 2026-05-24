@extends('layouts.app')

@section('title', 'Import Results')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-check-circle"></i> Import Results</h4>
                </div>
                <div class="card-body p-4">
                    <!-- Success Summary -->
                    @if (isset($results['successful']) && $results['successful'] > 0)
                        <div class="alert alert-success mb-4">
                            <i class="fas fa-check-circle"></i> 
                            <strong>{{ $results['successful'] }} questions imported successfully!</strong>
                        </div>
                    @endif

                    <!-- Failed Summary -->
                    @if (isset($results['failed']) && $results['failed'] > 0)
                        <div class="alert alert-warning mb-4">
                            <i class="fas fa-exclamation-triangle"></i> 
                            <strong>{{ $results['failed'] }} questions failed to import</strong>
                        </div>
                    @endif

                    <!-- Skipped Summary -->
                    @if (isset($results['skipped']) && $results['skipped'] > 0)
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle"></i> 
                            <strong>{{ $results['skipped'] }} rows were skipped (empty or invalid)</strong>
                        </div>
                    @endif

                    <!-- Detailed Results -->
                    @if (isset($results['details']) && count($results['details']) > 0)
                        <div class="mb-4">
                            <h5 class="mb-3">Import Details</h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Row</th>
                                            <th>Status</th>
                                            <th>Question</th>
                                            <th>Message</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($results['details'] as $detail)
                                            <tr>
                                                <td>{{ $detail['row'] ?? 'N/A' }}</td>
                                                <td>
                                                    @if ($detail['status'] === 'success')
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check"></i> Success
                                                        </span>
                                                    @elseif ($detail['status'] === 'failed')
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-times"></i> Failed
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-skip"></i> Skipped
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ substr($detail['question'] ?? 'N/A', 0, 50) }}...
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $detail['message'] ?? '-' }}</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                        <a href="/import/csv" class="btn btn-primary">
                            <i class="fas fa-cloud-upload-alt"></i> Import More
                        </a>
                        <a href="/admin/dashboard" class="btn btn-secondary">
                            <i class="fas fa-home"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection