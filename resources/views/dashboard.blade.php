<x-app-layout>
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header del Dashboard -->
        <div class="mb-8">
            <div class="flex items-center space-x-4 mb-4">
                <div class="w-16 h-16 bg-white rounded-xl shadow-lg flex items-center justify-center p-2">
                    <img src="{{ secure_asset('logo.png') }}" alt="Tramontina" class="w-full h-full object-contain">
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard</h1>
                    <p class="text-gray-600">Bienvenido al sistema corporativo de Tramontina</p>
                </div>
            </div>
        </div>

        <!-- Tarjetas de estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total de Productos -->
            <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Productos</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Product::count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Categorías -->
            <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Categorías</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Category::count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Usuarios -->
            <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Usuarios</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\User::count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Auditoría -->
            <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Logs Auditoría</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\AuditLog::count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones Rápidas</h3>
                <div class="space-y-3">
                    <a href="{{ route('products.index') }}" class="flex items-center p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span class="text-gray-700">Gestionar Productos</span>
                    </a>
                    <a href="#" class="flex items-center p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="text-gray-700">Ver Reportes</span>
                    </a>
                    <a href="#" class="flex items-center p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-gray-700">Configuración</span>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actividad Reciente</h3>
                <div class="space-y-3">
                    @php
                        $recentLogs = \App\Models\AuditLog::latest()->take(5)->get();
                    @endphp
                    @forelse($recentLogs as $log)
                        <div class="flex items-center p-2 rounded bg-gray-50">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-700">{{ $log->action }}</p>
                                <p class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No hay actividad reciente</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Información del sistema -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Sistema</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="font-medium text-gray-600">Laravel:</span>
                    <span class="text-gray-900">{{ Illuminate\Foundation\Application::VERSION }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-600">PHP:</span>
                    <span class="text-gray-900">{{ PHP_VERSION }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-600">Base de Datos:</span>
                    <span class="text-gray-900">PostgreSQL</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
