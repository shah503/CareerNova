<x-guest-layout>
    <div class="mb-4">
        <h2 class="text-2xl font-bold text-gray-800">Create Your Account</h2>
        <p class="text-gray-600 text-sm mt-1">Join CareerNova and start your exam preparation journey</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-4">
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input 
                id="name" 
                class="block mt-1 w-full" 
                type="text" 
                name="name" 
                :value="old('name')" 
                placeholder="Enter your full name"
                required 
                autofocus 
                autocomplete="name" 
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mb-4">
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input 
                id="email" 
                class="block mt-1 w-full" 
                type="email" 
                name="email" 
                :value="old('email')" 
                placeholder="you@example.com"
                required 
                autocomplete="username" 
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Role Selection -->
        <div class="mb-4">
            <x-input-label for="role" :value="__('Select Your Role')" />
            <select 
                id="role" 
                name="role" 
                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('role') border-red-500 @enderror"
                required
            >
                <option value="" selected disabled>-- Please choose your role --</option>
                <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>
                    👨‍🎓 Student - Take exams and practice tests
                </option>
                <option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>
                    👨‍🏫 Teacher - Create MCQs and manage classes
                </option>
                <option value="parent" {{ old('role') === 'parent' ? 'selected' : '' }}>
                    👨‍👩‍👧 Parent - Monitor your child's progress
                </option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
            <small class="text-gray-500 mt-1 block">
                💡 <strong>Can't decide?</strong> You can always change your role later. Admins are created by existing administrators only.
            </small>
        </div>

        <!-- Password -->
        <div class="mb-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input 
                id="password" 
                class="block mt-1 w-full"
                type="password"
                name="password"
                placeholder="Minimum 8 characters"
                required 
                autocomplete="new-password" 
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <small class="text-gray-500 mt-1 block">
                Must be at least 8 characters with uppercase, lowercase, number & symbol
            </small>
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input 
                id="password_confirmation" 
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation" 
                placeholder="Re-enter your password"
                required 
                autocomplete="new-password" 
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Terms Agreement -->
        <div class="mb-4">
            <label for="terms" class="inline-flex items-center">
                <input 
                    id="terms" 
                    type="checkbox" 
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" 
                    name="terms" 
                    required
                >
                <span class="ms-2 text-sm text-gray-600">
                    I agree to the 
                    <a href="#" class="text-indigo-600 hover:underline">Terms & Conditions</a> 
                    and 
                    <a href="#" class="text-indigo-600 hover:underline">Privacy Policy</a>
                </span>
            </label>
            <x-input-error :messages="$errors->get('terms')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="text-sm text-indigo-600 hover:text-indigo-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already have an account?') }}
            </a>

            <x-primary-button>
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Social Registration Options (Optional) -->
    <div class="mt-6 border-t pt-6">
        <p class="text-center text-sm text-gray-600 mb-4">Or continue with</p>
        <div class="flex gap-2">
            <button type="button" class="flex-1 btn btn-outline-secondary">
                <i class="fab fa-google"></i> Google
            </button>
            <button type="button" class="flex-1 btn btn-outline-secondary">
                <i class="fab fa-facebook"></i> Facebook
            </button>
        </div>
    </div>
</x-guest-layout>