@extends('layouts.master')
@section('content')

<div class="box box-default">
    <div class="box-body">
        <form action="{{ route('requisitions.index') }}" method="GET">
            <div class="row">
                <div class="col-md-2">
                    <label>Search Req #</label>
                    <input onchange="this.form.submit()" type="text" name="search" class="form-control input-sm" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label>Item or Category</label>
                    <input onchange="this.form.submit()" type="text" name="item_search" class="form-control input-sm" value="{{ request('item_search') }}">
                </div>
                <div class="col-md-2">
                    <label>Status</label>
                    <select name="status" class="form-control input-sm" onchange="this.form.submit()">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>






                <div class="col-md-2">
                    <label>Quick Period</label>
                    <select name="period" class="form-control input-sm" onchange="this.form.submit()">
                        <option value="">Custom Range Below</option>
                        <option value="this_month" {{ request('period') == 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                        <option value="this_year" {{ request('period') == 'this_year' ? 'selected' : '' }}>This Year</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label>From Date</label>
                    <input type="date" name="from_date" class="form-control input-sm" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-1">
                    <label>To Date</label>
                    <input type="date" name="to_date" class="form-control input-sm" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-2 text-right">
                    <label>&nbsp;</label><br>
                    <button type="submit" class="btn btn-sm btn-success">Apply Filters</button>
                    <a href="{{ route('requisitions.index') }}" class="btn btn-sm btn-default">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">Requisition List</h3>
        <a href="{{ route('requisitions.create') }}" class="btn btn-xs btn-success pull-right"><i class="fa fa-plus"></i> New Requisition</a>
    </div>

    <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
            <thead>
                <tr>
                    {{-- Sortable Headers --}}
                    <th><a href="{{ request()->fullUrlWithQuery(['sort' => 'requisition_no', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                            Req. Number @if(request('sort') == 'requisition_no') <i class="fa fa-sort-amount-{{ request('direction') }}"></i> @endif
                        </a></th>

                    <th><a href="{{ request()->fullUrlWithQuery(['sort' => 'requested_date', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                            Date @if(request('sort') == 'requested_date') <i class="fa fa-sort-amount-{{ request('direction') }}"></i> @endif
                        </a></th>

                    <th>Requested By</th>
                    <th>Items</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requisitions as $req)
                <tr>
                    <td style="min-width: 180px"><a href="{{ route('requisitions.show', $req->id) }}"><b>{{ $req->requisition_no }}</b></a></td>
                    <td style="min-width: 100px">{{ \Carbon\Carbon::parse($req->requested_date)->format('d M, Y') }}</td>
                    <td>{{ $req->user->name }}</td>
                    <td>
                        @foreach($req->items as $item)
                        <span class="label label-default">{{ $item->grocery->name }} ({{ $item->grocery->bengali_name }}) - {{$item->qty_requested}} {{$item->grocery->unit}}</span>
                        @endforeach
                    </td>
                    <td>
                        @if($req->status == 'pending')
                        <span class="label label-warning">Pending</span>
                        @elseif($req->status == 'partial')
                        <span class="label label-info">Partial</span>
                        @else
                        <span class="label label-success">Completed</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('requisitions.show', $req->id) }}" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No requisitions match your criteria.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="box-footer clearfix">
        <div class="pull-left">
            Showing {{ $requisitions->firstItem() }} to {{ $requisitions->lastItem() }} of {{ $requisitions->total() }} entries
        </div>
        <div class="pull-right">
            {{ $requisitions->links() }}
        </div>
    </div>
</div>
@endsection