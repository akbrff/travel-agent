<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $package->title }} - Travel Agent</title>
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

    <!-- Content Detail -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Information Column -->
            <div class="lg:col-span-2 space-y-6">
                <img src="{{ Storage::url($package->featured_image) }}" alt="{{ $package->title }}" class="w-full h-80 md:h-96 object-cover rounded-2xl shadow-sm">

                <div class="bg-white p-8 rounded-2xl border border-gray-100 space-y-4">
                    <span class="bg-blue-50 text-blue-600 text-xs px-3 py-1 rounded-full font-semibold">{{ $package->category->name ?? 'Uncategorized' }}</span>
                    <h1 class="text-3xl font-extrabold text-gray-900">{{ $package->title }}</h1>
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        <span>📍 Lokasi: <strong>{{ $package->location }}</strong></span>
                        <span>⏱️ Durasi: <strong>{{ $package->duration }}</strong></span>
                        <span>🚩 Meeting Point: <strong>{{ $package->meeting_point }}</strong></span>
                    </div>

                    <hr class="my-4 border-gray-100">

                    <h3 class="text-lg font-bold text-gray-900">Deskripsi Paket</h3>
                    <div class="prose max-w-none text-gray-600 text-sm leading-relaxed">
                        {!! $package->description !!}
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                        <div class="bg-emerald-50 p-4 rounded-xl">
                            <h4 class="font-bold text-emerald-800 text-sm mb-2">✅ Fasilitas Termasuk:</h4>
                            <p class="text-xs text-emerald-700 whitespace-pre-line">{{ $package->included }}</p>
                        </div>
                        <div class="bg-rose-50 p-4 rounded-xl">
                            <h4 class="font-bold text-rose-800 text-sm mb-2">❌ Fasilitas Tidak Termasuk:</h4>
                            <p class="text-xs text-rose-700 whitespace-pre-line">{{ $package->excluded }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Widget Column -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-md sticky top-24 space-y-6">
                    <h3 class="text-xl font-bold text-gray-900">Pesan Sekarang</h3>
                    
                    @if($package->schedules->count() > 0)
                        @auth
                            <!-- Form Pemesanan (Hanya Tampil Jika Sudah Login) -->
                            <form action="{{ route('booking.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Tanggal Keberangkatan</label>
                                    <select name="schedule_id" class="w-full border-gray-300 border p-3 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm" required>
                                        @foreach($package->schedules as $sched)
                                            <option value="{{ $sched->id }}">
                                                {{ \Carbon\Carbon::parse($sched->departure_date)->format('d M Y') }} - Rp {{ number_format($sched->price_per_person, 0, ',', '.') }}/pax (Sisa: {{ $sched->remaining_quota }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Peserta (Pax)</label>
                                    <input type="number" name="passengers" min="1" value="1" class="w-full border-gray-300 border p-3 rounded-xl text-sm" required>
                                </div>

                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow transition">Lanjut ke Pemesanan</button>
                            </form>
                        @else
                            <!-- Boks Peringatan (Tampil Jika Belum Login) -->
                            <div class="bg-blue-50 border border-blue-100 p-5 rounded-xl text-center space-y-3">
                                <p class="text-sm text-blue-900 font-medium">Kamu harus terdaftar dan masuk ke akun terlebih dahulu untuk memesan paket ini.</p>
                                <div class="flex space-x-2 pt-1">
                                    <a href="{{ route('register') }}" class="w-1/2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-3 rounded-xl text-sm transition">Daftar Akun</a>
                                    <a href="{{ route('login') }}" class="w-1/2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2.5 px-3 rounded-xl text-sm transition">Masuk</a>
                                </div>
                            </div>
                        @endauth
                    @else
                        <div class="bg-amber-50 text-amber-700 p-4 rounded-xl text-sm">
                            Maaf, belum ada jadwal keberangkatan aktif untuk paket wisata ini.
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-8 text-center text-sm text-gray-500 mt-16">
        &copy; {{ date('Y') }} TravelAgent. All rights reserved.
    </footer>

</body>
</html>