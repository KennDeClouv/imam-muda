<x-app>
    @php
        $user = Auth::user();
    @endphp
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-6">
                    <div class="user-profile-header-banner">
                        <img src="/assets/img/pages/profile-banner.png" alt="Banner image" class="rounded-top"
                            class="w-100">
                    </div>
                    <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-8">
                        <div class="flex-shrink-0 mt-1 mx-sm-0 mx-auto">
                            <img src="{{ $user->photo }}" alt="user image"
                                class="d-block h-auto ms-0 ms-sm-6 rounded-3 user-profile-img">
                        </div>
                        <div class="flex-grow-1 mt-3 mt-lg-5">
                            <div
                                class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-4">
                                <div class="user-profile-info">
                                    <h4 class="mb-2 mt-lg-7">{{ ucfirst($user->name) }}</h4>
                                    <ul
                                        class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-4 mt-4">
                                        <li class="list-inline-item">
                                            <span class="fw-medium">{{ ucfirst($user->Role->name ?? '-') }}</span>
                                        </li>
                                        <li class="list-inline-item">
                                            <span class="fw-medium">{{ $user->email }}</span>
                                        </li>
                                        <li class="list-inline-item">
                                            <span class="fw-medium">Terdaftar
                                                {{ $user->created_at->format('F Y') }}</span>
                                        </li>
                                    </ul>
                                </div>
                                <a href="#" class="btn btn-primary mb-1"
                                    onclick="event.preventDefault(); showLogoutConfirm();">
                                    <i class="fa fa-sign-out-alt fa-sm me-2"></i>Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Header -->

        <!-- User Profile Content -->
        @include('components.alert')
        <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-5">
                <!-- About User -->
                <div class="card mb-6 ">
                    <div class="card-body">
                        <small class="card-text text-uppercase text-muted small">About</small>
                        <div class="row mt-3">
                            <div class="col"><i class="text-primary fa fa-user me-2"></i> Nama</div>
                            <div class="col">: {{ $user->name ?? '-' }}</div>
                        </div>
                        <div class="row mt-3">
                            <div class="col"><i class="text-success fa fa-user me-2"></i> Username</div>
                            <div class="col">: {{ $user->username }}</div>
                        </div>
                        <div class="row mt-3">
                            <div class="col"><i class="text-info fa fa-crown me-2"></i> Role</div>
                            <div class="col">: {{ $user->Role->name ?? '-' }}</div>
                        </div>
                        <div class="row mt-3">
                            <div class="col"><i class="text-secondary fa fa-envelope me-2"></i> Email</div>
                            <div class="col">: {{ $user->email }}</div>
                        </div>
                        <div class="row mt-3">
                            <div class="col"><i class="text-warning fa fa-calendar-alt me-2"></i> Terdaftar
                            </div>
                            <div class="col">: {{ $user->created_at->format('d M Y') }}</div>
                        </div>
                    </div>
                </div>
                <!--/ About User -->
                @if (!$user->hasVerifiedEmail())
                    <div class="card mb-6">
                        <div class="card-body">
                            <h5 class="card-title">Verifikasi Email</h5>
                            <div class="alert alert-warning alert-dismissible mt-4" role="alert">
                                {{-- <h5 class="alert-heading mb-1">Pastikan bahwa persyaratan ini terpenuhi</h5> --}}
                                <span>Email kamu belum diverifikasi. Silahkan klik tombol dibawah ini untuk verifikasi
                                    email.</span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Tutup"></button>
                            </div>
                            <a href="{{ route('verification.notice') }}" class="btn btn-primary">
                                Verifikasi Email
                            </a>
                        </div>
                    </div>
                @endif
            </div>
            <div class="col-xl-8 col-lg-7 col-md-7">
                <form action="{{ route('account.update', $user->id) }}" method="POST" enctype="multipart/form-data"
                    id="AccountForm">
                    @csrf
                    @method('PUT')
                    <!-- Activity Timeline -->
                    <div class="card mb-6">
                        <!-- Account -->
                        <div class="card-body">
                            <div class="d-flex align-items-start align-items-sm-center gap-6 pb-4 border-bottom">
                                <img src="{{ $user->photo }}" alt="user-avatar"
                                    class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar">
                                <div class="button-wrapper">
                                    <label for="upload" class="btn btn-primary me-3 mb-4" tabindex="0">
                                        <span class="d-none d-sm-block">Upload foto baru</span>
                                        <i class="fa fa-upload d-block d-sm-none"></i>
                                        <input type="file" id="upload" class="account-file-input" hidden=""
                                            name="photo" accept="photo/*">
                                    </label>

                                    <div>Diizinkan JPG, JPEG, GIF atau PNG. Ukuran maksimum 5 Mb</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-4">
                            <div class="row g-6">
                                <div class="col-md-6 fv-plugins-icon-container">
                                    <label for="name" class="form-label">Nama</label>
                                    <input class="form-control" type="text" id="name" name="name"
                                        value="{{ $user->name }}">
                                    <div
                                        class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                    </div>
                                </div>
                                <div class="col-md-6 fv-plugins-icon-container">
                                    <label for="username" class="form-label">Username</label>
                                    <input class="form-control" type="text" name="username" id="username"
                                        value="{{ $user->username }}">
                                    <div
                                        class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                    </div>
                                </div>
                                <div class="col-md-6 fv-plugins-icon-container">
                                    <label for="email" class="form-label">Email</label>
                                    <input class="form-control" type="text" name="email" id="email"
                                        value="{{ $user->email }}">
                                    <div
                                        class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6">
                                <button type="submit" class="btn btn-primary me-3">Simpan Perubahan</button>
                                <button type="reset" class="btn btn-label-secondary">Batalkan</button>
                            </div>
                        </div>

                        <!-- /Account -->
                    </div>
                    <!--/ Activity Timeline -->
                    <div class="card mb-6">
                        <h5 class="card-header border-bottom mb-4">Ganti Password</h5>
                        <div class="card-body">
                            <form id="formChangePassword" method="GET" onsubmit="return false"
                                class="fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate">
                                <div class="alert alert-warning alert-dismissible" role="alert">
                                    <h5 class="alert-heading mb-1">Pastikan bahwa persyaratan ini terpenuhi</h5>
                                    <span>Minimal 8 karakter, huruf besar &amp; simbol</span>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Tutup"></button>
                                </div>
                                <div class="row gx-6">
                                    <div class="mb-4 col-12 col-sm-6 form-password-toggle fv-plugins-icon-container">
                                        <label class="form-label" for="newPassword">New Password</label>
                                        <div class="input-group input-group-merge has-validation">
                                            <input class="form-control" type="password" id="newPassword"
                                                name="password" placeholder="············">
                                            <span class="input-group-text cursor-pointer"><i
                                                    class="fa fa-eye-slash"></i></span>
                                        </div>
                                        <div
                                            class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                        </div>
                                    </div>

                                    <div class="mb-4 col-12 col-sm-6 form-password-toggle fv-plugins-icon-container">
                                        <label class="form-label" for="confirmPassword">Confirm New
                                            Password</label>
                                        <div class="input-group input-group-merge has-validation">
                                            <input class="form-control" type="password" name="password_confirmation"
                                                id="confirmPassword" placeholder="············">
                                            <span class="input-group-text cursor-pointer"><i
                                                    class="fa fa-eye-slash"></i></span>
                                        </div>
                                        <div
                                            class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                        </div>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary me-2">Ganti
                                            Password</button>
                                    </div>
                                </div>
                                <input type="hidden">
                            </form>
                        </div>
                    </div>
                </form>
                @switch($user->Role->code)
                    @case('imam')
                        {{-- Imam --}}
                        <form action="{{ route('imam.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card mb-6">
                                <!-- Account -->
                                <h5 class="card-header border-bottom mb-4">Informasi Tambahan</h5>
                                <div class="card-body pt-4">
                                    <div class="row g-6">
                                        <div class="col-md-6 fv-plugins-icon-container">
                                            <label for="fullname" class="form-label">Full Name</label>
                                            <input class="form-control" type="text" id="fullname" name="fullname"
                                                value="{{ $user->Imam->fullname ?? '-' }}">
                                            <div
                                                class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                            </div>
                                        </div>
                                        <div class="col-md-6 fv-plugins-icon-container">
                                            <label for="phone" class="form-label">Phone</label>
                                            <input class="form-control" type="text" id="phone" name="phone"
                                                value="{{ $user->Imam->phone ?? '-' }}">
                                            <div
                                                class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                            </div>
                                        </div>
                                        <div class="col-md-6 fv-plugins-icon-container">
                                            <label for="birthplace" class="form-label">Birthplace</label>
                                            <input class="form-control" type="text" id="birthplace" name="birthplace"
                                                value="{{ $user->Imam->birthplace ?? '-' }}">
                                            <div
                                                class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                            </div>
                                        </div>
                                        <div class="col-md-6 fv-plugins-icon-container">
                                            <label for="birthdate" class="form-label">Birthdate</label>
                                            <input class="form-control" type="date" id="birthdate" name="birthdate"
                                                value="{{ $user->Imam->birthdate ?? '-' }}">
                                            <div
                                                class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                            </div>
                                        </div>
                                        <div class="col-md-6 fv-plugins-icon-container">
                                            <label for="juz" class="form-label">Juz</label>
                                            <input class="form-control" type="number" id="juz" name="juz"
                                                value="{{ $user->Imam->juz ?? '-' }}">
                                            <div
                                                class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label" for="join_date">Tanggal Bergabung</label>
                                            <input type="date" id="join_date" class="form-control" name="join_date"
                                                value="{{ old('join_date', $user->Imam->join_date) }}" disabled>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label" for="no_rekening">Nomor Rekening</label>
                                            <input type="text" id="no_rekening" class="form-control"
                                                placeholder="Nomor Rekening" name="no_rekening"
                                                value="{{ old('no_rekening', $user->Imam->no_rekening) }}">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label" for="status">Status</label>
                                            <select id="status" class="form-select" name="status">
                                                <option value="belum nikah"
                                                    {{ old('status', $user->Imam->status) == 'belum nikah' ? 'selected' : '' }}>
                                                    Belum Nikah
                                                </option>
                                                <option value="nikah"
                                                    {{ old('status', $user->Imam->status) == 'nikah' ? 'selected' : '' }}>
                                                    Nikah
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label" for="child_count">Jumlah Anak</label>
                                            <input type="number" id="child_count" class="form-control"
                                                placeholder="Jumlah Anak" min="0" name="child_count"
                                                value="{{ old('child_count', $user->Imam->child_count) }}">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label" for="wife_count">Jumlah Istri</label>
                                            <input type="number" id="wife_count" class="form-control"
                                                placeholder="Jumlah Istri" min="0" name="wife_count"
                                                value="{{ old('wife_count', $user->Imam->wife_count) }}">
                                        </div>
                                        <div class="col-md-6 fv-plugins-icon-container">
                                            <label for="school" class="form-label">Pendidikan Terakhir</label>
                                            <input class="form-control" type="text" id="school" name="school"
                                                value="{{ $user->Imam->school ?? '-' }}">
                                            <div
                                                class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                            </div>
                                        </div>
                                        <div class=" fv-plugins-icon-container">
                                            <label for="address" class="form-label">Alamat</label>
                                            <input class="form-control" type="text" id="address" name="address"
                                                value="{{ $user->Imam->address ?? '-' }}">
                                            <div
                                                class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-6">
                                        <button type="submit" class="btn btn-primary me-3">Simpan Perubahan</button>
                                        <button type="reset" class="btn btn-label-secondary">Batalkan</button>
                                    </div>
                                </div>
                                <!-- /Account -->
                            </div>
                        </form>
                        {{-- / Imam --}}
                    @break

                    @case('admin')
                        <form action="{{ route('admin.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card mb-6">
                                <!-- Account -->
                                <h5 class="card-header border-bottom mb-4">Informasi Tambahan</h5>
                                <div class="card-body pt-4">
                                    <div class="row g-6">
                                        <div class="col-md-6 fv-plugins-icon-container">
                                            <label for="fullname" class="form-label">Nama</label>
                                            <input class="form-control" type="text" id="fullname" name="fullname"
                                                value="{{ $user->Admin->fullname ?? '-' }}">
                                            <div
                                                class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                            </div>
                                        </div>
                                        <div class="col-md-6 fv-plugins-icon-container">
                                            <label for="phone" class="form-label">No. Telp</label>
                                            <input class="form-control" type="text" id="phone" name="phone"
                                                value="{{ $user->Admin->phone ?? '-' }}">
                                            <div
                                                class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                            </div>
                                        </div>
                                        <div class="col-md-6 fv-plugins-icon-container">
                                            <label for="birthplace" class="form-label">Tempat Lahir</label>
                                            <input class="form-control" type="text" id="birthplace" name="birthplace"
                                                value="{{ $user->Admin->birthplace ?? '-' }}">
                                            <div
                                                class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                            </div>
                                        </div>
                                        <div class="col-md-6 fv-plugins-icon-container">
                                            <label for="birthdate" class="form-label">Tanggal Lahir</label>
                                            <input class="form-control" type="date" id="birthdate" name="birthdate"
                                                value="{{ $user->Admin->birthdate ?? '-' }}">
                                            <div
                                                class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                            </div>
                                        </div>

                                        <div class=" fv-plugins-icon-container">
                                            <label for="address" class="form-label">Alamat</label>
                                            <input class="form-control" type="text" id="address" name="address"
                                                value="{{ $user->Admin->address ?? '-' }}">
                                            <div
                                                class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                            </div>
                                        </div>
                                        <div class="fv-plugins-icon-container">
                                            <label for="description" class="form-label">Deskripsi</label>
                                            <textarea class="form-control" id="description" name="description">{{ $user->Admin->description ?? '-' }}</textarea>
                                            <div
                                                class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-6">
                                        <button type="submit" class="btn btn-primary me-3">Simpan Perubahan</button>
                                        <button type="reset" class="btn btn-label-secondary">Batalkan</button>
                                    </div>
                                </div>
                                <!-- /Account -->
                            </div>
                        </form>
                    @break

                    @default
                        {{-- Default --}}
                    @break
                @endswitch
            </div>
        </div>
        <!--/ User Profile Content -->
    </div>
    <!-- Modal untuk Crop Gambar -->
    <div class="modal fade" id="cropModal" tabindex="-1" aria-labelledby="cropModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header pb-4">
                    <h5 class="modal-title" id="cropModalLabel">Crop Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <img id="imagePreview">
                <div class="modal-footer pt-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="cropButton">Crop dan Upload</button>
                </div>
            </div>
        </div>
    </div>
    <x-slot:style>
        <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-profile.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/cropper-js/cropper-js.css') }}">
    </x-slot:style>
    <x-slot:js>
        <script src="{{ asset('assets/vendor/libs/cropper-js/cropper-js.js') }}"></script>
        <script>
            const htmlStyle = document.documentElement.getAttribute('data-style');
            const isDarkMode = htmlStyle === 'dark' || (htmlStyle !== 'light' && window.matchMedia(
                '(prefers-color-scheme: dark)').matches);

            function showLogoutConfirm() {
                Swal.fire({
                    title: 'Konfirmasi Logout',
                    text: 'Apakah anda yakin ingin logout dari akun ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Logout',
                    cancelButtonText: 'Tidak',
                    confirmButtonColor: 'var(--bs-primary)',
                    cancelButtonColor: '#8592a3',
                    background: isDarkMode ? '#2b2c40' : '#fff',
                    color: isDarkMode ? '#b2b2c4' : '#000',
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('logout-form').submit();
                    }
                });
            }
            $(document).ready(function() {
                let cropper;

                // Event listener untuk upload file
                $('#upload').on('change', function(event) {
                    const files = event.target.files;

                    if (files && files.length > 0) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const imagePreview = $('#imagePreview')[0];
                            imagePreview.src = e.target.result; // Tampilkan gambar di modal
                            $('#cropModal').modal('show'); // Tampilkan modal crop
                        };
                        reader.readAsDataURL(files[0]);
                    }
                });

                // Inisialisasi cropper saat modal ditampilkan
                $('#cropModal')
                    .on('shown.bs.modal', function() {
                        const image = $('#imagePreview')[0];

                        cropper = new Cropper(image, {
                            aspectRatio: 1, // Ratio 1:1, sesuaikan sesuai kebutuhan
                            viewMode: 1, // Crop di dalam boundary
                            autoCropArea: 1, // Full area crop
                            responsive: true, // Responsive untuk perubahan layar
                        });
                    })
                    .on('hidden.bs.modal', function() {
                        // Hancurkan cropper untuk mencegah memory leak
                        if (cropper) {
                            cropper.destroy();
                            cropper = null;
                        }
                    });

                // Event listener untuk tombol crop
                $('#cropButton').on('click', function() {
                    const accountForm = $('#AccountForm')[0];

                    if (cropper) {
                        const canvas = cropper.getCroppedCanvas({
                            width: 200, // Ukuran crop (opsional)
                            height: 200,
                        });

                        if (!canvas) {
                            Swal.fire({
                                title: 'Error',
                                text: 'Gagal membuat canvas dari gambar.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                background: isDarkMode ? '#2b2c40' : '#fff',
                                color: isDarkMode ? '#b2b2c4' : '#000',
                            });
                            return;
                        }

                        canvas.toBlob((blob) => {
                            if (!blob) {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Gagal menghasilkan blob dari canvas.',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    background: isDarkMode ? '#2b2c40' : '#fff',
                                    color: isDarkMode ? '#b2b2c4' : '#000',
                                });
                                return;
                            }

                            const reader = new FileReader();
                            reader.onloadend = () => {
                                const base64data = reader.result;

                                // Buat FormData baru
                                const formData = new FormData();
                                const accountFormData = new FormData(accountForm);

                                // Tambahkan base64 ke FormData
                                formData.append('photo', base64data);

                                // Tambahkan data form lainnya
                                for (const [key, value] of accountFormData.entries()) {
                                    if (key !== 'photo') {
                                        formData.append(key, value);
                                    }
                                }

                                // Kirim data ke backend menggunakan fetch
                                fetch(accountForm.action, {
                                        method: 'POST', // Ubah jika butuh method lain (PUT/POST)
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                                'content'),
                                        },
                                        body: formData,
                                    })
                                    .then((response) => {
                                        if (response.ok) {
                                            Swal.fire({
                                                title: 'Berhasil',
                                                text: 'Foto berhasil diunggah!',
                                                icon: 'success',
                                                confirmButtonText: 'OK',
                                                background: isDarkMode ? '#2b2c40' :
                                                    '#fff',
                                                color: isDarkMode ? '#b2b2c4' : '#000',
                                            }).then(() => {
                                                window.location.href =
                                                    '{{ route('account') }}';
                                            });
                                        } else {
                                            response.text().then((text) => {
                                                console.error('Response Error:', text);
                                                Swal.fire({
                                                    title: 'Gagal',
                                                    text: 'Terjadi kesalahan saat mengunggah foto.',
                                                    icon: 'error',
                                                    confirmButtonText: 'OK',
                                                    background: isDarkMode ?
                                                        '#2b2c40' : '#fff',
                                                    color: isDarkMode ?
                                                        '#b2b2c4' : '#000',
                                                });
                                            });
                                        }
                                    })
                                    .catch((error) => {
                                        console.error('Fetch Error:', error);
                                        Swal.fire({
                                            title: 'Gagal',
                                            text: 'Terjadi kesalahan saat mengunggah foto.',
                                            icon: 'error',
                                            confirmButtonText: 'OK',
                                            background: isDarkMode ? '#2b2c40' : '#fff',
                                            color: isDarkMode ? '#b2b2c4' : '#000',
                                        });
                                    });
                            };
                            reader.readAsDataURL(blob);
                        });
                    }
                });
            });
        </script>
    </x-slot:js>
</x-app>
