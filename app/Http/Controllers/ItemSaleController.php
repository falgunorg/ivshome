<?php

namespace App\Http\Controllers;

use App\Category;
use App\Customer;
use App\Exports\ExportProdukSale;
use App\Item;
use App\ItemSale;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ItemSaleController extends Controller {

    public function __construct() {
        $this->middleware('role:admin,staff');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
         $items = Item::orderBy('name', 'ASC')->get(['id', 'name', 'serial_number']);

        $customers = Customer::orderBy('name', 'ASC')
                ->get()
                ->pluck('name', 'id');

        $invoice_data = ItemSale::all();
        return view('item_sale.index', compact('items', 'customers', 'invoice_data'));
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
        $this->validate($request, [
            'item_id' => 'required|exists:items,id',
            'customer_id' => 'required',
            'qty' => 'required|integer|min:1',
            'date' => 'required|date'
        ]);

        // Start a transaction to ensure both operations succeed or fail together
        return DB::transaction(function () use ($request) {

                    // 1. Find the item and check stock availability
                    $item = Item::findOrFail($request->item_id);

                    if ($item->qty < $request->qty) {
                        return response()->json([
                                    'success' => false,
                                    'message' => 'Insufficient stock!'
                                        ], 400);
                    }

                    // 2. Create the Sale record
                    $data = $request->all();
                    $data['user_id'] = Auth::id();
                    ItemSale::create($data);

                    // 3. Update the Inventory
                    $item->decrement('qty', $request->qty);

                    return response()->json([
                                'success' => true,
                                'message' => 'Items Out Created'
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
        $item_sale = ItemSale::find($id);
        return $item_sale;
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
            'customer_id' => 'required',
            'qty' => 'required',
            'date' => 'required'
        ]);

        $item_sale = ItemSale::findOrFail($id);
        $item_sale->update($request->all());

        $item = Item::findOrFail($request->item_id);
        $item->qty -= $request->qty;
        $item->update();

        return response()->json([
                    'success' => true,
                    'message' => 'Item Out Updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        ItemSale::destroy($id);

        return response()->json([
                    'success' => true,
                    'message' => 'Items Delete Deleted'
        ]);
    }

    public function apiItemsOut() {
        $item = ItemSale::all();

        return Datatables::of($item)
                        ->addColumn('items_name', function ($item) {
                            return $item->item->name;
                        })
                        ->addColumn('customer_name', function ($item) {
                            return $item->customer->name;
                        })
                        ->addColumn('by', function ($item) {
                            return $item->user->name;
                        })
                        ->addColumn('action', function ($item) {
                            return'<a onclick="editForm(' . $item->id . ')" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i> Edit</a> ' .
                                    '<a onclick="deleteData(' . $item->id . ')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
                        })
                        ->rawColumns(['items_name', 'customer_name', 'action'])->make(true);
    }

    public function exportItemSaleAll() {
        $item_sale = ItemSale::with('item', 'user', 'customer')->get();
        $pdf = PDF::loadView('item_sale.itemSaleAllPDF', compact('item_sale'));
        return $pdf->download('item_out.pdf');
    }

    public function exportItemSale($id) {
        $item_sale = ItemSale::with('item', 'user', 'customer')->findOrFail($id);
        $pdf = PDF::loadView('item_sale.itemSalePDF', compact('item_sale'));
        return $pdf->download($item_sale->id . '_item_out.pdf');
    }

    public function exportExcel() {
        return (new ExportProdukSale)->download('item_sale.xlsx');
    }
}
