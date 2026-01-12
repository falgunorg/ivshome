@extends('layouts.master')

@section('top')
<link rel="stylesheet" href="{{ asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('content')
<div class="box box-success">
    <div class="box-header">
        <h3 class="box-title">Grocery Inventory</h3>
        <span class="pull-right">
            <a href="{{ route('groceries.ai') }}" class="btn btn-info" style="margin-top: -8px;">
                <i class="fa fa-magic"></i> Suggest 5 menus for today
            </a>
            <a onclick="addForm()" class="btn btn-success" style="margin-top: -8px;"><i class="fa fa-plus"></i> Add Grocery</a>
        </span>
    </div>

    <div class="box-body table-responsive">
        <table id="grocery-table" class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

@include('groceries.form')
@endsection

@section('bot')
<script src="{{ asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/validator/validator.min.js') }}"></script>

<script type="text/javascript">
                var table = $('#grocery-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('api.groceries') }}",
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'show_photo', name: 'show_photo', orderable: false, searchable: false},
                        {data: 'name', name: 'name'},
                        {data: 'qty', name: 'qty'},
                        {data: 'unit', name: 'unit'},
                        {data: 'category', name: 'category'},
                        {data: 'action', name: 'action', orderable: false, searchable: false}
                    ]
                });

                function addForm() {
                    save_method = "add";
                    $('input[name=_method]').val('POST');
                    $('#modal-form').modal('show');
                    $('#modal-form form')[0].reset();
                    $('.modal-title').text('Add Grocery Item');
                }

                // ... editForm and deleteData functions follow the same logic as your Items code ...
</script>
@endsection