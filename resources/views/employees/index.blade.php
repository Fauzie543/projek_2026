@extends('layouts.app')

@section('header', 'Employees Management')

@section('content')
<div class="bg-white p-6 rounded-md shadow-sm">
    <!-- Tombol untuk membuka modal tambah employee -->
    <button id="addEmployeeBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
        Add Employee
    </button>

    <table id="employees-table" class="table table-bordered table-striped w-full">
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Position</th>
                <th>Hire Date</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Include file modal -->
@include('employees.modal')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function () {
    var table = $('#employees-table').DataTable({
        // ... (konfigurasi datatable tetap sama) ...
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route('employees.data') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'user.name' },
            { data: 'email', name: 'user.email' },
            { data: 'phone', name: 'phone' },
            { data: 'role', name: 'role.name' },
            { data: 'position', name: 'position' },
            { data: 'hire_date', name: 'hire_date' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        columnDefs: [
            {
                targets: '_all', // Terapkan ke semua kolom
                className: 'py-3 align-middle' // Tambahkan class padding vertikal dan alignment
            }
        ],
        dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>'
    });

    // --- LOGIKA MODAL ---

    // Tampilkan modal untuk TAMBAH data
    $('#addEmployeeBtn').on('click', function () {
        $('#employeeForm')[0].reset();
        $('.error-message').text('');
        $('#modal_title').text('Add New Employee');
        $('#submitBtn').text('Save Employee');
        $('#form_method').val('POST');
        $('#employeeForm').attr('action', '{{ route('employees.store') }}');
        // Pastikan password required saat tambah data
        $('#password, #password_confirmation').prop('required', true);
        $('#employeeModal').removeClass('hidden');
    });

    // Tampilkan modal untuk EDIT data
    $('#employees-table').on('click', '.edit-btn', function () {
        var employeeId = $(this).data('id');
        var url = `/employees/${employeeId}/edit`;

        // Reset form
        $('#employeeForm')[0].reset();
        $('.error-message').text('');

        $.ajax({
            url: url,
            method: 'GET',
            success: function(data) {
                // Ubah judul & tombol modal
                $('#modal_title').text('Edit Employee');
                $('#submitBtn').text('Update Employee');
                
                // Siapkan form untuk method PUT dan set action URL
                $('#form_method').val('PUT');
                $('#employeeForm').attr('action', `/employees/${employeeId}`);

                // Isi data ke form
                $('#employee_id').val(data.id);
                $('#name').val(data.user.name);
                $('#email').val(data.user.email);
                $('#nik').val(data.nik);
                $('#phone').val(data.phone);
                $('#address').val(data.address);
                $('#role').val(data.role_id);
                $('#position').val(data.position);
                $('#hire_date').val(data.hire_date);
                
                // Format gaji saat mengisi form
                var salaryInput = $('#salary_monthly');
                salaryInput.val(data.salary_monthly);
                salaryInput.trigger('input'); // Memicu format Rupiah

                // Password tidak required saat edit
                $('#password, #password_confirmation').prop('required', false);

                // Tampilkan modal
                $('#employeeModal').removeClass('hidden');
            },
            error: function() {
                alert('Could not fetch employee data.');
            }
        });
    });


    // Sembunyikan modal
    $('#cancelBtn, .close-modal').on('click', function () {
        $('#employeeModal').addClass('hidden');
    });

    // Kirim data form (untuk ADD dan UPDATE)
    $('#employeeForm').on('submit', function (e) {
        e.preventDefault();
        $('.error-message').text('');

        var salaryInput = $('#salary_monthly');
        var numericSalary = unformatRupiah(salaryInput.val());
        salaryInput.val(numericSalary);

        var url = $(this).attr('action');
        var formData = $(this).serialize();
        
        salaryInput.val(formatRupiah(numericSalary.toString(), 'Rp '));

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            success: function (response) {
                $('#employeeModal').addClass('hidden');
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: response.success,
                    showConfirmButton: false,
                    timer: 3000
                });
                table.ajax.reload();
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please check the form for errors.',
                    });
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        $('#' + key + '_error').text(value[0]);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong! Please try again.',
                    });
                }
            }
        });
    });

    $('#employees-table').on('click', '.delete-btn', function (e) {
        e.preventDefault();
        var deleteUrl = $(this).data('url');

        Swal.fire({
            title: 'Are you sure?',
            text: "This will delete the employee and their user account. You won't be able to revert this!",
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
                    // --- PERUBAHAN PENTING DI SINI ---
                    // Menambahkan header CSRF secara eksplisit ke request ini
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: response.success, showConfirmButton: false, timer: 3000 });
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                         Swal.fire('Failed!', 'There was a problem deleting the employee. Status: ' + xhr.status, 'error');
                    }
                });
            }
        });
    });

    // --- LOGIKA TOGGLE PASSWORD ---
    $('.toggle-password').on('click', function() {
        var input = $(this).closest('.relative').find('input');
        var iconShow = $(this).find('.icon-show');
        var iconHide = $(this).find('.icon-hide');

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            iconShow.addClass('hidden');
            iconHide.removeClass('hidden');
        } else {
            input.attr('type', 'password');
            iconShow.removeClass('hidden');
            iconHide.addClass('hidden');
        }
    });

    // --- LOGIKA FORMAT RUPIAH ---
    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
    }
    
    function unformatRupiah(rupiah) {
        // Mengembalikan 0 jika input kosong atau tidak valid
        return parseInt(String(rupiah).replace(/[^0-9]/g, ''), 10) || 0;
    }

    $('#salary_monthly').on('input', function(e) {
        $(this).val(formatRupiah($(this).val(), 'Rp '));
    });
});
</script>
@endpush

