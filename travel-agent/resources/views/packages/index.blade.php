<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Paket Wisata - TravelAgent</title>
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
                    @if(auth()->user()->role === 'admin')
                        <a href="/admin" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">Dashboard Admin</a>
                    @endif
                    <a href="{{ route('booking.history') }}" class="text-gray-600 hover:text-blue-600 text-sm font-medium">Riwayat Pesanan</a>
                    <a href="{{ route('profile.edit') }}" class="text-gray-700 hover:text-blue-600 text-sm font-semibold">👤 Profil Saya</a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 text-sm font-medium">Masuk</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">Daftar</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-6">Jelajahi Paket Wisata</h1>

        <!-- Form Filter Interaktif -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mb-8">
            <form action="{{ route('packages.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search Input -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pencarian</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau lokasi..." class="w-full border-gray-300 border p-2.5 rounded-xl text-sm">
                </div>

                <!-- Select Category -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kategori</label>
                    <select name="category_id" class="w-full border-gray-300 border p-2.5 rounded-xl text-sm">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Maximum Price Filter -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Budget Maksimal (Rp)</label>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Contoh: 1500000" class="w-full border-gray-300 border p-2.5 rounded-xl text-sm">
                </div>

                <!-- Filter & Reset Buttons -->
                <div class="flex items-end space-x-2">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-xl text-sm transition">
                        🔍 Cari Paket
                    </button>
                    @if(request()->anyFilled(['search', 'category_id', 'max_price']))
                        <a href="{{ route('packages.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2.5 px-3 rounded-xl text-sm transition" title="Reset Filter">
                            🔄
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Listing Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($packages as $package)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <img src="{{ Storage::url($package->featured_image) }}" alt="{{ $package->title }}" class="w-full h-48 object-cover">
                        <div class="p-5 space-y-2">
                            <span class="bg-blue-50 text-blue-600 text-xs px-2.5 py-1 rounded-md font-semibold">{{ $package->category->name ?? 'Wisata' }}</span>
                            <h3 class="text-lg font-bold text-gray-900 leading-snug">{{ $package->title }}</h3>
                            <p class="text-xs text-gray-500">📍 {{ $package->location }} &bull; ⏱️ {{ $package->duration }}</p>
                        </div>
                    </div>
                    <div class="p-5 border-t border-gray-100 flex items-center justify-between">
                        <div>
                            <span class="text-xs text-gray-400 block">Mulai dari</span>
                            <span class="text-base font-extrabold text-blue-600">
                                Rp {{ number_format($package->schedules->min('price_per_person') ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                        <a href="{{ route('packages.show', $package->slug) }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 px-3 rounded-xl transition">
                            Detail Paket
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 bg-white p-12 text-center rounded-2xl border border-gray-100 text-gray-500">
                    Tidak ada paket wisata yang sesuai dengan kriteria pencarian Anda.
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $packages->links() }}
        </div>
    </main>

</body>
</html>