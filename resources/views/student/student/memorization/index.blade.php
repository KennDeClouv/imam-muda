<x-app>
    @php
        $permissions = Auth::user()->getPermissionCodes();
    @endphp
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-3">
            <div class="card-body">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('student.home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Daftar Hafalan Kamu</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="card">
            <div class="card-header border-bottom mb-4">
                <h5 class="card-title">Daftar Hafalan Kamu</h5>
            </div>
            <div class="card-body pb-0">
                @include('components.alert')
                <div class="card-actions d-flex">
                    <a href="{{ route('student.student.memorization.create') }}" class="btn btn-primary ms-auto">Tambah
                        Hafalan</a>
                </div>

            </div>
            <div class="card-datatable table-responsive text-start text-nowrap">
                <table class="table table-bordered table-responsive-sm table-responsive-md table-responsive-xl w-100"
                    id="dataTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Surat</th>
                            <th>Dari</th>
                            <th>Sampai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    {{-- @include('components.show') --}}
                    <tbody>
                        @foreach ($memorizations as $memorization)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $memorization->Student->fullname }}</td>
                                <td>{{ getSurahName($memorization->surah_number) }}</td>
                                <td>{{ $memorization->from }}</td>
                                <td>{{ $memorization->to }}</td>
                                <td>
                                    <span
                                        class="badge d-block h-100 {{ is_null($memorization->is_continue) ? 'bg-warning' : ($memorization->is_continue == 1 ? 'bg-success' : 'bg-danger') }}">
                                        {{ is_null($memorization->is_continue) ? 'Belum Diperiksa' : ($memorization->is_continue == 1 ? 'Lulus' : 'Tidak Lulus') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2" aria-label="Basic example">
                                        <a href="{{ route('student.student.memorization.show', $memorization->id) }}"
                                            class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="top"
                                            data-bs-title="Detail Hafalan">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('student.student.memorization.edit', $memorization->id) }}"
                                            class="btn btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
                                            data-bs-title="Edit Hafalan">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                        <x-confirm-delete :route="route('student.student.memorization.destroy', $memorization->id)" title="Hapus Hafalan"
                                            message="Apakah anda yakin ingin menghapus hafalan ini?" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <x-slot:js>
        <script src="https://cdn.datatables.net/2.1.8/js/jquery.dataTables.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#dataTable').DataTable({
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.10.19/i18n/Indonesian.json"
                    }
                });
            });
        </script>
    </x-slot:js>
</x-app>
