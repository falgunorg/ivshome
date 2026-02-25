@extends('layouts.master')

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Requisition Details: {{ $requisition->requisition_no }}</h3>
        <div class="pull-right">
            <a href="{{ route('requisitions.index') }}" class="btn btn-default btn-sm">Back to List</a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th>Status</th><td><span class="label label-warning">{{ strtoupper($requisition->status) }}</span></td></tr>
                    <tr><th>Requested By</th><td>{{ $requisition->user->name }}</td></tr>
                    <tr><th>Date</th><td>{{ $requisition->requested_date }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th>Remarks</th><td>{{ $requisition->remarks ?? 'N/A' }}</td></tr>
                    <tr><th>Created At</th><td>{{ $requisition->created_at->format('d M Y, h:i A') }}</td></tr>
                </table>
            </div>
        </div>

        <h4 style="margin-top: 20px;">Requested Items</h4>
        <table class="table table-striped table-bordered">
            <thead>
                <tr class="bg-gray">
                    <th>#</th>
                    <th>Grocery Item</th>
                    <th class="text-center">Qty Requested</th>
                    <th class="text-center">Qty Received</th>
                    <th>Progress</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requisition->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->grocery->name }}</td>
                    <td class="text-center">{{ $item->qty_requested }}</td>
                    <td class="text-center">{{ $item->qty_received }}</td>
                    <td>
                        @php 
                        $percent = ($item->qty_requested > 0) ? ($item->qty_received / $item->qty_requested) * 100 : 0;
                        @endphp
                        <div class="progress progress-xs">
                            <div class="progress-bar progress-bar-success" style="width: {{ $percent }}%"></div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection