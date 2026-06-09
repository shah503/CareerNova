@extends('layouts.app')

@section('title', 'Available Exams')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-3xl font-bold mb-6"><i class="fas fa-book"></i> Available Exams</h1>

                @if(isset($subjects) && $subjects->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($subjects as $subject)
                        <div class="border rounded-lg p-4">
                            <h3 class="font-semibold text-lg">{{ $subject->name }}</h3>
                            <p class="text-gray-600 text-sm">{{ $subject->description ?? 'No description' }}</p>
                            <a href="{{ route('exam.select-subject') }}" class="btn btn-sm btn-primary mt-2">Take Exam</a>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">No exams available at this time.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection