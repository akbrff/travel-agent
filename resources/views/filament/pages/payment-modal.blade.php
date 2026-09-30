<div class="space-y-4 text-sm">
    @if($payment)
        <div class="grid grid-cols-2 gap-2 border-b pb-3">
            <div>
                <span class="text-gray-500 text-xs block">Bank Pengirim</span>
                <strong>{{ $payment->bank_name }}</strong>
            </div>
            <div>
                <span class="text-gray-500 text-xs block">Nama Pemilik Rekening</span>
                <strong>{{ $payment->account_name }}</strong>
            </div>
            <div class="col-span-2 pt-2">
                <span class="text-gray-500 text-xs block">Nominal Transfer</span>
                <strong class="text-emerald-600 text-base">Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong>
            </div>
        </div>

        <div>
            <span class="text-gray-500 text-xs block mb-2">Foto Bukti Transfer</span>
            <a href="{{ Storage::url($payment->proof_image) }}" target="_blank" title="Klik untuk memperbesar">
                <img src="{{ Storage::url($payment->proof_image) }}" alt="Bukti Transfer" class="w-full max-h-96 object-contain rounded-lg border shadow-sm">
            </a>
        </div>
    @else
        <p class="text-gray-500">Belum ada bukti pembayaran yang diunggah.</p>
    @endif
</div>