<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>E-Ticket - {{ $booking->booking_code }}</title>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.5; font-size: 13px; }
        .header-table { width: 100%; border-bottom: 2px solid #2563eb; padding-bottom: 12px; margin-bottom: 20px; }
        .header-title h2 { color: #2563eb; margin: 0; font-size: 22px; }
        .header-title p { margin: 2px 0 0; color: #666; font-size: 11px; }
        .badge-paid { background-color: #d1fae5; color: #065f46; padding: 4px 12px; border-radius: 12px; font-weight: bold; font-size: 11px; text-transform: uppercase; }
        .section-title { font-size: 14px; font-weight: bold; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; margin-top: 18px; margin-bottom: 10px; }
        .info-table, .passenger-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .info-table td { padding: 4px 0; vertical-align: top; }
        .passenger-table th, .passenger-table td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; }
        .passenger-table th { background-color: #f8fafc; font-weight: bold; font-size: 12px; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        .qrcode-box { text-align: right; }
    </style>
</head>
<body>

    <!-- Header dengan QR Code -->
    <table class="header-table">
        <tr>
            <td class="header-title" width="75%">
                <h2>TRAVEL AGENT E-TICKET</h2>
                <p>Kode Booking: <strong>{{ $booking->booking_code }}</strong> | Status: <span class="badge-paid">PAID / LUNAS</span></p>
            </td>
            <td class="qrcode-box" width="25%">
                <img src="data:image/svg+xml;base64, {!! base64_encode(SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(85)->generate($booking->booking_code)) !!}" alt="QR Code Verifikasi">
            </td>
        </tr>
    </table>

    <div class="section-title">Detail Pemesanan & Paket</div>
    <table class="info-table">
        <tr>
            <td width="30%"><strong>Nama Pemesan:</strong></td>
            <td>{{ $booking->user->name }} ({{ $booking->user->email }})</td>
        </tr>
        <tr>
            <td><strong>Paket Wisata:</strong></td>
            <td>{{ $booking->packageSchedule->travelPackage->title }}</td>
        </tr>
        <tr>
            <td><strong>Lokasi & Meeting Point:</strong></td>
            <td>{{ $booking->packageSchedule->travelPackage->location }} — {{ $booking->packageSchedule->travelPackage->meeting_point }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal Keberangkatan:</strong></td>
            <td>{{ \Carbon\Carbon::parse($booking->packageSchedule->departure_date)->format('d F Y') }}</td>
        </tr>
        <tr>
            <td><strong>Total Pembayaran:</strong></td>
            <td>Rp {{ number_format($booking->total_amount, 0, ',', '.') }} ({{ $booking->total_passengers }} Pax)</td>
        </tr>
    </table>

    <div class="section-title">Daftar Penumpang (Pax)</div>
    <table class="passenger-table">
        <thead>
            <tr>
                <th width="8%">No</th>
                <th>Nama Lengkap</th>
                <th>No. KTP / Paspor</th>
                <th>No. HP / WA</th>
            </tr>
        </thead>
        <tbody>
            @foreach($booking->passengers as $index => $passenger)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $passenger->name }}</td>
                    <td>{{ $passenger->id_number }}</td>
                    <td>{{ $passenger->phone ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>E-Ticket ini resmi dan tidak memerlukan tanda tangan basah. Harap tunjukkan QR Code pada E-Ticket ini saat registrasi di meeting point.</p>
        <p>&copy; {{ date('Y') }} TravelAgent. All rights reserved.</p>
    </div>

</body>
</html>