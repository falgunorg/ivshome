@extends('layouts.master')
@section('content')
<section class="content">
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Issue History (Stock Out)</h3>
            <div class="box-tools">
                <a href="{{ route('issues.create') }}" class="btn btn-warning btn-sm"><i class="fa fa-share"></i> Issue Item</a>
            </div>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Item Name</th>
                        <th>Issued Qty</th>
                        <th>Issued To</th>
                        <th>Performed By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($issues as $issue)
                    <tr>
                        <td>{{ $issue->issue_date }}</td>
                        <td>{{ $issue->grocery->name }}</td>
                        <td>{{ $issue->issued_qty }} {{ $issue->grocery->unit }}</td>
                        <td>{{ $issue->issued_to }}</td>
                        <td>{{ $issue->user->name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $issues->links() }}
        </div>
    </div>
</section>
@endsection