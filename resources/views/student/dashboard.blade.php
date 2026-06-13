@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">📚 My Dashboard</h1>
    
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-primary h-100 shadow-sm">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h5 class="card-title text-muted mb-1">Total Tests</h5>
                    <h2 class="text-primary mb-0 fw-bold">
                        {{ $stats['total_tests'] ?? (isset($recentSessions) ? $recentSessions->count() : 0) }}
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-success h-100 shadow-sm">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h5 class="card-title text-muted mb-1">Average Score</h5>
                    <h2 class="text-success mb-0 fw-bold">{{ round($stats['average_score'] ?? 0, 1) }}%</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-info h-100 shadow-sm">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h5 class="card-title text-muted mb-1">Tests Passed</h5>
                    <h2 class="text-info mb-0 fw-bold">
                        @if(isset($recentSessions) && $recentSessions->count() > 0)
                            {{ $recentSessions->where('percentage', '>=', 50)->count() }}
                        @else
                            {{ $stats['tests_passed'] ?? 0 }}
                        @endif
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-warning h-100 shadow-sm">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h5 class="card-title text-muted mb-1">Current Rank</h5>
                    <h2 class="text-warning mb-0 fw-bold">#{{ auth()->user()->leaderboard?->overall_rank ?? 'N/A' }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-4 mb-3">
            <a href="{{ route('exam.select-subject') }}" class="card text-decoration-none border-primary h-100 shadow-sm btn-hover bg-light text-start transition-all">
                <div class="card-body d-flex align-items-center">
                    <div class="display-6 me-3 text-primary">📝</div>
                    <div>
                        <h5 class="card-title mb-1 text-dark fw-bold">Take a Test</h5>
                        <p class="card-text text-muted small mb-0">Launch into a brand new exam session.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="{{ route('student.result') }}" class="card text-decoration-none border-secondary h-100 shadow-sm btn-hover bg-light text-start transition-all">
                <div class="card-body d-flex align-items-center">
                    <div class="display-6 me-3 text-secondary">📋</div>
                    <div>
                        <h5 class="card-title mb-1 text-dark fw-bold">My Results</h5>
                        <p class="card-text text-muted small mb-0">Browse your detailed past results history.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="{{ route('student.analytics') }}" class="card text-decoration-none border-secondary h-100 shadow-sm btn-hover bg-light text-start transition-all">
                <div class="card-body d-flex align-items-center">
                    <div class="display-6 me-3 text-secondary">📊</div>
                    <div>
                        <h5 class="card-title mb-1 text-dark fw-bold">Analytics</h5>
                        <p class="card-text text-muted small mb-0">Deep dive into custom performance graphs.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0 fw-bold">📈 Performance Trend</h5>
                </div>
                <div class="card-body">
                    @if(isset($trendData) && count($trendData) > 0)
                        <canvas id="performanceChart" height="140"></canvas>
                    @else
                        <p class="text-muted text-center py-5">No test data available yet</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0 fw-bold">📊 Subject Performance</h5>
                </div>
                <div class="card-body">
                    @if(isset($subjectData) && count($subjectData) > 0)
                        <canvas id="subjectChart" height="140"></canvas>
                    @else
                        <p class="text-muted text-center py-5">No subject data available yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-5">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0 fw-bold">✓ Pass vs Fail Distribution</h5>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center" style="min-height: 250px;">
                    <div style="width: 80%; max-width: 300px;">
                        <canvas id="passFailChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-light">
            <h5 class="mb-0 fw-bold">📋 Recent Test History</h5>
        </div>
        <div class="card-body p-0">
            @if(isset($recentSessions) && $recentSessions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Subject</th>
                                <th>Score</th>
                                <th>Percentage</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentSessions as $session)
                            <tr>
                                <td class="ps-4 fw-semibold">{{ $session->subject->name ?? 'N/A' }}</td>
                                <td>{{ $session->score }} / {{ $session->total_questions }}</td>
                                <td>
                                    <span class="badge bg-{{ $session->percentage >= 50 ? 'success' : 'danger' }} px-2 py-2">
                                        {{ number_format($session->percentage, 1) }}%
                                    </span>
                                </td>
                                <td>
                                    @if($session->percentage >= 50)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-2">✓ Passed</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-2">✗ Failed</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $session->finished_at?->format('M d, Y') ?? $session->created_at->format('M d, Y') }}</td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('exam.result', $session->id) }}" class="btn btn-sm btn-outline-info">
                                        View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted text-center py-5 mb-0">No tests taken yet. Start taking exams! 🚀</p>
            @endif
        </div>
    </div>
</div>

<style>
    .btn-hover {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .btn-hover:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        background-color: #fff !important;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Performance Trend Chart
    @if(isset($trendData) && count($trendData) > 0)
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    new Chart(performanceCtx, {
        type: 'line',
        data: {
            labels: @json($trendLabels ?? []),
            datasets: [{
                label: 'Percentage',
                data: @json($trendData ?? []),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#667eea'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, max: 100 } }
        }
    });
    @endif
    
    // Subject Performance Chart
    @if(isset($subjectData) && count($subjectData) > 0)
    const subjectCtx = document.getElementById('subjectChart').getContext('2d');
    new Chart(subjectCtx, {
        type: 'bar',
        data: {
            labels: @json($subjectLabels ?? []),
            datasets: [{
                label: 'Average %',
                data: @json($subjectData ?? []),
                backgroundColor: ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#43e97b'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, max: 100 } }
        }
    });
    @endif
    
    // Pass vs Fail Chart
    const passFailCtx = document.getElementById('passFailChart').getContext('2d');
    new Chart(passFailCtx, {
        type: 'doughnut',
        data: {
            labels: ['Passed', 'Failed'],
            datasets: [{
                data: [
                    {{ isset($recentSessions) ? $recentSessions->where('percentage', '>=', 50)->count() : 0 }}, 
                    {{ isset($recentSessions) ? $recentSessions->where('percentage', '<', 50)->count() : 0 }}
                ],
                backgroundColor: ['#28a745', '#dc3545'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
@endsection