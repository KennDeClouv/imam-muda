<x-error>
    <div class="misc-wrapper text-center">
        <h1 class="mb-2 mx-2" style="line-height: 6rem;font-size: 6rem;">403</h1>
        <h4 class="mb-2 mx-2">Anda tidak diizinkan! 🔐</h4>
        <p class="mb-6 mx-2">Maaf anda tidak memiliki akses ke halaman ini. Kembali ke beranda!</p>
        <div class="d-flex gap-2">
            <a href="{{ url()->previous() }}" class="btn btn-primary">Kembali</a>
            <a href="{{ route('clear.cookie') }}" class="btn btn-secondary">Logout</a>
        </div>
    </div>
</x-error>
