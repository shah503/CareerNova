@extends('layouts.app')

@section('title', 'Create MCQ')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">
                        <i class="fas fa-plus"></i> Create MCQs
                    </h1>
                    <a href="{{ route('teacher.mcqs') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

                <!-- Tabs -->
                <ul class="nav nav-tabs mb-4" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="single-tab" data-bs-toggle="tab" data-bs-target="#single-form" type="button" role="tab">
                            <i class="fas fa-edit"></i> Single MCQ
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="bulk-tab" data-bs-toggle="tab" data-bs-target="#bulk-form" type="button" role="tab">
                            <i class="fas fa-file-csv"></i> Bulk Upload (CSV)
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Single MCQ Form -->
                    <div class="tab-pane fade show active" id="single-form" role="tabpanel">
                        <form action="{{ route('teacher.mcqs.store') }}" method="POST">
                            @csrf

                            <!-- Subject -->
                            <div class="mb-4">
                                <label for="subject_id" class="form-label fw-bold">Subject *</label>
                                <select id="subject_id" name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                                    <option value="">-- Select Subject --</option>
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

                            <!-- Question -->
                            <div class="mb-4">
                                <label for="question" class="form-label fw-bold">Question *</label>
                                <textarea id="question" name="question" class="form-control @error('question') is-invalid @enderror" 
                                          rows="4" placeholder="Enter the MCQ question" required>{{ old('question') }}</textarea>
                                @error('question')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Options -->
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="option_a" class="form-label fw-bold">Option A *</label>
                                    <input type="text" id="option_a" name="option_a" class="form-control @error('option_a') is-invalid @enderror" 
                                           placeholder="Option A" required value="{{ old('option_a') }}">
                                    @error('option_a')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="option_b" class="form-label fw-bold">Option B *</label>
                                    <input type="text" id="option_b" name="option_b" class="form-control @error('option_b') is-invalid @enderror" 
                                           placeholder="Option B" required value="{{ old('option_b') }}">
                                    @error('option_b')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="option_c" class="form-label fw-bold">Option C *</label>
                                    <input type="text" id="option_c" name="option_c" class="form-control @error('option_c') is-invalid @enderror" 
                                           placeholder="Option C" required value="{{ old('option_c') }}">
                                    @error('option_c')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="option_d" class="form-label fw-bold">Option D *</label>
                                    <input type="text" id="option_d" name="option_d" class="form-control @error('option_d') is-invalid @enderror" 
                                           placeholder="Option D" required value="{{ old('option_d') }}">
                                    @error('option_d')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Correct Answer -->
                            <div class="mb-4">
                                <label for="correct_answer" class="form-label fw-bold">Correct Answer *</label>
                                <select id="correct_answer" name="correct_answer" class="form-select @error('correct_answer') is-invalid @enderror" required>
                                    <option value="">-- Select Correct Answer --</option>
                                    <option value="A" {{ old('correct_answer') === 'A' ? 'selected' : '' }}>A</option>
                                    <option value="B" {{ old('correct_answer') === 'B' ? 'selected' : '' }}>B</option>
                                    <option value="C" {{ old('correct_answer') === 'C' ? 'selected' : '' }}>C</option>
                                    <option value="D" {{ old('correct_answer') === 'D' ? 'selected' : '' }}>D</option>
                                </select>
                                @error('correct_answer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Difficulty -->
                            <div class="mb-4">
                                <label for="difficulty" class="form-label fw-bold">Difficulty Level *</label>
                                <select id="difficulty" name="difficulty" class="form-select @error('difficulty') is-invalid @enderror" required>
                                    <option value="">-- Select Difficulty --</option>
                                    <option value="easy" {{ old('difficulty') === 'easy' ? 'selected' : '' }}>Easy</option>
                                    <option value="medium" {{ old('difficulty') === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="hard" {{ old('difficulty') === 'hard' ? 'selected' : '' }}>Hard</option>
                                </select>
                                @error('difficulty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Explanation -->
                            <div class="mb-4">
                                <label for="explanation" class="form-label fw-bold">Explanation (Optional)</label>
                                <textarea id="explanation" name="explanation" class="form-control @error('explanation') is-invalid @enderror" 
                                          rows="3" placeholder="Explain why this is the correct answer">{{ old('explanation') }}</textarea>
                                @error('explanation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Note -->
                            <div class="alert alert-info">
                                <strong>ℹ️ Note:</strong> Your MCQ will be submitted for admin review before being available to students.
                            </div>

                            <!-- Buttons -->
                            <div class="flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Submit MCQ for Review
                                </button>
                                <a href="{{ route('teacher.mcqs') }}" class="btn btn-secondary">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Bulk Upload Form -->
                    <div class="tab-pane fade" id="bulk-form" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4">📊 Bulk Import MCQs from CSV</h5>

                                <!-- Instructions -->
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> Instructions:</h6>
                                    <ol class="mb-0">
                                        <li>Download the CSV template below</li>
                                        <li>Fill in your MCQs following the template format</li>
                                        <li>Ensure all required columns are filled</li>
                                        <li>Upload the CSV file here</li>
                                        <li>Your MCQs will be submitted for admin review</li>
                                    </ol>
                                </div>

                                <!-- Download Template -->
                                <div class="mb-4">
                                    <a href="{{ route('teacher.csv.download-template') }}" class="btn btn-info btn-lg">
                                        <i class="fas fa-download"></i> Download CSV Template
                                    </a>
                                    <small class="text-muted d-block mt-2">
                                        Download this template and fill in your MCQs
                                    </small>
                                </div>

                                <!-- CSV Format Example -->
                                <div class="card mb-4 bg-light">
                                    <div class="card-body">
                                        <h6>CSV Format Example:</h6>
                                        <pre><code>subject_name,question,option_a,option_b,option_c,option_d,correct_answer,difficulty,explanation
Biology,What is the basic unit of life?,Atom,Cell,Molecule,Tissue,B,easy,The cell is the smallest unit of life.
Chemistry,What is the pH of neutral water?,0,7,14,10,B,medium,Neutral solutions have a pH of 7.</code></pre>
                                    </div>
                                </div>

                                <!-- Upload Form -->
                                <form action="{{ route('teacher.csv.import') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="mb-4">
                                        <label for="csv_file" class="form-label fw-bold">Select CSV File *</label>
                                        <div class="input-group">
                                            <input type="file" name="csv_file" id="csv_file" class="form-control @error('csv_file') is-invalid @enderror" 
                                                   accept=".csv,.txt" required>
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-upload"></i> Upload
                                            </button>
                                        </div>
                                        @error('csv_file')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Constraints -->
                                    <div class="alert alert-warning">
                                        <strong>⚠️ File Constraints:</strong>
                                        <ul class="mb-0">
                                            <li>File must be CSV or TXT format</li>
                                            <li>Maximum file size: 10MB</li>
                                            <li>All 9 columns are required</li>
                                            <li>Subject name must match existing subjects</li>
                                            <li>Correct answer must be A, B, C, or D</li>
                                            <li>Difficulty must be easy, medium, or hard</li>
                                        </ul>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection