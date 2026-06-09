@extends('layouts.app')

@section('title', 'Select Subject')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h1 class="text-3xl font-bold mb-6"><i class="fas fa-book"></i> Select Subject</h1>

                @if ($subjects->isEmpty())
                    <div class="alert alert-info">
                        No subjects available at this time. Please check back later.
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($subjects as $subject)
                            <a href="{{ route('exam.index', ['subject_id' => $subject->id]) }}" 
                               class="border rounded-lg p-6 hover:shadow-lg transition cursor-pointer bg-gradient-to-br from-blue-50 to-indigo-50 border-blue-200">
                                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $subject->name }}</h3>
                                <p class="text-gray-600 text-sm mb-4">{{ $subject->description }}</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-semibold text-blue-600">
                                        {{ $subject->mcqs_count }} Questions
                                    </span>
                                    <span class="text-lg">➜</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection