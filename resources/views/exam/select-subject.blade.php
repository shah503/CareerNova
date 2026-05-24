@extends('layouts.app')

@section('title', 'Select Subject')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-book"></i> Select Subject for Exam</h2>
            <p class="text-muted">Choose a subject and configure your exam settings</p>
        </div>
    </div>

    <div class="row">
        @forelse ($subjects as $subject)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm hover-lift">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-bookmark text-primary"></i> {{ $subject->name }}
                        </h5>
                        <p class="card-text text-muted small">{{ $subject->description }}</p>
                        
                        <div class="mb-3">
                            <small class="text-success">
                                <i class="fas fa-check-circle"></i> 
                                {{ $subject->getActiveMcqCount() }} Active Questions
                            </small>
                        </div>

                        <!-- Exam Config Form -->
                        <form action="/exam/create" method="POST" class="exam-config-form">
                            @csrf
                            <input type="hidden" name="subject_id" value="{{ $subject->id }}">

                            <div class="mb-2">
                                <label for="question_count_{{ $subject->id }}" class="form-label small">
                                    Number of Questions
                                </label>
                                <select class="form-select form-select-sm" name="question_count" id="question_count_{{ $subject->id }}" required>
                                    <option value="10">10 Questions</option>
                                    <option value="20">20 Questions</option>
                                    <option value="30">30 Questions</option>
                                    <option value="50">50 Questions</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="duration_{{ $subject->id }}" class="form-label small">
                                    Duration (minutes)
                                </label>
                                <select class="form-select form-select-sm" name="duration_minutes" id="duration_{{ $subject->id }}" required>
                                    <option value="10">10 Minutes</option>
                                    <option value="20" selected>20 Minutes</option>
                                    <option value="30">30 Minutes</option>
                                    <option value="60">60 Minutes</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-play"></i> Start Exam
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-md-12">
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <p>No subjects available at the moment. Please try again later.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    }
    .border-left-primary {
        border-left: 4px solid #007bff !important;
    }
    .border-left-success {
        border-left: 4px solid #28a745 !important;
    }
</style>
@endsection