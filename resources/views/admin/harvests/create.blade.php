@extends('adminlte::page')
@section('title', 'Data Panen')
@section('content_header')
    <h1 class="m-0 text-dark">Buat Data Panen</h1>
@endsection
@section('plugins.Datatables', true)

@section('content')
    <div class="card">
        <form id="addHarvestForm">
            @csrf
            <div class="card-header d-flex align-items-center">
                <div class="card-title">
                    Data Panen
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @if (Auth::user()->hasRole('Admin'))
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="user_id" class="form-label" required>Nama Pemilik</label>
                                <select name="user_id" id="user_id" class="form-control">
                                </select>
                            </div>
                        </div>
                    @endif
                    <div class="col-xl-6">
                        <div class="form-group">
                            <label for="category_id" class="form-label">Kategori</label>
                            <select name="category_id" id="category_id" class="form-control" required>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <x-adminlte-input name="total" label="Total Panen" placeholder="Jumlah total panen" required>
                            <x-slot name="appendSlot">
                                <div class="input-group-text">
                                    Kg
                                </div>
                            </x-slot>
                        </x-adminlte-input>
                    </div>
                    <div class="col-xl-6">
                        <x-adminlte-input name="price" label="Harga per Kg" placeholder="Harga per kg" required>
                            <x-slot name="prependSlot">
                                <div class="input-group-text">
                                    Rp
                                </div>
                            </x-slot>
                            <x-slot name="appendSlot">
                                <div class="input-group-text">
                                    /Kg
                                </div>
                            </x-slot>
                        </x-adminlte-input>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <x-adminlte-textarea label="Alamat" name="address" placeholder="Masukkan alamat" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <div class="form-group">
                            <label for="province_id" class="form-label">Provinsi</label>
                            <select name="province_id" id="province_id" class="form-control" required>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="form-group">
                            <label for="city_id" class="form-label">Kota</label>
                            <select name="city_id" id="city_id" class="form-control" required>
                                <option value="" selected disabled>Pilih provinsi terlebih dahulu!</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <div class="form-group">
                            <label for="subdistrict_id" class="form-label">Kecamatan</label>
                            <select name="subdistrict_id" id="subdistrict_id" class="form-control" required>
                                <option value="" selected disabled>Pilih kota terlebih dahulu!</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="form-group">
                            <label for="village_id" class="form-label">Kelurahan</label>
                            <select name="village_id" id="village_id" class="form-control" required>
                                <option value="" selected disabled>Pilih kecamatan terlebih dahulu!</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-end">
                    <x-adminlte-button label="Simpan" theme="primary" icon="fas fa-save" type="submit" />
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    @if (Auth::user()->hasRole('Admin'))
        <script>
            $('#user_id').select2({
                ajax: {
                    url: "{{ route('dropdown.farmers') }}",
                    data: function(params) {
                        return {
                            search: params.term,
                            type: 'public',
                        }
                    },
                },
                placeholder: "Pilih Pemilik",
                width: '100%',
                theme: "classic",
            });
        </script>
    @endif
    <script>
        $('#category_id').select2({
            ajax: {
                url: "{{ route('dropdown.categories') }}",
                data: function(params) {
                    return {
                        search: params.term,
                        type: 'public',
                    }
                },
            },
            placeholder: "Pilih Kategori",
            width: '100%',
            theme: "classic",
        });

        $('#province_id').select2({
            ajax: {
                url: "{{ route('dropdown.provinces') }}",
                data: function(params) {
                    return {
                        search: params.term,
                        type: 'public',
                    }
                },
            },
            placeholder: "Pilih Provinsi",
            width: '100%',
            theme: "classic",
        });

        $('#province_id').on('change', function() {
            var province_id = $(this).val();
            $('#city_id').select2({
                ajax: {
                    url: "{{ route('dropdown.cities') }}",
                    data: function(params) {
                        return {
                            search: params.term,
                            type: 'public',
                            province_id: province_id,
                        }
                    },
                },
                placeholder: "Pilih Kota",
                width: '100%',
                theme: "classic",
            });
        });

        $('#city_id').on('change', function() {
            var city_id = $(this).val();
            $('#subdistrict_id').select2({
                ajax: {
                    url: "{{ route('dropdown.subdistricts') }}",
                    data: function(params) {
                        return {
                            search: params.term,
                            type: 'public',
                            city_id: city_id,
                        }
                    },
                },
                placeholder: "Pilih Kecamatan",
                width: '100%',
                theme: "classic",
            });
        });

        $('#subdistrict_id').on('change', function() {
            var subdistrict_id = $(this).val();
            $('#village_id').select2({
                ajax: {
                    url: "{{ route('dropdown.villages') }}",
                    data: function(params) {
                        return {
                            search: params.term,
                            type: 'public',
                            subdistrict_id: subdistrict_id,
                        }
                    },
                },
                placeholder: "Pilih Kelurahan",
                width: '100%',
                theme: "classic",
            });
        });
    </script>
@endsection
