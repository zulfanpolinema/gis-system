@extends('adminlte::page')
@section('title', 'Data Panen')
@section('content_header')
    <h1 class="m-0 text-dark">{{ Route::is('harvests.create') ? 'Buat Data Panen' : 'Edit data panen' }}</h1>
@endsection
@section('content')
    <div class="card">
        <form id="{{ Route::is('harvests.create') ? 'addHarvestForm' : 'editHarvestForm' }}">
            @csrf
            @if (Route::is('harvests.edit'))
                @method('PUT')
            @endif
            <div class="card-header d-flex align-items-center">
                <div class="card-title">
                    Data Panen
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @hasrole('Admin')
                        <div class="col-xl-6">
                            <x-adminlte-select2 name="user_id" label="User" data-placeholder="Pilih user">
                                @if (Route::is('harvests.edit'))
                                    <option value="{{ $harvest->user_id }}" selected>{{ $harvest->user->name }}</option>
                                @endif
                            </x-adminlte-select2>
                        </div>
                    @endhasrole
                    <div class="col-xl-6">
                        <x-adminlte-select2 name="category_id" label="Kategori" data-placeholder="Pilih kategori">
                            @if (Route::is('harvests.edit'))
                                <option value="{{ $harvest->category_id }}" selected>{{ $harvest->category->name }}</option>
                            @endif
                        </x-adminlte-select2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <x-adminlte-input name="total" label="Total Panen" placeholder="Jumlah total panen" value="{{ $harvest->total ?? '' }}" required>
                            <x-slot name="appendSlot">
                                <div class="input-group-text">
                                    Kg
                                </div>
                            </x-slot>
                        </x-adminlte-input>
                    </div>
                    <div class="col-xl-6">
                        <x-adminlte-select2 name="harvest_month" label="Bulan Panen" data-placeholder="Pilih bulan panen" required>
                            <option value=""></option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ isset($harvest) && $harvest->harvest_month == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $i, 1)->monthName }}</option>
                            @endfor
                        </x-adminlte-select2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-4">
                        <x-adminlte-input name="price" label="Harga per Kg" placeholder="Harga per kg" value="{{ $harvest->price ?? '' }}" required>
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
                    <div class="col-xl-4">
                        <x-adminlte-input name="retail_price" label="Harga grosir per kg" placeholder="Harga grosir per kg" value="{{ $harvest->retail_price ?? '' }}" required>
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
                    <div class="col-xl-4">
                        <x-adminlte-input name="retail_minimum" label="Jumlah minimum grosir" placeholder="Jumlah minimum grosir" value="{{ $harvest->retail_minimum ?? '' }}" required>
                            <x-slot name="appendSlot">
                                <div class="input-group-text">
                                    Kg
                                </div>
                            </x-slot>
                        </x-adminlte-input>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <x-adminlte-input name="phonenumber" label="No. HP / WA" placeholder="No. HP / WA (08xxx)" value="{{ $harvest->phonenumber ?? '' }}" required />
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <x-adminlte-textarea label="Alamat" name="address" placeholder="Masukkan alamat" required>
                            {{ $harvest->address ?? '' }}
                        </x-adminlte-textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <x-adminlte-select2 name="province_id" label="Provinsi" data-placeholder="Pilih provinsi" required>
                            @if (Route::is('harvests.edit'))
                                <option value="{{ $harvest->village->district->city->province->id }}" selected>{{ ucwords(strtolower($harvest->village->province_name)) }}</option>
                            @endif
                        </x-adminlte-select2>
                    </div>
                    <div class="col-xl-6">
                        <x-adminlte-select2 name="city_id" label="Kota" data-placeholder="Pilih provinsi terlebih dahulu!" required>
                            @if (Route::is('harvests.edit'))
                                <option value="{{ $harvest->village->district->city->id }}" selected>{{ ucwords(strtolower($harvest->village->city_name)) }}</option>
                            @endif
                        </x-adminlte-select2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <x-adminlte-select2 name="subdistrict_id" label="Kecamatan" data-placeholder="Pilih kota terlebih dahulu!">
                            @if (Route::is('harvests.edit'))
                                <option value="{{ $harvest->village->district->id }}" selected>{{ ucwords(strtolower($harvest->village->district_name)) }}</option>
                            @endif
                        </x-adminlte-select2>
                    </div>
                    <div class="col-xl-6">
                        <x-adminlte-select2 name="village_id" label="Kelurahan" data-placeholder="Pilih kecamatan terlebih dahulu!">
                            @if (Route::is('harvests.edit'))
                                <option value="{{ $harvest->village->id }}" selected>{{ ucwords(strtolower($harvest->village->name)) }}</option>
                            @endif
                        </x-adminlte-select2>
                    </div>
                </div>
                <div class="row mt-3" id="getLocationWrapper" style="display: none">
                    <div class="col d-flex justify-content-center">
                        <x-adminlte-button id="getLocation" label="Cari lokasi saya" theme="primary" icon="fa fa-location-arrow" />
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div id="map" style="height: 500px;" class="my-3"></div>
                        <input type="hidden" name="latitude" id="latitude" value="{{ $harvest->latitude ?? '' }}">
                        <input type="hidden" name="longitude" id="longitude" value="{{ $harvest->longitude ?? '' }}">
                    </div>
                </div>
                @if (Route::is('harvests.create'))
                    <div class="row">
                        <label for="gambar" class="form-label">Gambar</label>
                    </div>
                    <div class="form-group" id="uploadGambar">
                        <div id="actions" class="row">
                            <div class="col-xl-6">
                                <div class="btn-group w-100">
                                    <span class="btn btn-success col fileinput-button">
                                        <i class="fas fa-plus"></i>
                                        <span>Add files</span>
                                    </span>
                                    <button type="button" class="btn btn-primary col start">
                                        <i class="fas fa-upload"></i>
                                        <span>Start upload</span>
                                    </button>
                                    <button type="reset" class="btn btn-warning col cancel">
                                        <i class="fas fa-times-circle"></i>
                                        <span>Cancel upload</span>
                                    </button>
                                </div>
                            </div>
                            <div class="col-xl-6 d-flex align-items-center">
                                <div class="fileupload-process w-100">
                                    <div id="total-progress" class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                        <div class="progress-bar progress-bar-success" style="width: 0%" data-dz-uploadprogress></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table table-striped files" id="previews">
                                <div id="template" class="row mt-2">
                                    <div class="col-auto">
                                        <span class="preview"><img src="data:," alt="" data-dz-thumbnail /></span>
                                    </div>
                                    <div class="col d-flex align-items-center">
                                        <p class="mb-0">
                                            <span class="lead" data-dz-name></span>
                                            (<span data-dz-size></span>)
                                        </p>
                                        <strong class="error text-danger" data-dz-errormessage></strong>
                                    </div>
                                    <div class="col-4 d-flex align-items-center">
                                        <div class="progress progress-striped active w-100" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                            <div class="progress-bar progress-bar-success" style="width: 0%" data-dz-uploadprogress></div>
                                        </div>
                                    </div>
                                    <div class="col-auto d-flex align-items-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary start">
                                                <i class="fas fa-upload"></i>
                                                <span>Start</span>
                                            </button>
                                            <button type="button" data-dz-remove class="btn btn-warning cancel">
                                                <i class="fas fa-times-circle"></i>
                                                <span>Cancel</span>
                                            </button>
                                            <button type="button" data-dz-remove class="btn btn-danger delete">
                                                <i class="fas fa-trash"></i>
                                                <span>Delete</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-end">
                    <x-adminlte-button label="Simpan" theme="primary" icon="fas fa-save" type="submit" id="btnSubmit" />
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        var map, marker;
        var lat = "{{ $harvest->latitude ?? -7.968376 }}";
        var long = "{{ $harvest->longitude ?? '112.632354' }}";

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: {
                    lat: parseFloat(lat),
                    lng: parseFloat(long),
                },
                mapTypeId: 'hybrid',
                streetViewControl: false,
            });

            marker = new google.maps.Marker({
                position: new google.maps.LatLng(parseFloat(lat), parseFloat(long)),
                map: map,
                draggable: true,
            });

            google.maps.event.addListener(marker, 'dragend', function() {
                $('#latitude').val(marker.getPosition().lat());
                $('#longitude').val(marker.getPosition().lng());
            });
        }

        $(function() {
            if (window.location.protocol === "https:") {
                $('#getLocationWrapper').show();
            } else {
                $('#getLocationWrapper').hide();
            }
        });

        function getCurrentPosition() {
            return new Promise(function(resolve, reject) {
                navigator.geolocation.getCurrentPosition(resolve, reject, {
                    enableHighAccuracy: true
                });
            });
        }

        $('#getLocation').on('click', function() {
            getCurrentPosition().then(function(position) {
                    var pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    map.setZoom(17);
                    map.setCenter(pos);
                    marker.setAnimation(google.maps.Animation.DROP);
                    marker.setPosition(pos);
                })
                .catch(function(error) {
                    console.error(error);
                });
        });
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDMlMc5Bgd_oMGxIK5b-tVYf71T5Qwm1y0&callback=initMap" defer type="text/javascript"></script>
    @hasrole('Admin')
        <script>
            $(function() {
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
                    width: '100%',
                    theme: 'bootstrap4',
                });
            });
        </script>
    @endhasrole
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    @if (Route::is('harvests.create'))
        <script>
            Dropzone.autoDiscover = false;
            var previewNode = document.querySelector("#template");
            previewNode.id = "";
            var previewTemplate = previewNode.parentNode.innerHTML;
            previewNode.parentNode.removeChild(previewNode);
            var uploadedDocumentMap = {};

            var myDropzone = new Dropzone('#uploadGambar', {
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                url: "{{ route('images.store') }}",
                thumbnailWidth: 80,
                thumbnailHeight: 80,
                parallelUploads: 20,
                previewTemplate: previewTemplate,
                autoQueue: false,
                previewsContainer: "#previews",
                clickable: ".fileinput-button",
                success: function(file, response) {
                    $('#addHarvestForm').append('<input type="hidden" name="gambar[]" value="' + response.name + '">')
                    uploadedDocumentMap[file.name] = response.name
                },
                removedfile: function(file) {
                    file.previewElement.remove()
                    var name = ''
                    if (typeof file.file_name !== 'undefined') {
                        name = file.file_name
                    } else {
                        name = uploadedDocumentMap[file.name]
                    }
                    $('#addHarvestForm').find('input[name="gambar[]"][value="' + name + '"]').remove()
                },
            });

            myDropzone.on("addedfile", function(file) {
                file.previewElement.querySelector(".start").onclick = function() {
                    myDropzone.enqueueFile(file);
                };
            });

            myDropzone.on("totaluploadprogress", function(progress) {
                document.querySelector("#total-progress .progress-bar").style.width =
                    progress + "%";
            });

            myDropzone.on("sending", function(file) {
                document.querySelector("#total-progress").style.opacity = "1";
                file.previewElement
                    .querySelector(".start")
                    .setAttribute("disabled", "disabled");
            });

            myDropzone.on("queuecomplete", function(progress) {
                document.querySelector("#total-progress").style.opacity = "0";
            });

            document.querySelector("#actions .start").onclick = function() {
                myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));
            };
            document.querySelector("#actions .cancel").onclick = function() {
                myDropzone.removeAllFiles(true);
            };
        </script>
    @endif

    <script>
        var im = new Inputmask({
            alias: 'numeric',
            allowMinus: false,
        });

        $(function() {
            im.mask('#price,#total');
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
                theme: 'bootstrap4',
                delay: 250,
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
                theme: 'bootstrap4',
                delay: 250,
            });
        })


        $('#province_id').on('change', function() {
            var province_id = $(this).val();
            $('#city_id').data('placeholder', 'Pilih Kota');
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
                width: '100%',
                theme: 'bootstrap4',
                delay: 250,
            });
        });

        $('#city_id').on('change', function() {
            var city_id = $(this).val();
            $('#subdistrict_id').data('placeholder', 'Pilih Kecamatan');
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
                width: '100%',
                theme: 'bootstrap4',
                delay: 250,
            });
        });

        $('#subdistrict_id').on('change', function() {
            $('#village_id').data('placeholder', 'Pilih Kelurahan');
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
                width: '100%',
                theme: 'bootstrap4',
                delay: 250,
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

        $('#addHarvestForm').on('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                customClass: {
                    confirmButton: 'bg-primary',
                },
                title: 'Periksa kembali data anda',
                text: "Apakah anda yakin ingin menyimpan data ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya'
            }).then((result) => {
                if (result.isConfirmed) {
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
                }
            });
        });
    </script>
    @if (Route::is('harvests.edit'))
        <script>
            $('#editHarvestForm').on('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    customClass: {
                        confirmButton: 'bg-primary',
                    },
                    title: 'Periksa kembali data anda',
                    text: "Apakah anda yakin ingin mengubah data ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // var id = "{{ $harvest->id }}";
                        $('#btnSubmit').attr('disabled', true);
                        $.ajax({
                            url: "{{ route('harvests.update', $harvest->id) }}",
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
                    }
                });
            });
        </script>
    @endif
@endsection
