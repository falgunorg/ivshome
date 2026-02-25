<div class="modal fade" id="modal-form" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" data-toggle="validator" enctype="multipart/form-data">
                @csrf {{ method_field('POST') }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Grocery Name (English)</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Bengali Name</label>
                                <input type="text" name="bengali_name" id="bengali_name" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" id="category" class="form-control">
                            <option value="">Select Category</option>
                            <option value="Essential">Essential</option>
                            <option value="Fish">Fish</option>
                            <option value="Meat">Meat</option>
                            <option value="Oil">Oil</option>
                            <option value="Pulse">Pulse</option>
                            <option value="Spices">Spices</option>
                            <option value="Vegetables">Vegetables</option>
                            <option value="Snacks">Snacks</option>

                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Quantity</label>
                                <input type="number" step="0.01" name="qty" id="qty" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Unit</label>
                                <select name="unit" id="unit" class="form-control" required>
                                    <option value="kg">kg</option>
                                    <option value="gram">gram</option>
                                    <option value="pcs">pcs</option>
                                    <option value="litre">litre</option>
                                    <option value="packet">packet</option>
                                    <option value="box">box</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Min. Stock</label>
                                <input type="number" name="min_stock" id="min_stock" class="form-control" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Photo</label>
                        <input type="file" name="image" id="image" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Item</button>
                </div>
            </form>
        </div>
    </div>
</div>