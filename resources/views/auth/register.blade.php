<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Create Account</h2>
        <p class="text-sm text-gray-500">Join the Netcoder ERP platform today.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-xl border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3" required autofocus>
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Email Address -->
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-xl border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3" required>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
            <input type="password" name="password" class="w-full rounded-xl border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3" required>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
            <input type="password" name="password_confirmation" class="w-full rounded-xl border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3" required>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <button type="submit" class="w-full py-4 bg-gray-900 text-white rounded-xl font-bold hover:bg-black transition-all shadow-lg hover:shadow-xl active:scale-95">
            Register
        </button>

        <p class="mt-6 text-center text-xs text-gray-500">
            Already have an account? 
            <a href="{{ route('login') }}" class="font-bold text-orange-600 hover:underline">Log in</a>
        </p>
    </form>
</x-guest-layout>
