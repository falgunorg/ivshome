@extends('layouts.master')

@section('content')
<section class="content">
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-upload"></i> Issue Grocery Item</h3>
        </div>
        <form action="{{ route('issues.store') }}" method="POST">
            @csrf
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Item to Issue</label>
                            <select name="grocery_id" class="form-control select2" id="issue_item_select" required>
                                <option value="">-- Select Item --</option>
                                @foreach($groceries as $item)
                                <option value="{{ $item->id }}" data-stock="{{ $item->qty }}" data-unit="{{ $item->unit }}">
                                    {{ $item->name }} (Available: {{ $item->qty }} {{ $item->unit }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Issue Date</label>
                            <input type="date" name="issue_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Issued To (Dept/Person)</label>
                            <input type="text" name="issued_to" class="form-control" placeholder="e.g. Main Kitchen" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Quantity to Issue</label>
                            <div class="input-group">
                                <input type="number" name="issued_qty" id="issued_qty" class="form-control" step="0.01" min="0.01" required>
                                <span class="input-group-addon unit-label">Unit</span>
                            </div>
                            <small class="text-danger" id="stock_warning" style="display:none;">Insufficient Stock!</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-warning" id="submit_issue"><i class="fa fa-share"></i> Confirm Issue</button>
            </div>
        </form>
    </div>
</section>
@endsection

@section('bot')
<script>
    $(document).ready(function () {
        $('.select2').select2();

        $('#issue_item_select').on('change', function () {
            let selected = $(this).find(':selected');
            $('.unit-label').text(selected.data('unit') || 'Unit');
        });

        $('#issued_qty').on('input', function () {
            let qty = parseFloat($(this).val());
            let stock = parseFloat($('#issue_item_select').find(':selected').data('stock'));

            if (qty > stock) {
                $('#stock_warning').show();
                $('#submit_issue').prop('disabled', true);
            } else {
                $('#stock_warning').hide();
                $('#submit_issue').prop('disabled', false);
            }
        });
    });
</script>
@endsection