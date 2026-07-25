@php
    $qrs = app(\App\Filament\Pages\VerQrs::class)->getQrs();
@endphp

<x-filament-panels::page>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        @forelse ($qrs as $qr)
            <div class="flex flex-col items-center bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <img src="{{ asset('storage/' . $qr['qr']) }}"
                     alt="QR {{ $qr['billetera_tipo']['nombre'] ?? 'Billetera' }}"
                     class="w-full max-w-[180px] h-auto rounded-lg cursor-pointer"
                     onclick="openQrModal(this.src, '{{ addslashes($qr['billetera_tipo']['nombre'] ?? '') }}', '{{ addslashes($qr['titular'] ?? '') }}', '{{ addslashes($qr['telefono'] ?? '') }}')">
                <p class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ $qr['billetera_tipo']['nombre'] ?? '—' }}
                </p>
                @if($qr['telefono'])
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $qr['telefono'] }}</p>
                @endif
                @if($qr['titular'])
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $qr['titular'] }}</p>
                @endif
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500 dark:text-gray-400">
                No hay códigos QR registrados. Agrega uno en Métodos de Pago.
            </div>
        @endforelse
    </div>

    <div id="qrModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60" onclick="closeQrModal()">
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-2xl max-w-md w-full mx-4" onclick="event.stopPropagation()">
            <button onclick="closeQrModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-2xl leading-none">&times;</button>
            <p id="qrModalTitle" class="text-lg font-semibold text-center mb-1 text-gray-900 dark:text-gray-100"></p>
            <p id="qrModalTelefono" class="text-sm text-center text-gray-500 dark:text-gray-400 mb-4"></p>
            <img id="qrModalImg" src="" alt="QR" class="w-full max-w-[280px] mx-auto rounded-lg">
        </div>
    </div>

    <script>
        function openQrModal(src, tipo, titular, telefono) {
            const modal = document.getElementById('qrModal');
            document.getElementById('qrModalImg').src = src;
            document.getElementById('qrModalTitle').textContent = tipo + (titular ? ' — ' + titular : '');
            document.getElementById('qrModalTelefono').textContent = telefono ? '📱 ' + telefono : '';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
        function closeQrModal() {
            const modal = document.getElementById('qrModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeQrModal();
        });
    </script>
</x-filament-panels::page>
