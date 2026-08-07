<footer class="customer-footer">
    <div class="customer-footer-brand">
        <img src="{{ asset('images/logo.png') }}?v=5" alt="Essensia Koffie">
        <div>
            <strong>Essensia Koffie</strong>
            <small>Fresh Coffee, Good Vibes.</small>
        </div>
    </div>

    <div class="customer-footer-info">
        <span><i class="bi bi-cup-hot"></i> Coffee & Space</span>
        @if(session('table_number'))
            <span><i class="bi bi-grid-3x3-gap"></i> {{ session('table_number') }}</span>
        @endif
        <span>{{ now()->format('Y') }}</span>
    </div>
</footer>
