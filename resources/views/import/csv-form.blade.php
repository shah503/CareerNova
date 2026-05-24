@extends('layouts.app')

@section('title', 'Import MCQs from CSV')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-upload"></i> Import MCQs from CSV</h4>
                </div>
                <div class="card-body p-4">
                    <form action="/import/csv" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Subject Selection -->
                        <div class="mb-3">
                            <label for="subject_id" class="form-label">Select Subject</label>
                            <select class="form-select @error('subject_id') is-invalid @enderror" 
                                    id="subject_id" name="subject_id" required>
                                <option value="">-- Choose a Subject --</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- CSV File Upload -->
                        <div class="mb-4">
                            <label for="csv_file" class="form-label">Upload CSV File</label>
                            <div class="input-group">
                                <input type="file" class="form-control @error('csv_file') is-invalid @enderror" 
                                       id="csv_file" name="csv_file" accept=".csv,.txt" required>
                            </div>
                            @error('csv_file')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Accepted formats: CSV (.csv) or TXT (.txt)<br>
                                Maximum file size: 5MB
                            </small>
                        </div>

                        <!-- File Format Info -->
                        <div class="alert alert-info mb-4">
                            <h6><i class="fas fa-info-circle"></i> CSV File Format</h6>
                            <p class="small mb-2">Your CSV file should contain the following columns:</p>
                            <ul class="small mb-0">
                                <li><strong>Question</strong> - The question text</li>
                                <li><strong>Option A</strong> - First option</li>
                                <li><strong>Option B</strong> - Second option</li>
                                <li><strong>Option C</strong> - Third option</li>
                                <li><strong>Option D</strong> - Fourth option</li>
                                <li><strong>Correct Answer</strong> - A, B, C, or D</li>
                                <li><strong>Difficulty</strong> - easy, medium, or hard (optional)</li>
                                <li><strong>Explanation</strong> - Answer explanation (optional)</li>
                            </ul>
                        </div>

                        <!-- Download Template -->
                        <div class="mb-4">
                            <a href="/import/csv-template" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-download"></i> Download CSV Template
                            </a>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-cloud-upload-alt"></i> Upload and Import
                        </button>
                    </form>

                    <hr class="my-4">

                    <!-- Example Data -->
                    <div>
                        <h6 class="mb-3"><i class="fas fa-table"></i> Example CSV Data</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Question</th>
                                        <th>Option A</th>
                                        <th>Option B</th>
                                        <th>Option C</th>
                                        <th>Option D</th>
                                        <th>Correct Answer</th>
                                        <th>Difficulty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>What is the capital of France?</td>
                                        <td>London</td>
                                        <td>Paris</td>
                                        <td>Berlin</td>
                                        <td>Madrid</td>
                                        <td>B</td>
                                        <td>easy</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection