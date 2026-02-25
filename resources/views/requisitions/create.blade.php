@extends('layouts.master')

@section('content')
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
                                    <tr>
                                        <td>
                                            <select name="items[0][grocery_id]" class="form-control select2 item-select" style="width: 100%;" required>
                                                <option value="">-- Search Item --</option>
                                                @foreach($groceries as $item)
                                                {{-- Store the unit in a data attribute --}}
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
                            <button type="reset" class="btn btn-danger btn-lg">Cancel</button>
                            <button type="submit" class="btn btn-success btn-lg"><i class="fa fa-save"></i> Save Requisition</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@section('bot')
<script>
    $(document).ready(function () {
        // Initialize Select2 on the first row
        $('.select2').select2();

        let rowIdx = 1;

        // 1. Add New Row
        $('#add_row').on('click', function () {
            let tableBody = $('#items_table tbody');
            let newRow = `
                <tr>
                    <td>
                        <select name="items[${rowIdx}][grocery_id]" class="form-control select2 item-select" style="width: 100%;" required>
                            <option value="">-- Search Item --</option>
                            @foreach($groceries as $item)
                                <option value="{{ $item->id }}" data-unit="{{ $item->unit }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <div class="input-group">
                            <input type="number" name="items[${rowIdx}][qty]" class="form-control" placeholder="0.00" step="0.01" min="0.01" required>
                            <span class="input-group-addon unit-label">Unit</span>
                        </div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>`;

            tableBody.append(newRow);

            // Initialize Select2 on the newly added row
            tableBody.find('tr:last .select2').select2();
            rowIdx++;
        });

        // 2. Remove Row
        $(document).on('click', '.remove-row', function () {
            $(this).closest('tr').remove();
        });

        // 3. Update Unit Label when Item is selected
        $(document).on('change', '.item-select', function () {
            // Get the unit from the selected option's data attribute
            let unit = $(this).find(':selected').data('unit');

            // Find the unit label in the same row and update it
            if (unit) {
                $(this).closest('tr').find('.unit-label').text(unit);
            } else {
                $(this).closest('tr').find('.unit-label').text('Unit');
            }
        });
    });
</script>


<style>
    .shadow {
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .table > therapeutic > tr > th {
        vertical-align: middle;
    }
    .btn-lg {
        padding: 10px 30px;
        font-weight: bold;
    }
    hr {
        border-top: 1px solid #eee;
        margin-top: 10px;
        margin-bottom: 20px;
    }
</style>
@endsection