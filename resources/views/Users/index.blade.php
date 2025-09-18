@extends('layouts.app')

@section('header', 'Users Management')

@section('content')
<div class="bg-white p-6 rounded-md shadow-sm">
    <table id="users-table" class="table table-bordered table-striped w-full">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route('users.data') }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'role', name: 'role', orderable: false, searchable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>'
    });
});
</script>
@endpush
