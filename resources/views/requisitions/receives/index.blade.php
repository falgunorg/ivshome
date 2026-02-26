@extends('layouts.master')
@section('content')
<section class="content">
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Receive History (Stock In)</h3>
            <div class="box-tools">
                <a href="{{ route('receives.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> New Receive</a>
            </div>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Requisition #</th>
                        <th>Item Name</th>
                        <th>Qty Received</th>
                        <th>Expiry Date</th>
                        <th>Lot #</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($receives as $receive)
                    <tr>
                        <td>{{ $receive->purchase_date }}</td>
                        <td>{{ $receive->requisitionItem->requisition->requisition_no ?? 'N/A' }}</td>
                        <td>{{ $receive->grocery->name }}</td>
                        <td>{{ $receive->received_qty }} {{ $receive->grocery->unit }}</td>
                        <td>
                            <span class="label {{ $receive->expiry_date < date('Y-m-d') ? 'label-danger' : 'label-default' }}">
                                {{ $receive->expiry_date }}
                            </span>
                        </td>
                        <td>{{ $receive->lot_number }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $receives->links() }} {{-- Pagination Links --}}
        </div>
    </div>
</section>
@endsection