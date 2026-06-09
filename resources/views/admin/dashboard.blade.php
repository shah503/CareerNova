@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h1 class="text-3xl font-bold mb-6">
                    <i class="fas fa-tachometer-alt"></i> Admin Dashboard
                </h1>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="text-gray-600 text-sm font-medium">Total Students</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['total_students'] ?? 0 }}</p>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h3 class="text-gray-600 text-sm font-medium">Total Teachers</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['total_teachers'] ?? 0 }}</p>
                    </div>
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <h3 class="text-gray-600 text-sm font-medium">Total MCQs</h3>
                        <p class="text-3xl font-bold text-purple-600">{{ $stats['total_mcqs'] ?? 0 }}</p>
                    </div>
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <h3 class="text-gray-600 text-sm font-medium">Total Tests</h3>
                        <p class="text-3xl font-bold text-orange-600">{{ $stats['total_tests'] ?? 0 }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <h3 class="text-gray-600 text-sm font-medium">Pending MCQ Reviews</h3>
                        <p class="text-3xl font-bold text-red-600">{{ $stats['pending_review'] ?? 0 }}</p>
                    </div>
                    <div class="bg-cyan-50 border border-cyan-200 rounded-lg p-4">
                        <h3 class="text-gray-600 text-sm font-medium">Average Test Score</h3>
                        <p class="text-3xl font-bold text-cyan-600">{{ round($stats['avg_score'] ?? 0, 1) }}%</p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('admin.users.list') }}" class="bg-white p-4 border rounded-lg hover:shadow-md transition cursor-pointer">
                        <h3 class="font-semibold"><i class="fas fa-users"></i> Manage Users</h3>
                        <p class="text-gray-600 text-sm">View and manage all users</p>
                    </a>
                    <a href="{{ route('admin.mcqs.list') }}" class="bg-white p-4 border rounded-lg hover:shadow-md transition cursor-pointer">
                        <h3 class="font-semibold"><i class="fas fa-file-alt"></i> Review MCQs</h3>
                        <p class="text-gray-600 text-sm">{{ $stats['pending_review'] ?? 0 }} pending review</p>
                    </a>
                    <a href="{{ route('admin.analytics') }}" class="bg-white p-4 border rounded-lg hover:shadow-md transition cursor-pointer">
                        <h3 class="font-semibold"><i class="fas fa-chart-bar"></i> Analytics</h3>
                        <p class="text-gray-600 text-sm">View system analytics</p>
                    </a>
                    <a href="{{ route('admin.admins.list') }}" class="bg-white p-4 border rounded-lg hover:shadow-md transition cursor-pointer">
                        <h3 class="font-semibold"><i class="fas fa-user-shield"></i> Manage Admins</h3>
                        <p class="text-gray-600 text-sm">Create and manage admin accounts</p>
                    </a>
                    <a href="{{ route('admin.settings') }}" class="bg-white p-4 border rounded-lg hover:shadow-md transition cursor-pointer">
                        <h3 class="font-semibold"><i class="fas fa-cog"></i> Settings</h3>
                        <p class="text-gray-600 text-sm">System settings and configuration</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection