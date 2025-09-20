@extends('layouts.app')

@section('header', 'Stock Overview')

@push('styles')
<style>
    div.dt-container div.dt-search input {
        width: 15rem;
    }
</style>
@endpush

@section('content')
<div class="bg-white p-6 rounded-md shadow-sm">
    <div class="flex items-center justify-start space-x-4 mb-4">
        <button id="stockInBtn" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-block">
            Manual Stock In
        </button>
        <button id="stockOutBtn" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded inline-block">
            Manual Stock Out
        </button>
    </div>

    <table id="stocks-table" class="table table-bordered table-striped w-full">
        <thead>
            <tr>
                <th>No</th>
                <th>SKU</th>
                <th>Item Name</th>
                <th>Category</th>
                <th>Current Stock</th>
            </tr>
        </thead>
    </table>
</div>

@include('inventory.stocks.modal')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function () {
    var table = $('#stocks-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route('inventory.stocks.index') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'sku', name: 'sku' },
            { data: 'name', name: 'name' },
            { data: 'category', name: 'category' },
            { data: 'current_stock', name: 'current_stock', orderable: false, searchable: false }
        ],
        columnDefs: [{ targets: '_all', className: 'py-3 align-middle' }],
        dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>'
    });

    // --- LOGIKA MODAL ---

    function openStockModal(type) {
        $('#stockForm')[0].reset();
        $('.error-message').text('');
        
        if (type === 'in') {
            $('#modal_title').text('Manual Stock In');
            $('#stockForm').attr('action', '{{ route('inventory.stocks.in') }}');
        } else {
            $('#modal_title').text('Manual Stock Out');
            $('#stockForm').attr('action', '{{ route('inventory.stocks.out') }}');
        }
        
        $('#stockModal').removeClass('hidden');
    }

    $('#stockInBtn').on('click', function () {
        openStockModal('in');
    });

    $('#stockOutBtn').on('click', function () {
        openStockModal('out');
    });

    $('#cancelBtn, .close-modal').on('click', function () {
        $('#stockModal').addClass('hidden');
    });

    $('#stockForm').on('submit', function (e) {
        e.preventDefault();
        $('.error-message').text('');
        var url = $(this).attr('action');
        var formData = $(this).serialize();

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                $('#stockModal').addClass('hidden');
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: response.success, showConfirmButton: false, timer: 3000 });
                table.ajax.reload();
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    Swal.fire({ icon: 'error', title: 'Validation Error', text: 'Please check the form for errors.' });
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        $('#' + key + '_error').text(value[0]);
                    });
                } else {
                     Swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong! Please try again.' });
                }
            }
        });
    });
});
</script>
@endpush
