@extends('adminlte::page')
@section('title', 'Data Panen')
@section('content_header')
    <h1 class="m-0 text-dark">Data Panen</h1>
@endsection
@section('plugins.Datatables', true)

@php
    $heads = [['label' => 'No', 'width' => 2], 'Gambar', 'Pemilik', 'Kategori', 'Total Panen (Kg)', 'Harga/Kg', 'Alamat', 'Koordinat', 'No. HP / WA', 'Actions'];
    $config = [
        'serverSide' => true,
        'processing' => true,
        'ajax' => ['url' => route('harvests.index')],
        'order' => [[0, 'asc']],
        'columns' => [['data' => 'DT_RowIndex'], ['data' => 'gambar', 'orderable' => false, 'searchable' => false], ['data' => 'pemilik'], ['data' => 'category'], ['data' => 'total'], ['data' => 'price'], ['data' => 'address', 'orderable' => false], ['data' => 'coordinate', 'orderable' => false], ['data' => 'phonenumber'], ['data' => 'actions', 'orderable' => false, 'searchable' => false, 'visible' => true]],
    ];
@endphp
{{-- @hasrole('Pengepul')
    @php
        $config['columns'][8]['visible'] = false;
    @endphp
@endhasrole --}}
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <div class="ml-auto">
                @hasanyrole(['Admin', 'Petani'])
                    <a href="{{ route('harvests.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Data Panen</a>
                @endhasanyrole
            </div>
        </div>
        <div class="card-body">
            <x-adminlte-datatable id="table-harvests" :heads="$heads" :config="$config" striped hoverable>
            </x-adminlte-datatable>
        </div>
    </div>
    <x-adminlte-modal id="showLocationModal" title="Lokasi" size="lg">
        <div id="map" style="height: 500px;" class="my-3"></div>
        <a class="btn btn-xl btn-primary float-right" id="location" target="_blank">Arahkan melalui google maps</a>
    </x-adminlte-modal>
    @include('admin.harvests.orderModal')
@endsection

@section('js')
    <script>
        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 13,
                mapTypeId: 'hybrid',
                streetViewControl: false,
            });

            marker = new google.maps.Marker({
                map: map,
                draggable: false,
            });
        }

        $(document).on('click', '#showLocation', function() {
            var route = "{{ route('harvests.show', ':id') }}";
            $.ajax({
                url: route.replace(':id', $(this).data('id')),
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    map.setCenter(new google.maps.LatLng(parseFloat(data.latitude), parseFloat(data.longitude)));
                    marker.setPosition(new google.maps.LatLng(parseFloat(data.latitude), parseFloat(data.longitude)));
                    $('#location').attr('href', 'https://www.google.com/maps/dir/current+location/' + data.latitude + ',' + data.longitude);
                }
            })
        });
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDMlMc5Bgd_oMGxIK5b-tVYf71T5Qwm1y0&callback=initMap" defer type="text/javascript"></script>
    <script>
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
                    var route = "{{ route('harvests.destroy', ':id') }}";
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
                            $('#table-harvests').DataTable().ajax.reload();
                        },
                        error: function(data) {
                            toastr.error(data.responseJSON.message, 'Error');
                        }
                    });
                }
            });
        });
    </script>
@endsection
