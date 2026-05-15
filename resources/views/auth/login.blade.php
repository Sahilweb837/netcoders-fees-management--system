<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Welcome Back</h2>
        <p class="text-sm text-gray-500">Please enter your credentials to access the ERP.</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-xl border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3" required autofocus>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
            <input type="password" name="password" class="w-full rounded-xl border-gray-200 focus:border-orange-500 focus:ring-orange-500 py-3" required>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between mb-6">
            <label class="inline-flex items-center">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                <span class="ms-2 text-xs font-medium text-gray-500">Remember me</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-xs font-bold text-orange-600 hover:text-orange-700" href="{{ route('password.request') }}">
                    Forgot Password?
                </a>
            @endif
        </div>

        <button type="submit" class="w-full py-4 bg-gray-900 text-white rounded-xl font-bold hover:bg-black transition-all shadow-lg hover:shadow-xl active:scale-95">
            Log in
        </button>

        @if (Route::has('register'))
            <p class="mt-6 text-center text-xs text-gray-500">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-bold text-orange-600 hover:underline">Register here</a>
            </p>
        @endif
    </form>
</x-guest-layout>
