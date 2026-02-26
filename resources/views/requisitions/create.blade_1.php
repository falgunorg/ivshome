@extends('layouts.master')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('requisitions.store') }}" method="POST">
                @csrf
                <div class="box box-success shadow">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-file-text-o"></i> New Purchase Requisition</h3>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Requested Date <span class="text-danger">*</span></label>
                                    <input type="date" name="requested_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Remarks / Note (Optional)</label>
                                    <input type="text" name="remarks" class="form-control" placeholder="Enter instructions...">
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h4 class="text-green"><i class="fa fa-shopping-cart"></i> Requested Items</h4>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="items_table">
                                <thead class="bg-gray">
                                    <tr>
                                        <th width="60%">Grocery Item</th>
                                        <th width="30%">Quantity</th>
                                        <th width="10%" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- The first row --}}
                                    <tr class="item-row">
                                        <td>
                                            <select name="items[0][grocery_id]" class="form-control select2 item-select" style="width: 100%;" required>
                                                <option value="">-- Search Item --</option>
                                                @foreach($groceries as $item)
                                                <option value="{{ $item->id }}" data-unit="{{ $item->unit }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" name="items[0][qty]" class="form-control" placeholder="0.00" step="0.01" min="0.01" required>
                                                <span class="input-group-addon unit-label">Unit</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            {{-- First row trash is disabled to ensure at least one item --}}
                                            <button type="button" class="btn btn-danger btn-sm disabled"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <button type="button" id="add_row" class="btn btn-info btn-sm">
                            <i class="fa fa-plus"></i> Add Another Item
                        </button>
                    </div>

                    <div class="box-footer">
                        <div class="pull-right">
                            <a href="{{ route('requisitions.index') }}" class="btn btn-default btn-lg">Cancel</a>
                            <button type="submit" class="btn btn-success btn-lg"><i class="fa fa-save"></i> Save Requisition</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- Template for new rows (hidden from view) --}}
<table style="display:none;">
    <tr id="row_template">
        <td>
            <select class="form-control item-select-dynamic" style="width: 100%;">
                <option value="">-- Search Item --</option>
                @foreach($groceries as $item)
                <option value="{{ $item->id }}" data-unit="{{ $item->unit }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <div class="input-group">
                <input type="number" class="form-control qty-input" step="0.01" min="0.01" placeholder="0.00">
                <span class="input-group-addon unit-label">Unit</span>
            </div>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button>
        </td>
    </tr>
</table>

@endsection

@section('bot')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    let rowIdx = 1;

    // Helper to initialize Select2
    function initSelect2(element) {
        $(element).select2({
            placeholder: "-- Search Item --",
            allowClear: true
        });
    }

    // Initialize the first row
    initSelect2('.select2');

    // ADD ROW LOGIC
    $('#add_row').on('click', function (e) {
        e.preventDefault();

        // Clone the template row
        let $newRow = $('#row_template').clone();
        $newRow.removeAttr('id'); // Remove the template ID

        // Update the names with the current index
        $newRow.find('.item-select-dynamic')
                .attr('name', `items[${rowIdx}][grocery_id]`)
                .addClass('select2-dynamic')
                .prop('required', true);

        $newRow.find('.qty-input')
                .attr('name', `items[${rowIdx}][qty]`)
                .prop('required', true);

        // Append to the table
        $('#items_table tbody').append($newRow);

        // Initialize Select2 on the newly added row specifically
        initSelect2($newRow.find('.select2-dynamic'));

        rowIdx++;
    });

    // REMOVE ROW LOGIC
    $(document).on('click', '.remove-row', function () {
        $(this).closest('tr').remove();
    });

    // UNIT LABEL LOGIC
    $(document).on('change', '.item-select, .item-select-dynamic', function () {
        let unit = $(this).find(':selected').data('unit') || 'Unit';
        $(this).closest('tr').find('.unit-label').text(unit);
    });
});
</script>

<style>
    .shadow {
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .table > thead > tr > th {
        vertical-align: middle;
        background-color: #f4f4f4;
    }
    .btn-lg {
        padding: 10px 30px;
        font-weight: bold;
    }
    hr {
        border-top: 1px solid #eee;
        margin: 15px 0;
    }
    /* Fix for Select2 height in Bootstrap */
    .select2-container .select2-selection--single {
        height: 34px !important;
        border: 1px solid #ccc !important;
    }
</style>
@endsection