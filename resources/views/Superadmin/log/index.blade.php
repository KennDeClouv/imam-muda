<x-app>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-3">
            <div class="card-body">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Daftar Log</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Log Statistik</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-1">
                            <span class="fs-5 badge bg-primary rounded-3">Local: {{ $stats['local'] }}</span>
                            <span class="fs-5 badge bg-success rounded-3">Production: {{ $stats['production'] }}</span>
                            <span class="fs-5 badge bg-danger rounded-3">Error: {{ $stats['error'] }}</span>
                            <span class="fs-5 badge bg-warning rounded-3">Warning: {{ $stats['warning'] }}</span>
                            <span class="fs-5 badge bg-info rounded-3">Info: {{ $stats['info'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header border-bottom mb-4">
                <h5 class="card-title">Daftar Log</h5>
            </div>
            <div class="card-body pb-0">
                @include('components.alert')
            </div>
            <div class="card-datatable table-responsive text-start text-nowrap">
                <table class="table table-bordered table-responsive-sm table-responsive-md table-responsive-xl w-100"
                    id="dataTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Nama File</th>
                            <th>Ukuran (KB)</th>
                            <th>Terakhir Diubah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    @include('components.show')
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td>{{ $log['name'] }}</td>
                                <td>{{ $log['size'] }}</td>
                                <td>{{ date('Y-m-d H:i:s', $log['modified']) }}</td>
                                <td>
                                    <a href="{{ route('superadmin.logs.show', ['filename' => $log['name']]) }}"
                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-title="Lihat Log">
                                        <i class="fa fa-eye fs-5"></i>
                                    </a>
                                    <x-confirm-delete :route="route('superadmin.logs.destroy', $log['name'])" title="Hapus Log" />
                                    <a href="{{ route('superadmin.logs.download', ['filename' => $log['name']]) }}"
                                        class="btn btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-title="Download Log">
                                        <i class="fa fa-download fs-5"></i>
                                    </a>
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
                $('#dataTable').DataTable();
            });
        </script>
    </x-slot:js>
</x-app>