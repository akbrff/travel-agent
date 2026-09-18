<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Paket Wisata - Travel Agent</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">

    <!-- Header / Navbar -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-blue-600">TravelAgent</a>
            <nav class="space-x-6 hidden md:flex items-center">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-blue-600 font-medium">Beranda</a>
                <a href="{{ route('packages.index') }}" class="text-blue-600 font-semibold">Paket Wisata</a>
            </nav>
            <div class="flex items-center space-x-4">
                @auth
                    <a href="/admin" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">Dashboard Admin</a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 text-sm font-medium">Masuk</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">Daftar</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Page Header -->
    <div class="bg-white border-b border-gray-200 py-10 px-4">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-extrabold text-gray-900">Semua Paket Wisata</h1>
            <p class="text-gray-500 mt-2">Temukan pilihan liburan terbaik yang siap kamu pesan kapan saja.</p>
        </div>
    </div>

    <!-- Catalog Grid -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($packages as $pkg)
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-lg transition flex flex-col justify-between">
                    <div>
                        <img src="{{ Storage::url($pkg->featured_image) }}" alt="{{ $pkg->title }}" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <span class="inline-block bg-blue-50 text-blue-600 text-xs px-3 py-1 rounded-full font-semibold mb-3">{{ $pkg->category->name ?? 'Uncategorized' }}</span>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $pkg->title }}</h3>
                            <p class="text-sm text-gray-500 mb-4">📍 {{ $pkg->location }} &bull; ⏱️ {{ $pkg->duration }}</p>
                        </div>
                    </div>
                    <div class="px-6 pb-6 pt-0 flex justify-between items-center border-t border-gray-50 mt-4">
                        <div>
                            <span class="text-xs text-gray-400 block">Mulai dari</span>
                            <span class="text-lg font-bold text-emerald-600">
                                @if($pkg->schedules->first())
                                    Rp {{ number_format($pkg->schedules->first()->price_per_person, 0, ',', '.') }}
                                @else
                                    Hubungi Admin
                                @endif
                            </span>
                        </div>
                        <a href="{{ route('packages.show', $pkg->slug) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">Detail</a>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 col-span-3 text-center py-12">Belum ada paket wisata tersedia saat ini.</p>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $packages->links() }}
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-8 text-center text-sm text-gray-500 mt-16">
        &copy; {{ date('Y') }} TravelAgent. All rights reserved.
    </footer>

</body>
</html>