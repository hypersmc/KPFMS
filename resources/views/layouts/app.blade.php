<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Printer Farm</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">

<div class="min-h-screen flex flex-col">
    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-200 shadow-md sticky top-0 z-40">
        <div class="max-w-screen-xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="text-lg font-bold">Printer Farm</div>
            <div class="flex space-x-4 items-center">
                @auth
                    <span class="font-semibold">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="font-semibold text-red-600">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="font-semibold">Login</a>
                    <a href="{{ route('register') }}" class="font-semibold">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="flex flex-1">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-gray-100 flex-shrink-0">
            <div class="h-16 flex items-center justify-center text-xl font-bold border-b border-gray-700">Farm Panel</div>
            <nav class="p-4 space-y-2">
                <a href="/dashboard" class="block px-4 py-2 rounded hover:bg-gray-700 font-semibold">🏠 Dashboard</a>
                @can('printer-view')
                    <a href="/printers" class="block px-4 py-2 rounded hover:bg-gray-700 font-semibold">🖨️ Printers</a>
                @endcan
                @can('job-view')
                    <a href="/jobs" class="block px-4 py-2 rounded hover:bg-gray-700 font-semibold">📦 Jobs</a>
                @endcan
                @can('file-view')
                    <a href="/files" class="block px-4 py-2 rounded hover:bg-gray-700 font-semibold">📁 Files</a>
                @endcan
                @can('settings-access')
                    <a href="/settings" class="block px-4 py-2 rounded hover:bg-gray-700 font-semibold">⚙️ Settings</a>
                @endcan
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 bg-gray-100 p-6 overflow-y-auto">
            <!-- Summary Boxes -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
                <div class="bg-blue-500 text-white rounded shadow-lg p-5">
                    <div class="text-sm">Total Printers</div>
                    <div class="text-3xl font-bold">5</div>
                    <div class="text-sm">+3 online</div>
                </div>
                <div class="bg-green-500 text-white rounded shadow-lg p-5">
                    <div class="text-sm">Active Jobs</div>
                    <div class="text-3xl font-bold">2</div>
                    <div class="text-sm">+1 from yesterday</div>
                </div>
                <div class="bg-yellow-400 text-white rounded shadow-lg p-5">
                    <div class="text-sm">Queued Files</div>
                    <div class="text-3xl font-bold">7</div>
                    <div class="text-sm">Waiting to print</div>
                </div>
                <div class="bg-red-500 text-white rounded shadow-lg p-5">
                    <div class="text-sm">Completed Jobs</div>
                    <div class="text-3xl font-bold">124</div>
                    <div class="text-sm">+5 today</div>
                </div>
            </div>

            <!-- Main Card Boxed Area -->
            <div class="bg-white rounded shadow-md p-6">
                @yield('content')
            </div>
        </main>
    </div>
</div>

</body>
</html>
