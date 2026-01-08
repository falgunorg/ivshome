<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content" style="padding: 15px;">

            <form id="form-item" method="post" class="form-horizontal" enctype="multipart/form-data">
                @csrf
                @method('POST')

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title"></h3>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="id" name="id">

                    <div class="form-group">
                        <label>Items</label>
                        @php
                        // Transform the array/collection to "ID | Name" format
                        $formattedItems = collect($items)->mapWithKeys(function ($name, $id) {
                        return [$id => $id . ' | ' . $name];
                        })->toArray();
                        @endphp

                        {!! Form::select('item_id', $formattedItems, null, [
                        'class' => 'form-control select', 
                        'placeholder' => '-- Choose Item --', 
                        'id' => 'item_id', 
                        'required'
                        ]) !!}
                        <span class="help-block with-errors"></span>
                    </div>

                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" class="form-control" id="qty" name="qty" min="1" required>
                    </div>

                    <div class="form-group">
                        <label>Date</label>
                        <input type="text" class="form-control" id="date" name="date" required>
                    </div>

                    <div class="form-group">
                        <label>Remarks</label>
                        <textarea class="form-control" name="remarks"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Evidence Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </form>

        </div>
    </div>
</div>
