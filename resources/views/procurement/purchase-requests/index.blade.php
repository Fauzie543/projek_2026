@extends('layouts.app')

@section('header', 'Purchase Requests')

@push('styles')
<style>
    div.dt-container div.dt-search input { width: 15rem; }
</style>
@endpush

@section('content')
<div class="bg-white p-6 rounded-md shadow-sm">
    <button id="addPrBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
        Create Purchase Request
    </button>

    <table id="pr-table" class="table table-bordered table-striped w-full">
        <thead>
            <tr>
                <th>No</th>
                <th>Request Date</th>
                <th>Needed By</th>
                <th>Requester</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>

@include('procurement.purchase-requests.modal')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function () {
    var table = $('#pr-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route('procurement.purchase-requests.index') }}',
        order: [[1, 'desc']],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'request_date', name: 'request_date' },
            { data: 'needed_by_date', name: 'needed_by_date' },
            { data: 'requester_name', name: 'requester.name' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        columnDefs: [{ targets: '_all', className: 'py-3 align-middle' }],
        dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>'
    });

    // --- LOGIKA FORM DINAMIS ---
    let itemIndex = 0;
    function addItemRow(item = null) {
        const template = $('#item-template').html().replace(/INDEX/g, itemIndex);
        const newRow = $(template);
        if (item) {
            newRow.find('.item-id').val(item.inventory_item_id);
            newRow.find('.item-qty').val(item.qty);
            newRow.find('.item-notes').val(item.notes);
        }
        $('#items-container').append(newRow);
        itemIndex++;
    }
    $('#addItemBtn').on('click', function() { addItemRow(); });
    $('#items-container').on('click', '.remove-item-btn', function() { $(this).closest('.item-row').remove(); });

    // --- LOGIKA MODAL ---
    $('#addPrBtn').on('click', function () {
        $('#prForm')[0].reset();
        $('#items-container').empty();
        itemIndex = 0;
        addItemRow();
        $('.error-message').text('');
        $('#modal_title').text('New Purchase Request');
        $('#submitBtn').text('Submit Request');
        $('#form_method').val('POST');
        $('#prForm').attr('action', '{{ route('procurement.purchase-requests.store') }}');
        $('#prModal').removeClass('hidden');
    });

    $('#pr-table').on('click', '.edit-btn', function () {
        var prId = $(this).data('id');
        var url = `/procurement/purchase-requests/${prId}/edit`;
        $('#prForm')[0].reset();
        $('#items-container').empty();
        itemIndex = 0;
        $('.error-message').text('');
        $.ajax({
            url: url,
            method: 'GET',
            success: function(data) {
                $('#modal_title').text('Edit Purchase Request');
                $('#submitBtn').text('Update Request');
                $('#form_method').val('PUT');
                $('#prForm').attr('action', `/procurement/purchase-requests/${prId}`);

                $('#pr_id').val(data.id);
                $('#request_date').val(data.request_date);
                $('#needed_by_date').val(data.needed_by_date);
                $('#notes').val(data.notes);
                if (data.items && data.items.length > 0) {
                    data.items.forEach(item => addItemRow(item));
                } else {
                    addItemRow();
                }
                $('#prModal').removeClass('hidden');
            },
            error: function() { Swal.fire('Error', 'Could not fetch PR data.', 'error'); }
        });
    });

    $('#cancelBtn, .close-modal').on('click', function () { $('#prModal').addClass('hidden'); });

    // --- SUBMIT & DELETE (AJAX) ---
    $('#prForm').on('submit', function (e) {
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
                $('#prModal').addClass('hidden');
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: response.success, showConfirmButton: false, timer: 3000 });
                table.ajax.reload();
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    Swal.fire({ icon: 'error', title: 'Validation Error', text: 'Please check the form for errors.' });
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        $('#' + key.replace(/\./g, '_') + '_error').text(value[0]);
                    });
                } else {
                     Swal.fire({ icon: 'error', title: 'Oops...', text: xhr.responseJSON.error || 'Something went wrong!' });
                }
            }
        });
    });

    $('#pr-table').on('click', '.delete-btn', function (e) {
        e.preventDefault();
        var deleteUrl = $(this).data('url');
        Swal.fire({
            title: 'Are you sure?', text: "You won't be able to revert this!", icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: deleteUrl,
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: response.success, showConfirmButton: false, timer: 3000 });
                        table.ajax.reload();
                    },
                    error: function(xhr) { Swal.fire('Failed!', xhr.responseJSON.error || 'There was a problem deleting the PR.', 'error'); }
                });
            }
        });
    });
});
</script>
@endpush
