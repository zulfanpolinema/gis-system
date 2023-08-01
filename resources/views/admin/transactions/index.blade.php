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
@hasrole('Admin')
    @php
        $config['columns'][7]['visible'] = false;
    @endphp
@endhasrole
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center">
        </div>
        <div class="card-body">
            <x-adminlte-datatable id="transactionsTable" :heads="$heads" :config="$config" striped hoverable>
            </x-adminlte-datatable>
        </div>
    </div>
    @include('admin.transactions.updateModal')
@endsection
