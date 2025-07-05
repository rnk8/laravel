<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' - ' : '' }}{{ config('app.name', 'Tramontina') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        <!-- Meta tags para corporativo -->
        <meta name="description" content="Sistema Corporativo Tramontina - Gestión de productos y auditoría">
        <meta name="keywords" content="Tramontina, utensilios, cocina, gestión, auditoría">
        <meta name="author" content="Tramontina">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Estilos adicionales -->
        <style>
            .bg-tramontina { background-color: #2563eb; }
            .text-tramontina { color: #2563eb; }
            .border-tramontina { border-color: #2563eb; }
            .btn-tramontina {
                background-color: #2563eb;
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 0.375rem;
                font-weight: 600;
                transition: all 0.2s;
            }
            .btn-tramontina:hover {
                background-color: #1d4ed8;
                transform: translateY(-1px);
            }
            .card-tramontina {
                background: white;
                border-radius: 8px;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
                border: 1px solid #e2e8f0;
            }
        </style>

        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="min-h-screen flex flex-col">
            <header class="bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg">
                <div class="max-w-7xl mx-auto px-4 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-lg p-1">
                                <img src="{{ asset('logo.png') }}" alt="Tramontina" class="w-full h-full object-contain">
                            </div>
                            <div>
                                <span class="font-bold text-xl text-white">Tramontina</span>
                                <p class="text-blue-100 text-sm">Sistema Corporativo</p>
                            </div>
                        </div>
                        <nav class="hidden md:flex space-x-8">
                            <a href="{{ route('dashboard') }}" class="text-white hover:text-blue-200 font-medium transition-colors">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                                </svg>
                                Dashboard
                            </a>
                            <a href="{{ route('products.index') }}" class="text-white hover:text-blue-200 font-medium transition-colors">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                Productos
                            </a>
                            <a href="#" class="text-white hover:text-blue-200 font-medium transition-colors">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Auditoría
                            </a>
                            <a href="#" class="text-white hover:text-blue-200 font-medium transition-colors">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Reportes
                            </a>
                        </nav>
                        <div class="flex items-center space-x-4">
                            @auth
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-bold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                    </div>
                                    <div class="hidden md:block">
                                        <p class="text-white text-sm font-medium">{{ Auth::user()->name }}</p>
                                        <p class="text-blue-100 text-xs">{{ Auth::user()->email }}</p>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-white text-blue-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-50 transition-colors">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Salir
                                    </button>
                                </form>
                            @endauth
                        </div>
                    </div>
                </div>
            </header>
            <main class="flex-1 py-6">
                @if (session('success'))
                    <div class="max-w-7xl mx-auto px-4 mb-4">
                        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                {{ session('success') }}
                            </div>
                        </div>
                    </div>
                @endif
                @if (session('error'))
                    <div class="max-w-7xl mx-auto px-4 mb-4">
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ session('error') }}
                            </div>
                        </div>
                    </div>
                @endif
                {{ $slot }}
            </main>
            <footer class="bg-gradient-to-r from-blue-700 to-blue-800 text-white py-8">
                <div class="max-w-7xl mx-auto px-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center p-1">
                                <img src="{{ asset('logo.png') }}" alt="Tramontina" class="w-full h-full object-contain">
                            </div>
                            <div>
                                <span class="font-bold text-lg">Tramontina</span>
                                <p class="text-blue-100 text-sm">Sistema Corporativo</p>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-3">Enlaces Rápidos</h4>
                            <ul class="space-y-2 text-sm">
                                <li><a href="{{ route('dashboard') }}" class="text-blue-100 hover:text-white transition-colors">Dashboard</a></li>
                                <li><a href="{{ route('products.index') }}" class="text-blue-100 hover:text-white transition-colors">Productos</a></li>
                                <li><a href="#" class="text-blue-100 hover:text-white transition-colors">Auditoría</a></li>
                                <li><a href="#" class="text-blue-100 hover:text-white transition-colors">Reportes</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-3">Sistema</h4>
                            <div class="text-sm text-blue-100 space-y-1">
                                <p>Laravel v{{ Illuminate\Foundation\Application::VERSION }}</p>
                                <p>PHP v{{ PHP_VERSION }}</p>
                                <p>PostgreSQL</p>
                                <p>&copy; {{ date('Y') }} Tramontina</p>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        @stack('scripts')

        <!-- Scripts adicionales -->
        <script>
            // Función para mostrar notificaciones
            function showNotification(message, type = 'success') {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
                    type === 'success' ? 'bg-green-500' : 'bg-red-500'
                } text-white`;
                notification.textContent = message;
                
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.classList.add('opacity-0', 'transform', 'translate-x-full');
                    setTimeout(() => {
                        document.body.removeChild(notification);
                    }, 300);
                }, 3000);
            }

            // Auto-hide alerts después de 5 segundos
            document.addEventListener('DOMContentLoaded', function() {
                const alerts = document.querySelectorAll('[class*="bg-green-50"], [class*="bg-red-50"]');
                alerts.forEach(alert => {
                    setTimeout(() => {
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 300);
                    }, 5000);
                });
            });
        </script>
    </body>
</html>
