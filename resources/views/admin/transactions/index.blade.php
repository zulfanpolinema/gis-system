@extends('adminlte::page')
@section('title', 'Data Panen')
@section('content_header')
    <h1 class="m-0 text-dark">Data Panen</h1>
@endsection
@section('plugins.Datatables', true)

@php
    $heads = [['label' => 'No', 'width' => 2], 'Pemilik', 'Kategori', 'Jumlah Pembelian', 'Harga', 'No. HP / WA', 'Status', ['label' => 'Actions']];
    $config = [
        'serverSide' => true,
        'processing' => true,
        'ajax' => ['url' => route('transactions.index')],
        'order' => [[0, 'asc']],
        'columns' => [['data' => 'DT_RowIndex'], ['data' => 'pemilik'], ['data' => 'category'], ['data' => 'amount'], ['data' => 'total'], ['data' => 'phonenumber'], ['data' => 'status'], ['data' => 'actions', 'orderable' => false, 'searchable' => false, 'visible' => true]],
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
