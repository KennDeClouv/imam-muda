<x-app>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-3">
            <div class="card-body">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Masjid</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="card mb-6">
            <div class="card-header border-bottom mb-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit Masjid</h5> <small class="text-body float-end">Data Masjid</small>
            </div>
            <div class="card-body">
                @include('components.alert')
                <form action="{{ route('admin.masjid.update', $masjid->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-6">
                        <label class="form-label" for="masjid-name">Nama Masjid</label>
                        <input type="text" name="name" class="form-control" id="masjid-name" placeholder="Nama Masjid"
                            value="{{ old('name', $masjid->name) }}" required>
                    </div>
                    <div class="mb-6">
                        <label class="form-label" for="masjid-address">Alamat</label>
                        <textarea name="address" class="form-control" id="masjid-address" placeholder="Alamat lengkap Masjid" required>{{ old('address', $masjid->address) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-warning">Edit</button>
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