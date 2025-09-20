@extends('layouts.app')

@section('header', 'Feed Recipes Management')

@push('styles')
<style>
    div.dt-container div.dt-search input { width: 15rem; }
</style>
@endpush

@section('content')
<div class="bg-white p-6 rounded-md shadow-sm">
    <button id="addRecipeBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
        Add Feed Recipe
    </button>

    <table id="recipes-table" class="table table-bordered table-striped w-full">
        <thead>
            <tr>
                <th>No</th>
                <th>Recipe Name</th>
                <th>Components</th>
                <th>Est. Cost/Kg</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>

@include('inventory.feed-recipes.modal')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function () {
    var table = $('#recipes-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route('inventory.feed-recipes.index') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'components_list', name: 'components', orderable: false, searchable: false },
            { data: 'cost_per_kg', name: 'cost_per_kg' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        columnDefs: [{ targets: '_all', className: 'py-3 align-middle' }],
        dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>'
    });

    // --- LOGIKA FORM DINAMIS ---
    let componentIndex = 0;
    
    function addComponentRow(component = null) {
        const template = $('#component-template').html().replace(/INDEX/g, componentIndex);
        const newRow = $(template);
        if (component) {
            newRow.find('.component-item').val(component.item_id);
            newRow.find('.component-qty').val(component.qty);
        }
        $('#components-container').append(newRow);
        componentIndex++;
    }

    $('#addComponentBtn').on('click', function() {
        addComponentRow();
    });

    $('#components-container').on('click', '.remove-component-btn', function() {
        $(this).closest('.component-row').remove();
    });


    // --- LOGIKA MODAL ---
    $('#addRecipeBtn').on('click', function () {
        $('#recipeForm')[0].reset();
        $('#components-container').empty();
        componentIndex = 0;
        addComponentRow(); // Tambah satu baris kosong saat modal dibuka
        $('.error-message').text('');
        $('#modal_title').text('Add New Feed Recipe');
        $('#submitBtn').text('Save Recipe');
        $('#form_method').val('POST');
        $('#recipeForm').attr('action', '{{ route('inventory.feed-recipes.store') }}');
        $('#recipeModal').removeClass('hidden');
    });

    $('#recipes-table').on('click', '.edit-btn', function () {
        var recipeId = $(this).data('id');
        var url = `/inventory/feed-recipes/${recipeId}/edit`;

        $('#recipeForm')[0].reset();
        $('#components-container').empty();
        componentIndex = 0;
        $('.error-message').text('');

        $.ajax({
            url: url,
            method: 'GET',
            success: function(data) {
                $('#modal_title').text('Edit Feed Recipe');
                $('#submitBtn').text('Update Recipe');
                $('#form_method').val('PUT');
                $('#recipeForm').attr('action', `/inventory/feed-recipes/${recipeId}`);

                $('#recipe_id').val(data.id);
                $('#name').val(data.name);
                $('#description').val(data.description);
                $('#cost_per_kg').val(data.cost_per_kg).trigger('input');

                if (data.components && data.components.length > 0) {
                    data.components.forEach(comp => addComponentRow(comp));
                } else {
                    addComponentRow(); // Jika tidak ada komponen, tambahkan satu baris kosong
                }

                $('#recipeModal').removeClass('hidden');
            },
            error: function() {
                Swal.fire('Error', 'Could not fetch recipe data.', 'error');
            }
        });
    });

    $('#cancelBtn, .close-modal').on('click', function () {
        $('#recipeModal').addClass('hidden');
    });

    $('#recipeForm').on('submit', function (e) {
        e.preventDefault();
        $('.error-message').text('');
        
        var costInput = $('#cost_per_kg');
        var numericCost = unformatRupiah(costInput.val());
        costInput.val(numericCost);

        var url = $(this).attr('action');
        var formData = $(this).serialize();
        
        costInput.val(formatRupiah(numericCost.toString()));

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                $('#recipeModal').addClass('hidden');
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: response.success, showConfirmButton: false, timer: 3000 });
                table.ajax.reload();
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    Swal.fire({ icon: 'error', title: 'Validation Error', text: 'Please check the form for errors.' });
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        // Handle error untuk field array (components.0.item_id)
                        var formattedKey = key.replace(/\./g, '_');
                        $('#' + formattedKey + '_error').text(value[0]);
                    });
                } else {
                     Swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong! Please try again.' });
                }
            }
        });
    });

    // --- LOGIKA DELETE ---
    $('#recipes-table').on('click', '.delete-btn', function (e) {
        e.preventDefault();
        var deleteUrl = $(this).data('url');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
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
                    error: function(xhr) {
                         Swal.fire('Failed!', xhr.responseJSON.error || 'There was a problem deleting the recipe.', 'error');
                    }
                });
            }
        });
    });

    // --- HELPERS ---
    function formatRupiah(angka) { /* ... (kode sama seperti sebelumnya) ... */ }
    function unformatRupiah(rupiah) { /* ... (kode sama seperti sebelumnya) ... */ }
    $('#cost_per_kg').on('input', function(e) { $(this).val(formatRupiah($(this).val())); });
});
</script>
@endpush
