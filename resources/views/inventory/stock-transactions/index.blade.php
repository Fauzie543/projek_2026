@extends('layouts.app')

@section('header', 'Stock Transaction History')

@push('styles')
<style>
    div.dt-container div.dt-search input {
        width: 15rem;
    }
</style>
@endpush

@section('content')
<div class="bg-white p-6 rounded-md shadow-sm">
    <p class="text-gray-600 mb-4">This page displays a complete history of all stock movements (in, out, adjustments).</p>
    <table id="transactions-table" class="table table-bordered table-striped w-full">
        <thead>
            <tr>
                <th>No</th>
                <th>Date</th>
                <th>Item Name</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>Performed By</th>
                <th>Note</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    $('#transactions-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route('inventory.stock-transactions.index') }}',
        order: [[1, 'desc']], // Urutkan berdasarkan tanggal terbaru secara default
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'performed_at', name: 'performed_at' },
            { data: 'item_name', name: 'item.name' },
            { data: 'type', name: 'type' },
            { data: 'qty_formatted', name: 'qty' },
            { data: 'performed_by_name', name: 'user.name' },
            { data: 'note', name: 'note', orderable: false, searchable: false }
        ],
        columnDefs: [{ targets: '_all', className: 'py-3 align-middle' }],
        dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>'
    });
});
</script>
@endpush
