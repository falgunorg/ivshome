<?php

namespace App\Http\Controllers;

use App\Exports\ExportProdukPurchase;
use App\Item;
use App\ItemPurchase;
use App\Supplier;
use PDF;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ItemPurchaseController extends Controller {

    public function __construct() {
        $this->middleware('role:admin,staff');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $items = Item::orderBy('name', 'ASC')
                ->get()
                ->pluck('name', 'id');

        $suppliers = Supplier::orderBy('name', 'ASC')
                ->get()
                ->pluck('name', 'id');

        $invoice_data = ItemPurchase::all();
        return view('item_purchase.index', compact('items', 'suppliers', 'invoice_data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // 1. Stricter Validation
        $this->validate($request, [
            'item_id' => 'required|exists:items,id',
            'supplier_id' => 'required|exists:suppliers,id', // Recommended to check if supplier exists
            'qty' => 'required|numeric|min:0.01', // Ensure it's a positive number
            'date' => 'required|date'
        ]);

        // 2. Wrap in a Transaction
        return DB::transaction(function () use ($request) {

                    // Prepare data without modifying the global $request object
                    $data = $request->all();
                    $data['user_id'] = Auth::id();

                    // 3. Create the Purchase Record
                    ItemPurchase::create($data);

                    // 4. Update Inventory (Increment)
                    $item = Item::findOrFail($request->item_id);
                    $item->increment('qty', $request->qty);

                    return response()->json([
                                'success' => true,
                                'message' => 'Items In Created Successfully'
                    ]);
                });
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $item_purchase = ItemPurchase::find($id);
        return $item_purchase;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $this->validate($request, [
            'item_id' => 'required',
            'supplier_id' => 'required',
            'qty' => 'required',
            'date' => 'required'
        ]);

        $item_purchase = ItemPurchase::findOrFail($id);
        $item_purchase->update($request->all());

        $item = Item::findOrFail($request->item_id);
        $item->qty += $request->qty;
        $item->update();

        return response()->json([
                    'success' => true,
                    'message' => 'Item In Updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        ItemPurchase::destroy($id);

        return response()->json([
                    'success' => true,
                    'message' => 'Items In Deleted'
        ]);
    }

    public function apiItemsIn() {
        $item = ItemPurchase::all();

        return Datatables::of($item)
                        ->addColumn('items_name', function ($item) {
                            return $item->item->name;
                        })
                        ->addColumn('supplier_name', function ($item) {
                            return $item->supplier->name;
                        })
                        ->addColumn('by', function ($item) {
                            return $item->user->name;
                        })
                        ->addColumn('action', function ($item) {
                            return '<a onclick="editForm(' . $item->id . ')" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i> Edit</a> ' .
                                    '<a onclick="deleteData(' . $item->id . ')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> Delete</a> ';
                        })
                        ->rawColumns(['items_name', 'supplier_name', 'action'])->make(true);
    }

    public function exportItemPurchaseAll() {
        $item_purchase = ItemPurchase::with('item', 'user', 'supplier')->get();
        $pdf = PDF::loadView('item_purchase.itemPurchaseAllPDF', compact('item_purchase'));
        return $pdf->download('item_enter.pdf');
    }

    public function exportItemPurchase($id) {
        $item_purchase = ItemPurchase::with('item', 'user', 'supplier')->findOrFail($id);
        $pdf = PDF::loadView('item_purchase.itemPurchasePDF', compact('item_purchase'));
        return $pdf->download($item_purchase->id . '_item_enter.pdf');
    }

    public function exportExcel() {
        return (new ExportProdukPurchase)->download('item_purchase.xlsx');
    }
}
