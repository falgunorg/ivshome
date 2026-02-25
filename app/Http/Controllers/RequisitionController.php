<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GroceryRequisition;
use App\GroceryRequisitionItem;
use App\Grocery;
use App\GroceryReceive;
use App\GroceryIssue;
use Illuminate\Support\Facades\Auth;
use DB;

class RequisitionController extends Controller {

    public function index() {
        $groceries = Grocery::all();
        $requisitions = GroceryRequisition::with('items.grocery')->latest()->get();
        return view('requisitions.index', compact('requisitions', 'groceries'));
    }

    public function create() {
        $groceries = Grocery::all();
        return view('requisitions.create', compact('groceries'));
    }

    public function store(Request $request) {
        $request->validate([
            'requested_date' => 'required|date',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.grocery_id' => 'required|exists:groceries,id',
            'items.*.qty' => 'required|numeric|min:0.1',
        ]);

        print_r($request->validate);

        try {
            return DB::transaction(function () use ($request) {
                        // 1. Create the Main Requisition
                        $requisition = new GroceryRequisition();
                        $requisition->requisition_no = 'REQ-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
                        $requisition->user_id = Auth::id();
                        $requisition->requested_date = $request->requested_date;
                        $requisition->remarks = $request->remarks;
                        $requisition->status = 'pending';
                        // Note: approved_by and approved_at are left null/empty here
                        $requisition->save();

                        // 2. Prepare Items
                        $items = [];
                        foreach ($request->items as $item) {
                            $items[] = [
                                'grocery_requisition_id' => $requisition->id,
                                'grocery_id' => $item['grocery_id'],
                                'qty_requested' => $item['qty'],
                                'qty_received' => 0,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }

                        // 3. Bulk Insert for performance
                        GroceryRequisitionItem::insert($items);

                        return redirect()->route('requisitions.index')
                                        ->with('success', "Requisition {$requisition->requisition_no} created!");
                    });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show($id) {
        // Eager load items and the grocery details
        $requisition = GroceryRequisition::with(['items.grocery', 'user'])->findOrFail($id);
        return view('requisitions.show', compact('requisition'));
    }
}
