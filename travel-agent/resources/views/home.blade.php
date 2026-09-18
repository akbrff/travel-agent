<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Agent - Jelajahi Keindahan Nusantara</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">

    <!-- Header / Navbar -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-blue-600">TravelAgent</a>
            <nav class="space-x-6 hidden md:flex items-center">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-blue-600 font-medium">Beranda</a>
                <a href="{{ route('packages.index') }}" class="text-gray-600 hover:text-blue-600 font-medium">Paket Wisata</a>
            </nav>
            <div class="flex items-center space-x-4">
                @auth
                    <a href="/admin" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">Dashboard Admin</a>
                    <a href="{{ route('booking.history') }}" class="text-gray-600 hover:text-blue-600 text-sm font-medium">Riwayat Pesanan</a>
                    <a href="{{ route('profile.edit') }}" class="text-gray-600 hover:text-blue-600 text-sm font-medium">Profil Saya</a>
                    @if(auth()->user()->role === 'admin')
                        <a href="/admin" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">Dashboard Admin</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 text-sm font-medium">Masuk</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">Daftar</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-20 px-4">
        <div class="max-w-5xl mx-auto text-center space-y-6">
            <h1 class="text-4xl md:text-6xl font-extrabold leading-tight">Jelajahi Destinasi Impian Tanpa Khawatir</h1>
            <p class="text-lg md:text-xl text-blue-100 max-w-2xl mx-auto">Temukan berbagai paket liburan seru dengan harga terjangkau dan jadwal terlengkap di seluruh Indonesia.</p>
            <div class="pt-4">
                <a href="{{ route('packages.index') }}" class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-8 py-4 rounded-xl shadow-lg hover:shadow-xl transition text-lg inline-block">Cari Paket Wisata</a>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-2xl font-bold mb-8 text-gray-900">Kategori Pilihan</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @forelse($categories as $category)
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm text-center hover:shadow-md transition">
                    <h3 class="font-bold text-gray-800 text-lg">{{ $category->name }}</h3>
                </div>
            @empty
                <p class="text-gray-500 col-span-4">Belum ada kategori ketersediaan.</p>
            @endforelse
        </div>
    </section>

    <!-- Featured Packages Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-16">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Paket Wisata Populer</h2>
            <a href="{{ route('packages.index') }}" class="text-blue-600 hover:underline font-semibold text-sm">Lihat Semua &rarr;</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($featuredPackages as $pkg)
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
                <p class="text-gray-500 col-span-3">Belum ada paket wisata yang dipublikasikan.</p>
            @endforelse
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-8 text-center text-sm text-gray-500">
        &copy; {{ date('Y') }} TravelAgent. All rights reserved.
    </footer>

</body>
</html>