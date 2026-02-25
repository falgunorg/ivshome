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
                    <th>Bengali Name</th> <th>Qty</th>
                    <th>Unit</th>
                    <th>Category</th>
                    <th>Min. Stock</th> <th>Actions</th>
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
                        {data: 'bengali_name', name: 'bengali_name'}, // Added
                        {data: 'qty', name: 'qty'},
                        {data: 'unit', name: 'unit'},
                        {data: 'category', name: 'category'},
                        {data: 'min_stock', name: 'min_stock'}, // Added
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

                function editForm(id) {
                    save_method = 'edit';
                    $('input[name=_method]').val('PATCH');
                    $('#modal-form form')[0].reset();
                    $.ajax({
                        url: "{{ url('groceries') }}/" + id + "/edit",
                        type: "GET",
                        dataType: "JSON",
                        success: function (data) {
                            $('#modal-form').modal('show');
                            $('.modal-title').text('Edit Grocery Item');

                            // Map data to Form IDs
                            $('#id').val(data.id);
                            $('#name').val(data.name);
                            $('#bengali_name').val(data.bengali_name); // Added mapping
                            $('#qty').val(data.qty);
                            $('#unit').val(data.unit);
                            $('#category').val(data.category);
                            $('#min_stock').val(data.min_stock); // Added mapping
                        },
                        error: function () {
                            alert("Could not fetch data");
                        }
                    });
                }

                function deleteData(id) {
                    var csrf_token = $('meta[name="csrf-token"]').attr('content');
                    if (confirm("Are you sure you want to delete this?")) {
                        $.ajax({
                            url: "{{ url('groceries') }}/" + id,
                            type: "POST",
                            data: {'_method': 'DELETE', '_token': csrf_token},
                            success: function (data) {
                                table.ajax.reload();
                                alert("Data deleted successfully");
                            },
                            error: function () {
                                alert("Oops! Something went wrong.");
                            }
                        });
                    }
                }

                $(function () {
                    $('#modal-form form').validator().on('submit', function (e) {
                        if (!e.isDefaultPrevented()) {
                            var id = $('#id').val();
                            if (save_method == 'add')
                                url = "{{ url('groceries') }}";
                            else
                                url = "{{ url('groceries') . '/' }}" + id;

                            $.ajax({
                                url: url,
                                type: "POST",
                                data: new FormData($("#modal-form form")[0]),
                                contentType: false,
                                processData: false,
                                success: function (data) {
                                    $('#modal-form').modal('hide');
                                    table.ajax.reload();
                                    alert("Data saved successfully!");
                                },
                                error: function (data) {
                                    alert('Oops! Error saving data. Check your fields.');
                                }
                            });
                            return false;
                        }
                    });
                });
</script>
@endsection