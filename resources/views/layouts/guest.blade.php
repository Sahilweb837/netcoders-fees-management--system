<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Netcoder ERP') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Inter', sans-serif; }
            .auth-bg { background: radial-gradient(circle at top right, #fff7ed, #ffffff, #f8fafc); }
        </style>
    </head>
    <body class="antialiased text-gray-900 auth-bg">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-6">
            <div class="mb-8">
                <a href="/" class="flex flex-col items-center gap-3">
                    <div class="w-16 h-16 bg-orange-600 rounded-2xl flex items-center justify-center text-white shadow-2xl rotate-3 hover:rotate-0 transition-transform">
                        <i class="fas fa-microchip text-3xl"></i>
                    </div>
                    <span class="text-2xl font-bold tracking-tighter text-gray-800">Netcoder<span class="text-orange-600">ERP</span></span>
                </a>
            </div>

            <div class="w-full sm:max-w-md bg-white p-8 shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-gray-100 rounded-[2rem]">
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-sm text-gray-400 font-medium">
                &copy; {{ date('Y') }} Netcoder Technology
            </div>
        </div>
    </body>
</html>
