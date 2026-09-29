<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - Travel Agent</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased flex flex-col min-h-screen">

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
                    @if(auth()->user()->role === 'admin')
                        <a href="/admin" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                            Dashboard Admin
                        </a>
                    @endif

                    <a href="{{ route('booking.history') }}" class="text-blue-600 font-semibold text-sm">
                        Riwayat Pesanan
                    </a>

                    <div class="flex items-center space-x-3 border-l border-gray-200 pl-4">
                        <a href="{{ route('profile.edit') }}" class="text-gray-700 hover:text-blue-600 text-sm font-semibold flex items-center gap-1">
                            👤 <span>Profil Saya</span>
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-rose-50 text-rose-600 hover:bg-rose-100 px-3 py-1.5 rounded-lg text-xs font-semibold transition border border-rose-200">
                                Keluar
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 text-sm font-medium">Masuk</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">Daftar</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Content -->
    <main class="max-w-5xl mx-auto px-4 py-10 flex-grow w-full">
        <h1 class="text-2xl font-bold mb-6 text-gray-900">Riwayat Pemesanan Anda</h1>

        <div class="space-y-4">
            @forelse($bookings as $booking)
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col md:flex-row justify-between md:items-center space-y-4 md:space-y-0">
                    
                    <!-- Info Booking -->
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-md">#{{ $booking->booking_code }}</span>
                        <h3 class="text-lg font-bold text-gray-900 pt-1">
                            {{ $booking->packageSchedule->travelPackage->title ?? 'Paket Wisata' }}
                        </h3>
                        <p class="text-xs text-gray-500">
                            📍 Lokasi: {{ $booking->packageSchedule->travelPackage->location ?? '-' }} &bull; 
                            📅 Keberangkatan: <strong>{{ \Carbon\Carbon::parse($booking->packageSchedule->departure_date)->format('d M Y') }}</strong> &bull; 
                            👥 {{ $booking->total_passengers }} Pax
                        </p>
                    </div>

                    <!-- Status, Harga, & Aksi -->
                    <div class="flex flex-col md:flex-row items-start md:items-center gap-4 justify-between md:justify-end border-t md:border-t-0 pt-3 md:pt-0">
                        <div class="text-left md:text-right">
                            <span class="text-xs text-gray-400 block">Total Biaya</span>
                            <span class="font-bold text-emerald-600 text-base">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                        </div>

                        <!-- Badge Status -->
                        <div>
                            @if($booking->status === 'paid')
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                    Lunas (Paid)
                                </span>
                            @elseif($booking->status === 'waiting_verification')
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">
                                    Verifikasi
                                </span>
                            @elseif($booking->status === 'pending')
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @elseif($booking->status === 'cancelled')
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800">
                                    Batal
                                </span>
                            @elseif($booking->status === 'completed')
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Selesai
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            @endif
                        </div>

                        <!-- Tombol Aksi Kustom -->
                        <div class="pt-2 md:pt-0">
                            @if($booking->status === 'paid' || $booking->status === 'completed')
                                <a href="{{ route('booking.ticket', $booking->booking_code) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2 px-3 rounded-xl transition inline-flex items-center gap-1 shadow-sm">
                                    📄 Cetak E-Ticket (PDF)
                                </a>
                            @elseif($booking->status === 'pending')
                                <a href="{{ route('booking.checkout', $booking->booking_code) }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 px-3 rounded-xl transition inline-flex items-center gap-1 shadow-sm">
                                    💳 Bayar Sekarang
                                </a>
                            @elseif($booking->status === 'waiting_verification')
                                <span class="text-xs text-amber-600 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-xl font-medium block">
                                    ⏳ Menunggu Verifikasi
                                </span>
                            @endif
                        </div>

                    </div>
                </div>
            @empty
                <div class="bg-white p-12 text-center rounded-2xl border border-gray-100 text-gray-500 space-y-3 shadow-sm">
                    <p class="text-base font-medium">Belum ada riwayat pemesanan paket wisata.</p>
                    <a href="{{ route('packages.index') }}" class="inline-block bg-blue-600 text-white font-semibold text-sm px-5 py-2.5 rounded-xl hover:bg-blue-700 transition">
                        Cari Paket Wisata
                    </a>
                </div>
            @endforelse
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-6 text-center text-sm text-gray-500 mt-12">
        &copy; {{ date('Y') }} TravelAgent. All rights reserved.
    </footer>

</body>
</html>