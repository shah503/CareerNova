@extends('layouts.app')

@section('title', 'My Analytics')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">📊 Performance Analytics</h1>
    
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h5 class="card-title text-muted mb-2">Total Tests</h5>
                    <h2 class="text-primary mb-0 fw-bold">
                        {{ $analytics['total_tests'] ?? (isset($trendData) ? count($trendData) : 0) }}
                    </h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h5 class="card-title text-muted mb-2">Average Score</h5>
                    <h2 class="text-success mb-0 fw-bold">
                        @if(isset($trendData) && count($trendData) > 0)
                            {{ round(array_sum($trendData) / count($trendData), 1) }}%
                        @else
                            0%
                        @endif
                    </h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h5 class="card-title text-muted mb-2">Tests Passed</h5>
                    <h2 class="text-info mb-0 fw-bold">
                        @if(isset($trendData) && count($trendData) > 0)
                            {{ collect($trendData)->filter(fn($score) => $score >= 50)->count() }}
                        @else
                            0
                        @endif
                    </h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h5 class="card-title text-muted mb-2">Success Rate</h5>
                    <h2 class="text-warning mb-0 fw-bold">
                        @if(isset($trendData) && count($trendData) > 0)
                            @php
                                $passedCount = collect($trendData)->filter(fn($score) => $score >= 50)->count();
                                $totalCount = count($trendData);
                            @endphp
                            {{ round(($passedCount / $totalCount) * 100, 1) }}%
                        @else
                            {{ round($analytics['success_rate'] ?? 0, 1) }}%
                        @endif
                    </h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0 fw-bold">Performance Trend</h5>
                </div>
                <div class="card-body">
                    <canvas id="trendChart" height="140"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0 fw-bold">Subject Performance</h5>
                </div>
                <div class="card-body">
                    <canvas id="subjectChart" height="140"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-light">
            <h5 class="mb-0 fw-bold">⚠️ Areas for Improvement</h5>
        </div>
        <div class="card-body p-0">
            @if(isset($weakAreas) && $weakAreas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Subject</th>
                                <th class="text-end pe-4">Wrong Attempts</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($weakAreas as $area)
                            <tr>
                                <td class="ps-4 fw-semibold">{{ $area->name }}</td>
                                <td class="text-end pe-4">
                                    <span class="badge bg-danger px-3 py-2 fs-6">{{ $area->wrong_count }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted text-center py-5 mb-0">No weak areas identified. Great job! 🎉</p>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Trend Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: @json($trendLabels ?? []),
            datasets: [{
                label: 'Score Percentage',
                data: @json($trendData ?? []),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, max: 100 }
            }
        }
    });
    
    // Subject Chart
    const subjectCtx = document.getElementById('subjectChart').getContext('2d');
    new Chart(subjectCtx, {
        type: 'bar',
        data: {
            labels: @json($subjectLabels ?? []),
            datasets: [{
                label: 'Average Score %',
                data: @json($subjectData ?? []),
                backgroundColor: ['#667eea', '#764ba2', '#f093fb', '#4facfe'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, max: 100 }
            }
        }
    });
</script>
@endsection