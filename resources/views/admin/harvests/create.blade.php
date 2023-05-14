@extends('adminlte::page')
@section('title', 'Data Panen')
@section('content_header')
    <h1 class="m-0 text-dark">Buat Data Panen</h1>
@endsection
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
                    @hasrole('Admin')
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="user_id" class="form-label" required>Nama Pemilik</label>
                                <select name="user_id" id="user_id" class="form-control">
                                </select>
                            </div>
                        </div>
                    @endhasrole
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
                <div class="row">
                    <div class="col">
                        <div id="map" style="height: 500px;"></div>
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-end">
                    <x-adminlte-button label="Simpan" theme="primary" icon="fas fa-save" type="submit" id="btnSubmit"/>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        var map, marker;

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: {
                    lat: -7.968376,
                    lng: 112.632354
                },
                mapTypeId: 'hybrid',
                streetViewControl: false,
            });

            marker = new google.maps.Marker({
                position: new google.maps.LatLng(-7.968376, 112.632354),
                map: map,
                draggable: true,
            });

            google.maps.event.addListener(marker, 'dragend', function() {
                $('#latitude').val(marker.getPosition().lat());
                $('#longitude').val(marker.getPosition().lng());
            });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAWDH7OEaIYZuJOD5dsXGerjcPf7IHCnGg&callback=initMap" defer type="text/javascript"></script>
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

        $('#village_id').on('change', async function() {
            var village_id = $(this).val();
            $.ajax({
                url: "{{ route('harvests.create') }}",
                type: 'GET',
                dataType: 'JSON',
                data: $.param({
                    village_id: village_id,
                }),
                success: function(data) {
                    if (data.meta.lat != "NULL" || data.meta.long != "NULL") {
                        map.setCenter(new google.maps.LatLng(data.meta.lat, data.meta.long));
                        marker.setPosition(new google.maps.LatLng(data.meta.lat, data.meta.long));
                        $('#latitude').val(data.meta.lat);
                        $('#longitude').val(data.meta.long);
                    } else {
                        toastr.warning("Data posisi tidak ditemukan, silahkan pilih secara manual!", 'Warning');
                    }

                },
                error: function(data) {
                    toastr.error(data.responseJSON.message, 'Error');
                },
            });
        });

        $('#addHarvestForm').on('submit', function (e) {
            e.preventDefault();
            $('#btnSubmit').attr('disabled', true);
            $.ajax({
                url: "{{ route('harvests.store') }}",
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
                    window.location.assign(data.message);
                }
            });
            $('#btnSubmit').removeAttr('disabled');
        });
    </script>
@endsection
