@extends('adminlte::page')
@section('title', 'Users')
@section('content_header')
    <h1 class="m-0 text-dark">Users</h1>
@endsection
@section('plugins.Datatables', true)

@php
    $heads = [['label' => 'No', 'width' => 2], 'Nama', 'Role', 'Tanggal Daftar', ['label' => 'Actions', 'width' => 5]];
    $config = [
        'serverSide' => true,
        'processing' => true,
        'ajax' => ['url' => route('users.index')],
        'order' => [[0, 'asc']],
        'columns' => [['data' => 'DT_RowIndex'], ['data' => 'name'], ['data' => 'role'], ['data' => 'created_at'], ['data' => 'actions']],
    ];
@endphp
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <div class="ml-auto">
                <x-adminlte-button label="User" theme="primary" icon="fas fa-plus" data-toggle="modal" data-target="#addUserModal" />
            </div>
        </div>
        <div class="card-body">
            <x-adminlte-datatable id="table-users" :heads="$heads" :config="$config" striped hoverable>
            </x-adminlte-datatable>
        </div>
    </div>
    <form id="addUser">
        <x-adminlte-modal id="addUserModal" title="Add User">
            @csrf
            <x-adminlte-input name="name" label="Nama Lengkap" placeholder="Masukkan Nama Lengkap" disable-feedback />
            <x-adminlte-input name="email" label="E-Mail" placeholder="nama@domain.com" disable-feedback />
            <x-adminlte-input type="password" name="password" label="Password" placeholder="Masukkan password" disable-feedback />
            <label for="role" class="form-label">Role</label>
            <select name="role[]" id="role" class="form-control">
            </select>
            <x-slot name="footerSlot">
                <x-adminlte-button theme="primary" label="Simpan" type="submit" id="submitButton" />
                <x-adminlte-button theme="default" label="Batalkan" data-dismiss="modal" id="dismissButton" />
            </x-slot>
        </x-adminlte-modal>
    </form>
    <form id="editUser">
        <x-adminlte-modal id="editUserModal" title="Edit User">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="editId">
            <x-adminlte-input id="editName" name="name" label="Nama Lengkap" placeholder="Masukkan Nama Lengkap" disable-feedback />
            <x-adminlte-input id="editEmail" name="email" label="E-Mail" placeholder="nama@domain.com" disable-feedback />
            <x-adminlte-input id="editPassword" type="password" name="password" label="Password" placeholder="Masukkan password" disable-feedback />
            <label for="editRole" class="form-label">Role</label>
            <select id="editRole" name="role[]" class="form-control">
            </select>
            <x-slot name="footerSlot">
                <x-adminlte-button theme="primary" label="Simpan" type="submit" id="submitEditButton" />
                <x-adminlte-button theme="default" label="Batalkan" data-dismiss="modal" id="dismissEditButton" />
            </x-slot>
        </x-adminlte-modal>
    </form>
@endsection

@section('js')
    <script>
        $('#role').select2({
            ajax: {
                url: "{{ route('dropdown.roles') }}",
                data: function(params) {
                    return {
                        search: params.term,
                        type: 'public',
                    }
                },
            },
            placeholder: "Pilih Role",
            width: '100%',
            theme: "classic",
            dependantDropdown: $('#addUser'),
        });

        $('#editRole').select2({
            ajax: {
                url: "{{ route('dropdown.roles') }}",
                data: function(params) {
                    return {
                        search: params.term,
                        type: 'public',
                    }
                },
            },
            placeholder: "Pilih Role",
            width: '100%',
            theme: "classic",
            dependantDropdown: $('#editUser'),
        });

        $('#addUser').on('submit', function(e) {
            e.preventDefault();
            $('#submitButton').attr('disabled', true);
            $.ajax({
                url: "{{ route('users.store') }}",
                type: "POST",
                dataType: "JSON",
                processData: false,
                contentType: false,
                cache: false,
                data: new FormData(this),
                error: function(data) {
                    toastr.error(data.responseJSON.message, 'Error');
                },
                success: function(data) {
                    toastr.success(data.message, 'Sukses');
                    $('#addUserModal').modal('toggle');
                    $('#table-users').DataTable().ajax.reload();
                }
            });
            $('#submitButton').removeAttr('disabled');
            return false;
        });

        $(document).on("click", "#deleteButton", function(e) {
            e.preventDefault();
            Swal.fire({
                customClass: {
                    confirmButton: 'bg-danger',
                },
                title: 'Apakah anda yakin?',
                text: "Apakah anda yakin ingin menghapus data ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    e.preventDefault();
                    var id = $(this).data("id");
                    var route = "{{ route('users.destroy', ':id') }}";
                    route = route.replace(':id', id);
                    $.ajax({
                        url: route,
                        type: 'DELETE',
                        data: {
                            _token: $("meta[name='csrf-token']").attr("content"),
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK',
                                timer: 1000,
                                timerProgressBar: true,
                            })
                            $('#table-users').DataTable().ajax.reload();
                        },
                        error: function(data) {
                            toastr.error(data.responseJSON.message, 'Error');
                        }
                    });
                }
            });
        });

        $(document).on("click", "#editButton", function(e) {
            var id = $(this).data("id");
            var route = "{{ route('users.edit', ':id') }}";
            route = route.replace(':id', id);
            $.ajax({
                url: route,
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    $('#editId').val(data.id);
                    $('#editName').val(data.name);
                    $('#editEmail').val(data.email);
                    $('#editRole').val(data.roles[0].id).trigger('change');
                }
            });
        });

        $('#editUser').on('submit', function(e) {
            e.preventDefault();
            $('#submitEditButton').attr('disabled', true);
            var id = $('#editId').val();
            var route = "{{ route('users.update', ':id') }}";
            route = route.replace(':id', id);
            $.ajax({
                url: route,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                dataType: "JSON",
                processData: false,
                contentType: false,
                cache: false,
                data: new FormData(this),
                error: function(data) {
                    toastr.error(data.responseJSON.message, 'Error');
                },
                success: function(data) {
                    toastr.success(data.message, 'Sukses');
                    $('#editUserModal').modal('toggle');
                    $('#table-users').DataTable().ajax.reload();
                }
            });
            $('#submitEditButton').removeAttr('disabled');
            return false;
        });
    </script>
@endsection
