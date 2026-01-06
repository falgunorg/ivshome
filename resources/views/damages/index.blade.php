@extends('layouts.master')

@section('top')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<!-- Datepicker -->
<link rel="stylesheet" href="{{ asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('content')

{{-- Damage List --}}
<div class="box box-success">

    <div class="box-header">
        <h3 class="box-title">Damage List</h3>
    </div>

    <div class="box-header">
        <a onclick="addForm()" class="btn btn-success">
            <i class="fa fa-plus"></i> Add Damage
        </a>

        <a href="{{ route('exportPDF.damageAll') }}" class="btn btn-danger">
            <i class="fa fa-file-pdf-o"></i> Export PDF
        </a>

        <a href="{{ route('exportExcel.damageAll') }}" class="btn btn-primary">
            <i class="fa fa-file-excel-o"></i> Export Excel
        </a>
    </div>

    <div class="box-body">
        <table id="damage-table" class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Date</th>
                    <th>By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

</div>

{{-- Damage Invoice --}}
<div class="box box-success col-md-6">

    <div class="box-header">
        <h3 class="box-title">Export Damage Invoice</h3>
    </div>

    <div class="box-body">
        <table id="invoice" class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Item</th>
                    <th>Remarks</th>
                    <th>Qty</th>
                    <th>Date</th>
                    <th>By</th>
                    <th>Action</th>
                </tr>
            </thead>

            @foreach($invoice_data as $i)
            <tbody>
                <tr>
                    <td>{{ $i->id }}</td>
                    <td>{{ $i->item->name }}</td>
                    <td>{{ $i->remarks }}</td>
                    <td>{{ $i->qty }}</td>
                    <td>{{ $i->date }}</td>
                    <td>{{ $i->user->name }}</td>
                    <td>
                        <a href="{{ route('exportPDF.damage', ['id' => $i->id]) }}"
                           class="btn btn-sm btn-danger">
                            Export Invoice
                        </a>
                    </td>
                </tr>
            </tbody>
            @endforeach

        </table>
    </div>

</div>

@include('damages.form')

@endsection

@section('bot')

<!-- DataTables -->
<script src="{{ asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

<!-- Datepicker -->
<script src="{{ asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

<!-- Validator -->
<script src="{{ asset('assets/validator/validator.min.js') }}"></script>

<script>
            $(function () {

                // Invoice table (same as item_sale)
                $('#invoice').DataTable({
                    paging: true,
                    lengthChange: false,
                    searching: false,
                    ordering: true,
                    info: true,
                    autoWidth: false
                });

                // Datepicker
                $('#date').datepicker({
                    autoclose: true,
                    format: 'yyyy-mm-dd'
                });

            });
</script>

<script type="text/javascript">
    var table = $('#damage-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('api.damages') }}",
        columns: [
            {data: 'id', name: 'id'},
            {data: 'item_name', name: 'item_name'},
            {data: 'qty', name: 'qty'},
            {data: 'date', name: 'date'},
            {data: 'user_name', name: 'user_name'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });

    function addForm() {
        save_method = "add";
        $('input[name=_method]').val('POST');
        $('#modal-form').modal('show');
        $('#modal-form form')[0].reset();
        $('.modal-title').text('Add Damage');
    }

    function editForm(id) {
        save_method = 'edit';
        $('input[name=_method]').val('PATCH');
        $('#modal-form form')[0].reset();

        $.ajax({
            url: "{{ url('damages') }}/" + id + "/edit",
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                $('#modal-form').modal('show');
                $('.modal-title').text('Edit Damage');

                $('#id').val(data.id);
                $('#item_id').val(data.item_id);
                $('#qty').val(data.qty);
                $('#date').val(data.date);
                $('textarea[name=remarks]').val(data.remarks);
            },
            error: function () {
                alert("No data found");
            }
        });
    }

    function deleteData(id) {
        var csrf_token = $('meta[name="csrf-token"]').attr('content');

        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then(function () {
            $.ajax({
                url: "{{ url('damages') }}/" + id,
                type: "POST",
                data: {
                    '_method': 'DELETE',
                    '_token': csrf_token
                },
                success: function (data) {
                    table.ajax.reload();
                    swal({
                        title: 'Success!',
                        text: data.message,
                        type: 'success',
                        timer: 1500
                    });
                }
            });
        });
    }

    $(function () {
        $('#modal-form form').validator().on('submit', function (e) {
            if (!e.isDefaultPrevented()) {
                var id = $('#id').val();
                var url = save_method === 'add'
                        ? "{{ url('damages') }}"
                        : "{{ url('damages') }}/" + id;

                $.ajax({
                    url: url,
                    type: "POST",
                    data: new FormData($("#modal-form form")[0]),
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        $('#modal-form').modal('hide');
                        table.ajax.reload();
                        swal({
                            title: 'Success!',
                            text: data.message,
                            type: 'success',
                            timer: 1500
                        });
                    }
                });
                return false;
            }
        });
    });
</script>

@endsection
