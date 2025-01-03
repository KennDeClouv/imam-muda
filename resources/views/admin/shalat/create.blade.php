<x-app>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-3">
            <div class="card-body">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.shalat.index') }}">Daftar Shalat</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah Shalat</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="card mb-6">
            <div class="card-header border-bottom mb-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Tambahkan Shalat</h5> <small class="text-body float-end">Data Shalat</small>
            </div>
            <div class="card-body">
                @include('components.alert')
                <form action="{{ route('admin.shalat.store') }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label class="form-label" for="shalat-name">Nama Shalat</label>
                        <input type="text" name="name" class="form-control" id="shalat-name" placeholder="Nama Shalat" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-6">
                        <label class="form-label" for="lama-shalat">Jam Mulai Shalat</label>
                        <input type="time" min="1" name="start" class="form-control" id="lama-shalat" placeholder="5 Menit" value="{{ old('minutes') }}" required>
                    </div>
                    <div class="mb-6">
                        <label class="form-label" for="lama-shalat">Jam Selesai Shalat</label>
                        <input type="time" min="1" name="end" class="form-control" id="lama-shalat" placeholder="5 Menit" value="{{ old('minutes') }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Tambahkan</button>
                </form>
            </div>
        </div>
    </div>
    <x-slot:style>
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bs-stepper/bs-stepper.css') }}">

    </x-slot:style>
    <x-slot:js>
        <script src="{{ asset('assets/js/form-wizard-numbered.js') }}"></script>

        <script src="{{ asset('assets/vendor/libs/bs-stepper/bs-stepper.js') }}"></script>
    </x-slot:js>
</x-app>
