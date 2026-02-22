@extends('layouts.master')

@section('top')
<link rel="stylesheet" href="{{ asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('content')
<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-clock-o"></i> Pending Staff Requests</h3>
    </div>

    <div class="box-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('error') }}
        </div>
        @endif

        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading"><b>Sale Requests</b></div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Staff</th>
                                    <th>Qty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pending_sales as $sale)
                                <tr>
                                    <td>{{ $sale->item->name }}</td>
                                    <td>{{ $sale->user->name }}</td>
                                    <td><span class="label label-warning">{{ $sale->qty }}</span></td>
                                    <td>
                                        <form action="{{ route('requests.sale.approve', $sale->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-success" title="Approve"><i class="fa fa-check"></i></button>
                                        </form>
                                        <form action="{{ route('requests.decline', ['id' => $sale->id, 'type' => 'sale']) }}" method="POST" style="display:inline;">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger" title="Decline"><i class="fa fa-times"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted">No pending sales</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading"><b>Purchase Requests</b></div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Staff</th>
                                    <th>Qty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pending_purchases as $purchase)
                                <tr>
                                    <td>{{ $purchase->item->name }}</td>
                                    <td>{{ $purchase->user->name }}</td>
                                    <td><span class="label label-primary">{{ $purchase->qty }}</span></td>
                                    <td>
                                        <form action="{{ route('requests.purchase.approve', $purchase->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-success" title="Approve"><i class="fa fa-check"></i></button>
                                        </form>
                                        <form action="{{ route('requests.decline', ['id' => $purchase->id, 'type' => 'purchase']) }}" method="POST" style="display:inline;">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger" title="Decline"><i class="fa fa-times"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted">No pending purchases</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading"><b>Damage Requests</b></div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Staff</th>
                                    <th>Qty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pending_damages as $damage)
                                <tr>
                                    <td>{{ $damage->item->name }}</td>
                                    <td>{{ $damage->user->name }}</td>
                                    <td><span class="label label-danger">{{ $damage->qty }}</span></td>
                                    <td>
                                        <form action="{{ route('requests.damage.approve', $damage->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-success" title="Approve"><i class="fa fa-check"></i></button>
                                        </form>
                                        <form action="{{ route('requests.decline', ['id' => $damage->id, 'type' => 'damage']) }}" method="POST" style="display:inline;">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger" title="Decline"><i class="fa fa-times"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted">No pending damage reports</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection