@extends('layouts.master')
@section('content')


<div class="box box-success">

    <div class="box-header">
        <h3 class="box-title">List of Requisitions</h3>
        <span class="pull-right">
            <a href="{{ route('requisitions.create') }}" class="btn btn-success" style="margin-top: -8px;"><i class="fa fa-plus"></i> Add Requisition</a>

        </span>
    </div>


    <!-- /.box-header -->
    <div class="box-body table-responsive">
        <table id="items-table" class="table table-bordered table-hover table-striped">
            <thead class="bg-light">
                <tr>
                    <th>Req. Number</th>
                    <th>Date</th>
                    <th>Requested By</th>
                    <th>Items Summary</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requisitions as $req)
                <tr>
                    <td class="align-middle font-weight-bold">{{ $req->requisition_no }}</td>
                    <td class="align-middle">{{ $req->requested_date}}</td>
                    <td class="align-middle">{{ $req->user->name }}</td>
                    <td class="align-middle">
                        <span class="text-muted small">
                            {{ $req->items->count() }} Items 
                            ({{ $req->items->sum('qty_requested') }} total units)
                        </span>
                    </td>
                    <td class="align-middle">
                        @if($req->status == 'pending')
                        <span class="badge badge-warning text-dark">Pending</span>
                        @elseif($req->status == 'partial')
                        <span class="badge badge-info text-white">Partially Received</span>
                        @else
                        <span class="badge badge-success">Completed</span>
                        @endif
                    </td>
                    <td class="text-right align-middle">
                        <div class="btn-group">
                            <a href="{{ route('requisitions.show', $req->id) }}" class="btn btn-sm btn-outline-secondary">
                                View
                            </a>

                          
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        No requisitions found. <a href="{{ route('requisitions.create') }}">Create one now?</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>



@endsection
