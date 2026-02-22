<div class="modal fade" id="modal-form" tabindex="1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg"> <div class="modal-content">
            <form id="form-item" method="post" class="form-horizontal" data-toggle="validator" enctype="multipart/form-data">
                {{ csrf_field() }} {{ method_field('POST') }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title"></h3>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>User Name</label>
                                    <input readonly type="text" class="form-control" value="{{ \Auth::user()->name }}">
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Item Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Item Type</label>
                                    <select class="form-control" id="item_type" name="item_type" required>
                                        <option value="" selected disabled>-- Select Category --</option>
                                        @foreach($item_types as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Quantity</label>
                                    <input type="number" min="1" class="form-control" id="qty" name="qty" required>
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Condition</label>
                                    <select class="form-control" id="condition" name="condition">
                                        <option value="New">New</option>
                                        <option value="Good">Good</option>
                                        <option value="Fair">Fair</option>
                                        <option value="Damaged">Damaged</option>
                                        <option value="Replace">Replace</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date of Purchase</label>
                                    <input type="date" class="form-control" id="date_of_purchase" name="date_of_purchase">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Warranty Time</label>
                                    <input type="date" class="form-control" id="warranty_date" name="warranty_date">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date of Expiry</label>
                                    <input type="date" class="form-control" id="date_of_expiry" name="date_of_expiry">
                                </div>
                            </div>
                        </div>



                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Location</label>
                                    <select class="form-control" id="location_id" name="location_id" required>
                                        <option value="" selected disabled>-- Select Location --</option>
                                        @foreach($locations as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cabinet</label>
                                    <select class="form-control" id="cabinet_id" name="cabinet_id">
                                        <option value="" selected disabled>-- Select Location First --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Drawer</label>
                                    <select class="form-control" id="drawer_id" name="drawer_id">
                                        <option value="" selected disabled>-- Select Cabinet First --</option>
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label>Item Image</label>
                            <input type="file" class="form-control" id="image" name="image">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Cancel</button>
                    <button type="submit" onclick="setPrint(true)" class="btn btn-warning"><i class="fa fa-print"></i> Submit & Print</button>
                    <button type="submit" onclick="setPrint(false)" class="btn btn-success">Submit Only</button>
                </div>

            </form>
        </div>
    </div>
</div>