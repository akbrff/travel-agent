<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - Travel Agent</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-blue-600">TravelAgent</a>
            <nav class="space-x-6 hidden md:flex items-center">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-blue-600 font-medium">Beranda</a>
                <a href="{{ route('packages.index') }}" class="text-gray-600 hover:text-blue-600 font-medium">Paket Wisata</a>
            </nav>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-10">
        <h1 class="text-2xl font-bold mb-6">Riwayat Pemesanan Anda</h1>

        <div class="space-y-4">
            @forelse($bookings as $booking)
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col md:flex-row justify-between md:items-center space-y-4 md:space-y-0">
                    <div>
                        <span class="text-xs font-semibold text-gray-400">#{{ $booking->booking_code }}</span>
                        <h3 class="text-lg font-bold text-gray-900 mt-1">{{ $booking->packageSchedule->travelPackage->title ?? 'Paket Wisata' }}</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Keberangkatan: {{ \Carbon\Carbon::parse($booking->packageSchedule->departure_date)->format('d M Y') }} &bull; {{ $booking->total_passengers }} Pax
                        </p>
                    </div>

                    <div class="flex items-center space-x-6 justify-between md:justify-end">
                        <div class="text-right">
                            <span class="text-xs text-gray-400 block">Total Biaya</span>
                            <span class="font-bold text-emerald-600 text-base">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                        </div>
                        
                        <span class="px-3 py-1 text-xs font-semibold rounded-full 
                            {{ $booking->status === 'paid' ? 'bg-emerald-100 text-emerald-800' : '' }}
                            {{ $booking->status === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                            {{ $booking->status === 'cancelled' ? 'bg-rose-100 text-rose-800' : '' }}
                            {{ $booking->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 text-center rounded-2xl border border-gray-100 text-gray-500">
                    Belum ada riwayat pemesanan. <a href="{{ route('packages.index') }}" class="text-blue-600 font-semibold hover:underline">Cari Paket Wisata</a>
                </div>
            @endforelse
        </div>
    </main>

</body>
</html>