@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="panel-title">
                        <strong>Item Type: {{ $itemType->name }}</strong> 
                    </h3>
                    <a href="{{ route('item-types.index') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Unique Items:</label> 
                            <span class="label label-primary">{{ $itemType->items->count() }}</span>
                        </div>
                        <div class="col-md-3">
                            <label>Total Stock Qty:</label> 
                            <span class="label label-success">{{ $itemType->items->sum('qty') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Items under this Type</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr class="bg-light">
                                    <th >S/N</th>
                                    <th >Image</th>
                                    <th>Item Name</th>
                                    <th>Qty</th>
                                    <th>Physical Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($itemType->items as $item)
                                <tr>

                                    <td>
                                        <a href="{{route('items.show', $item->id)}}">{{ $item->serial_number }}</a>
                                    </td>
                                    <td>
                                        <img src="{{ $item->show_photo }}" style="width: 45px; height: 45px; border-radius: 4px;" alt="item">
                                    </td>
                                    <td>
                                        <strong>{{ $item->name }}</strong><br>
                                        <small class="text-muted">{{ $item->description }}</small>
                                    </td>
                                    <td>{{ $item->qty }}</td>
                                    <td>
                                        <i class="fa fa-map-marker text-muted"></i>

                                        {{-- Base Location --}}
                                        @if ($item->itemLocation)
                                        <a href="{{ route('locations.show', $item->itemLocation->id) }}">
                                            {{ $item->itemLocation->name }}
                                        </a>
                                        @else
                                        <span class="text-muted">No Location</span>
                                        @endif

                                        {{-- Cabinet --}}
                                        @if ($item->cabinet)
                                        <i class="fa fa-angle-right" style="margin:0 2px;"></i>
                                        <a href="{{ route('cabinets.show', $item->cabinet->id) }}">
                                            {{ $item->cabinet->title }}
                                        </a>
                                        @endif

                                        {{-- Drawer --}}
                                        @if ($item->drawer)
                                        <i class="fa fa-angle-right" style="margin:0 2px;"></i>
                                        <span class="text-muted">{{ $item->drawer->title }}</span>
                                        @endif
                                    </td>

                                </tr>
                                @empty
                                <tr>
                                    <td class="text-center">No items found for this type.</td>
                                </tr>
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