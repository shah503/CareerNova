@extends('layouts.app')

@section('title', 'My Classes')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h1 class="text-3xl font-bold mb-6">
                    <i class="fas fa-users"></i> My Classes
                </h1>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Class management feature is coming soon.
                    For now, you can track student progress through the Results section.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection