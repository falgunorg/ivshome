@extends('layouts.master')

@section('content')
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-download"></i> Receive Grocery Items</h3>
        </div>
        <form action="{{ route('receives.store') }}" method="POST">
            @csrf
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Select Requisition Item <span class="text-danger">*</span></label>
                            <select name="requisition_item_id" class="form-control select2" id="req_item_select" required>
                                <option value="">-- Select Pending Item --</option>
                                @foreach($pendingItems as $item)
                                <option value="{{ $item->id }}" 
                                        data-name="{{ $item->grocery->name }}" 
                                        data-pending="{{ $item->qty_requested - $item->qty_received }}"
                                        data-unit="{{ $item->grocery->unit }}">
                                    {{ $item->requisition->requisition_no }} | {{ $item->grocery->name }} 
                                    (Pending: {{ $item->qty_requested - $item->qty_received }} {{ $item->grocery->unit }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Purchase Date</label>
                            <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Expiry Date</label>
                            <input type="date" name="expiry_date" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Quantity to Receive</label>
                            <div class="input-group">
                                <input type="number" name="received_qty" id="received_qty" class="form-control" step="0.01" min="0.01" required>
                                <span class="input-group-addon unit-label">Unit</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Lot / Batch Number</label>
                            <input type="text" name="lot_number" class="form-control" placeholder="Optional">
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update Stock</button>
            </div>
        </form>
    </div>
</section>
@endsection

@section('bot')
<script>
    $(document).ready(function () {
        $('.select2').select2();

        $('#req_item_select').on('change', function () {
            let selected = $(this).find(':selected');
            $('.unit-label').text(selected.data('unit') || 'Unit');
            $('#received_qty').val(selected.data('pending'));
        });
    });
</script>
@endsection