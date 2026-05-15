<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Netcoder ERP - Management System</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
            .hero-gradient { background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%); }
            .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.5); }
        </style>
    </head>
    <body class="antialiased text-gray-900">
        <div class="min-h-screen flex flex-col">
            <!-- Navbar -->
            <nav class="glass sticky top-0 z-50 px-6 py-4">
                <div class="max-w-7xl mx-auto flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-orange-600 rounded-lg flex items-center justify-center text-white shadow-lg">
                            <i class="fas fa-microchip text-xl"></i>
                        </div>
                        <span class="text-xl font-bold tracking-tight text-gray-800">Netcoder<span class="text-orange-600">ERP</span></span>
                    </div>
                    
                    <div class="flex items-center gap-6">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 bg-gray-900 text-white rounded-xl hover:bg-gray-800 transition-all font-medium text-sm shadow-md">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="text-gray-600 hover:text-orange-600 font-medium transition-colors text-sm">Log in</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="px-5 py-2.5 bg-orange-600 text-white rounded-xl hover:bg-orange-700 transition-all font-medium text-sm shadow-md">Get Started</a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </nav>

            <!-- Hero Section -->
            <main class="flex-1 hero-gradient flex items-center justify-center px-6 py-20">
                <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-orange-50 text-orange-600 rounded-full text-xs font-bold uppercase tracking-wider mb-6">
                            <span class="w-2 h-2 bg-orange-600 rounded-full animate-pulse"></span>
                            Next-Gen ERP Solution
                        </div>
                        <h1 class="text-5xl lg:text-7xl font-extrabold text-gray-900 leading-tight mb-6">
                            Modern Management for <span class="text-orange-600 italic">Netcoder</span> Technology.
                        </h1>
                        <p class="text-lg text-gray-600 mb-10 leading-relaxed max-w-xl">
                            A professional, scalable, and intuitive ERP system designed to streamline student fees, staff payroll, and client billing with precision and ease.
                        </p>
                        
                        <div class="flex flex-wrap gap-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="px-8 py-4 bg-gray-900 text-white rounded-2xl hover:scale-105 transition-transform font-bold shadow-xl flex items-center gap-3">
                                    Go to Dashboard <i class="fas fa-arrow-right"></i>
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="px-8 py-4 bg-orange-600 text-white rounded-2xl hover:scale-105 transition-transform font-bold shadow-xl flex items-center gap-3">
                                    Login to Portal <i class="fas fa-sign-in-alt"></i>
                                </a>
                                <a href="#features" class="px-8 py-4 bg-white text-gray-900 border border-gray-200 rounded-2xl hover:bg-gray-50 transition-colors font-bold shadow-sm">
                                    Explore Features
                                </a>
                            @endauth
                        </div>
                        
                        <!-- Stats -->
                        <div class="mt-16 flex gap-12 border-t border-gray-200 pt-10">
                            <div>
                                <div class="text-3xl font-bold text-gray-900">100%</div>
                                <div class="text-sm text-gray-500 font-medium">Digital Migration</div>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-gray-900">Real-time</div>
                                <div class="text-sm text-gray-500 font-medium">Fee Tracking</div>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-gray-900">Secure</div>
                                <div class="text-sm text-gray-500 font-medium">Data Integrity</div>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="absolute -inset-4 bg-orange-100 rounded-[3rem] blur-2xl opacity-30 animate-pulse"></div>
                        <div class="relative bg-white rounded-[2.5rem] p-4 shadow-2xl border border-gray-100 overflow-hidden">
                            <div class="bg-gray-50 rounded-[2rem] p-8 aspect-square flex items-center justify-center">
                                <div class="grid grid-cols-2 gap-4 w-full">
                                    <div class="p-6 bg-white rounded-3xl shadow-sm border border-gray-100">
                                        <i class="fas fa-user-graduate text-3xl text-orange-500 mb-4"></i>
                                        <div class="font-bold">Students</div>
                                        <div class="text-xs text-gray-400 mt-1">Management</div>
                                    </div>
                                    <div class="p-6 bg-white rounded-3xl shadow-sm border border-gray-100 mt-8">
                                        <i class="fas fa-indian-rupee-sign text-3xl text-green-500 mb-4"></i>
                                        <div class="font-bold">Fees</div>
                                        <div class="text-xs text-gray-400 mt-1">Collection</div>
                                    </div>
                                    <div class="p-6 bg-white rounded-3xl shadow-sm border border-gray-100">
                                        <i class="fas fa-file-invoice text-3xl text-blue-500 mb-4"></i>
                                        <div class="font-bold">Invoices</div>
                                        <div class="text-xs text-gray-400 mt-1">Generation</div>
                                    </div>
                                    <div class="p-6 bg-white rounded-3xl shadow-sm border border-gray-100 mt-8">
                                        <i class="fas fa-chart-line text-3xl text-purple-500 mb-4"></i>
                                        <div class="font-bold">Reports</div>
                                        <div class="text-xs text-gray-400 mt-1">Analytics</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 px-6 py-12">
                <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-gray-200 rounded flex items-center justify-center text-gray-600">
                            <i class="fas fa-microchip text-sm"></i>
                        </div>
                        <span class="text-sm font-bold tracking-tight text-gray-800">Netcoder<span class="text-orange-600">ERP</span></span>
                    </div>
                    <div class="text-sm text-gray-500 font-medium">
                        &copy; {{ date('Y') }} Netcoder Technology. All rights reserved.
                    </div>
                    <div class="flex gap-6">
                        <a href="#" class="text-gray-400 hover:text-orange-600 transition-colors"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-400 hover:text-orange-600 transition-colors"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-orange-600 transition-colors"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="text-gray-400 hover:text-orange-600 transition-colors"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
