@extends('layouts.master')

@section('top')
<link rel="stylesheet" href="{{ asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
<script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

<style>
    /* Styling for the Trix Editor to look consistent with AdminLTE */
    trix-editor {
        min-height: 250px !important;
        max-height: 400px;
        overflow-y: auto;
        background-color: #fff;
        border: 1px solid #ccc !important;
        padding: 10px !important;
    }
    /* Fix for button sticking if validation fails */
    .btn[disabled] {
        cursor: not-allowed;
    }
</style>
@endsection

@section('content')
<div class="box box-success">
    <div class="box-header">
        <h3 class="box-title">List of Recipes</h3>
    </div>

    <div class="box-header">
        <a onclick="addForm()" class="btn btn-success"><i class="fa fa-plus"></i> Add New Recipe</a>
    </div>

    <div class="box-body">
        <table id="recipes-table" class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Photo</th>
                    <th>Title</th>
                    <th>Note</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

@include('recipes.form')
@endsection

@section('bot')
<script src="{{ asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/validator/validator.min.js') }}"></script>

<script type="text/javascript">
var table = $('#recipes-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('api.recipes') }}",
    columns: [
        {data: 'id', name: 'id'},
        {data: 'show_photo', name: 'show_photo'},
        {data: 'title', name: 'title'},
        {data: 'note', name: 'note'},
        {data: 'action', name: 'action', orderable: false, searchable: false}
    ]
});

function addForm() {
    save_method = "add";
    $('input[name=_method]').val('POST');
    $('#modal-form').modal('show');
    $('#modal-form form')[0].reset();

    // Reset Trix
    var element = document.querySelector("#instructions_editor");
    if (element)
        element.editor.loadHTML("");

    $('.modal-title').text('Add Recipe');
    // RE-ENABLE BUTTON IMMEDIATELY
    $('#modal-form button[type=submit]').prop('disabled', false).removeClass('disabled');
}

function editForm(id) {
    save_method = 'edit';
    $('input[name=_method]').val('PATCH');
    $('#modal-form form')[0].reset();

    $.ajax({
        url: "{{ url('recipes') }}" + '/' + id + "/edit",
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            $('#modal-form').modal('show');
            $('.modal-title').text('Edit Recipe');

            $('#id').val(data.id);
            $('#title').val(data.title);
            $('#note').val(data.note);

            var element = document.querySelector("#instructions_editor");
            if (element)
                element.editor.loadHTML(data.instructions || "");

            // RE-ENABLE BUTTON IMMEDIATELY
            $('#modal-form button[type=submit]').prop('disabled', false).removeClass('disabled');
        },
        error: function () {
            swal({title: 'Oops...', text: 'Nothing Data', type: 'error'});
        }
    });
}

// Standard Delete function
function deleteData(id) {
    var csrf_token = $('meta[name="csrf-token"]').attr('content');
    swal({
        title: 'Are you sure?',
        type: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#d33',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then(function () {
        $.ajax({
            url: "{{ url('recipes') }}" + '/' + id,
            type: "POST",
            data: {'_method': 'DELETE', '_token': csrf_token},
            success: function (data) {
                table.ajax.reload();
                swal({title: 'Success!', text: data.message, type: 'success', timer: '1500'});
            }
        });
    });
}

$(function () {
    // Force Trix to update the hidden input and tell Validator it changed
    document.addEventListener("trix-change", function (event) {
        var content = $('#instructions_input').val();
        // If there's content, manually remove the 'disabled' state from the button
        if (content.length > 0) {
            $('#modal-form button[type=submit]').prop('disabled', false).removeClass('disabled');
        }
    });

    // HANDLE SUBMIT MANUALLY TO BYPASS VALIDATOR GLITCHES
    $('#form-recipe').on('submit', function (e) {

        var trixOutput = document.querySelector("trix-editor").value;
        $('#instructions_input').val(trixOutput);
        if (e.isDefaultPrevented()) {
            // If validator says no, let's check if it's just the Instructions field
            var instructions = $('#instructions_input').val();
            if (instructions.length === 0) {
                return false; // Truly empty, stop here
            }
        }

        // If we reach here, proceed with AJAX
        var id = $('#id').val();
        var url = (save_method == 'add') ? "{{ url('recipes') }}" : "{{ url('recipes') . '/' }}" + id;

        $.ajax({
            url: url,
            type: "POST",
            data: new FormData(this),
            contentType: false,
            processData: false,
            success: function (data) {
                $('#modal-form').modal('hide');
                table.ajax.reload();
                swal({title: 'Success!', text: data.message, type: 'success', timer: '1500'});
            },
            error: function (data) {
                var errors = data.responseJSON;
                var errorHtml = '<ul>';
                $.each(errors.errors, function (key, value) {
                    errorHtml += '<li>' + value[0] + '</li>';
                });
                errorHtml += '</ul>';
                swal({title: 'Validation Error', html: errorHtml, type: 'error'});
            }
        });

        return false;
    });
});
</script>
@endsection