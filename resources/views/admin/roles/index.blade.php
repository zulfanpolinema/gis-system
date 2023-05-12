@extends('adminlte::page')
@section('title', 'Role and Permissions')
@section('content_header')
    <h1 class="m-0 text-dark">Role & Permissions</h1>
@endsection
@section('plugins.Datatables', true)

@php
    $heads = [['label' => 'No', 'width' => 2], 'Role', 'Permissions', 'Dibuat Pada', ['label' => 'Actions', 'width' => 5]];
    $config = [
        'serverSide' => true,
        'processing' => true,
        'ajax' => ['url' => route('roles.index')],
        'order' => [[0, 'asc']],
        'columns' => [['data' => 'DT_RowIndex'], ['data' => 'name'], ['data' => 'permissions'], ['data' => 'created_at'], ['data' => 'actions']],
    ];
@endphp
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <div class="ml-auto">
                <x-adminlte-button label="Add Role" theme="primary" icon="fas fa-plus" data-toggle="modal" data-target="#addRoleModal" />
                <x-adminlte-button label="Add Permission" theme="primary" icon="fas fa-plus" data-toggle="modal" data-target="#addPermissionModal" />
            </div>
        </div>
        <div class="card-body">
            <x-adminlte-datatable id="table-roles" :heads="$heads" :config="$config" striped hoverable>
            </x-adminlte-datatable>
        </div>
    </div>
    <form id="addRole">
        <x-adminlte-modal id="addRoleModal" title="Add Role">
            @csrf
            <x-adminlte-input name="name" label="Role Name" placeholder="Masukkan nama role" disable-feedback />
            <label for="permissions[]" class="form-label">Permissions</label>
            @foreach ($permissions as $item)
                <div class="form-check">
                    <input class="form-check-input" name="permissions[]" value="{{ $item->id }}" type="checkbox" id="{{ $item->id }}">
                    <label class="form-check-label" for="{{ $item->id }}">
                        {{ $item->name }}
                    </label>
                </div>
            @endforeach
            <x-slot name="footerSlot">
                <x-adminlte-button theme="primary" label="Simpan" type="submit" id="submitButton" />
                <x-adminlte-button theme="default" label="Batalkan" data-dismiss="modal" id="dismissButton" />
            </x-slot>
        </x-adminlte-modal>
    </form>
    <form id="editRole">
        <x-adminlte-modal id="editRoleModal" title="Edit Role">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="editId">
            <x-adminlte-input name="name" label="Role Name" placeholder="Masukkan nama role" id="editName" disable-feedback />
            <label for="permissions[]" class="form-label">Permissions</label>
            @foreach ($permissions as $item)
                <div class="form-check">
                    <input class="form-check-input" name="permissions[]" value="{{ $item->id }}" type="checkbox" id="permissions{{ $item->id }}">
                    <label class="form-check-label" for="{{ $item->id }}">
                        {{ $item->name }}
                    </label>
                </div>
            @endforeach
            <x-slot name="footerSlot">
                <x-adminlte-button theme="primary" label="Simpan" type="submit" id="submitEditButton" />
                <x-adminlte-button theme="default" label="Batalkan" data-dismiss="modal" id="dismissButton" />
            </x-slot>
        </x-adminlte-modal>
    </form>
@endsection

@section('js')
    <script>
        $('#addRole').on('submit', function(e) {
            e.preventDefault();
            $('#submitButton').attr('disabled', true);
            $.ajax({
                url: "{{ route('roles.store') }}",
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
                    $('#addRoleModal').modal('toggle');
                    $('#table-roles').DataTable().ajax.reload();
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
                    var route = "{{ route('roles.destroy', ':id') }}";
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
                            $('#table-roles').DataTable().ajax.reload();
                        },
                        error: function(data) {
                            toastr.error(data.responseJSON.message, 'Error');
                        }
                    });
                }
            });
        });

        $(document).on("click", "#editButton", function(e) {
            $("[id^='permissions']").prop('checked', false);
            var id = $(this).data("id");
            var route = "{{ route('roles.edit', ':id') }}";
            route = route.replace(':id', id);
            $.ajax({
                url: route,
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    $('#editId').val(data.id);
                    $('#editName').val(data.name);
                    $.each(data.permissions, function(i, item) {
                        $('#permissions' + item.id).prop('checked', true);
                    });
                }
            });
        });

        $('#editRole').on('submit', function(e) {
            e.preventDefault();
            $('#submitEditButton').attr('disabled', true);
            var id = $('#editId').val();
            var route = "{{ route('roles.update', ':id') }}";
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
                    $('#editRoleModal').modal('toggle');
                    $('#table-roles').DataTable().ajax.reload();
                }
            });
            $('#submitEditButton').removeAttr('disabled');
            return false;
        });
    </script>
@endsection
