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
use Carbon\Carbon;

class RequisitionController extends Controller {

    public function index(Request $request) {
        $query = GroceryRequisition::with(['user', 'items.grocery']);

        // --- DATE FILTERING LOGIC ---
        // 1. Quick Filters (Monthly / Yearly)
        if ($request->filled('period')) {
            if ($request->period == 'this_month') {
                $query->whereMonth('requested_date', Carbon::now()->month)
                        ->whereYear('requested_date', Carbon::now()->year);
            } elseif ($request->period == 'last_month') {
                $query->whereMonth('requested_date', Carbon::now()->subMonth()->month)
                        ->whereYear('requested_date', Carbon::now()->subMonth()->year);
            } elseif ($request->period == 'this_year') {
                $query->whereYear('requested_date', Carbon::now()->year);
            }
        }

        // 2. Custom Date Range (From - To)
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('requested_date', [$request->from_date, $request->to_date]);
        }

        // --- PREVIOUS SEARCH/SORT LOGIC ---
        $query->when($request->search, function ($q, $search) {
            $q->where('requisition_no', 'like', "%{$search}%");
        })->when($request->status, function ($q, $status) {
            $q->where('status', $status);
        })->when($request->item_search, function ($q, $itemSearch) {
            $q->whereHas('items.grocery', function ($sub) use ($itemSearch) {
                $sub->where('name', 'like', "%{$itemSearch}%")
                        ->orWhere('category', 'like', "%{$itemSearch}%");
            });
        });

        $sort = $request->get('sort', 'requested_date');
        $direction = $request->get('direction', 'desc');
        $requisitions = $query->orderBy($sort, $direction)->paginate(20)->withQueryString();
        return view('requisitions.index', compact('requisitions'));
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

    public function approve($id) {
        $requisition = GroceryRequisition::findOrFail($id);

        $requisition->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Requisition approved successfully.');
    }

    // List all Receive history
    public function indexReceive() {
        $receives = GroceryReceive::with(['grocery', 'requisitionItem.requisition'])
                ->latest()
                ->paginate(15);

        return view('requisitions.receives.index', compact('receives'));
    }

    public function storeReceive(Request $request) {
        $request->validate([
            'requisition_item_id' => 'required|exists:grocery_requisition_items,id',
            'received_qty' => 'required|numeric|min:0.01',
            'purchase_date' => 'required|date',
            'expiry_date' => 'required|date|after:purchase_date',
            'lot_number' => 'nullable|string|max:191',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $reqItem = GroceryRequisitionItem::findOrFail($request->requisition_item_id);
                $grocery = Grocery::findOrFail($reqItem->grocery_id);

                // 1. Record the Receive
                GroceryReceive::create([
                    'user_id' => Auth::id(),
                    'grocery_requisition_item_id' => $reqItem->id,
                    'grocery_id' => $grocery->id,
                    'received_qty' => $request->received_qty,
                    'current_stock' => $grocery->qty,
                    'purchase_date' => $request->purchase_date,
                    'expiry_date' => $request->expiry_date,
                    'lot_number' => $request->lot_number,
                ]);

                // 2. Update progress
                $reqItem->increment('qty_received', $request->received_qty);

                // 3. Update Inventory
                $grocery->increment('qty', $request->received_qty);

                // 4. Update Status
                $this->updateRequisitionStatus($reqItem->grocery_requisition_id);
            });

            return redirect()->route('receives.index')
                            ->with('success', 'Items received and stock updated.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to receive items: ' . $e->getMessage());
        }
    }

    private function updateRequisitionStatus($requisitionId) {
        $requisition = GroceryRequisition::with('items')->find($requisitionId);
        $totalRequested = $requisition->items->sum('qty_requested');
        $totalReceived = $requisition->items->sum('qty_received');

        if ($totalReceived >= $totalRequested) {
            $requisition->update(['status' => 'completed']);
        } elseif ($totalReceived > 0) {
            $requisition->update(['status' => 'partial']);
        }
    }

// List all Issue history
    public function indexIssue() {
        $issues = GroceryIssue::with(['grocery', 'user'])
                ->latest()
                ->paginate(15);

        return view('requisitions.issues.index', compact('issues'));
    }

    public function storeIssue(Request $request) {
        $request->validate([
            'grocery_id' => 'required|exists:groceries,id',
            'issued_qty' => 'required|numeric|min:0.01',
            'issue_date' => 'required|date',
        ]);

        $grocery = Grocery::findOrFail($request->grocery_id);

        // Check if we have enough stock
        if ($grocery->qty < $request->issued_qty) {
            return back()->with('error', 'Insufficient stock! Current stock: ' . $grocery->qty);
        }

        return DB::transaction(function () use ($request, $grocery) {
                    // 1. Record the Issue
                    GroceryIssue::create([
                        'grocery_id' => $grocery->id,
                        'user_id' => Auth::id(),
                        'issued_qty' => $request->issued_qty,
                        'issue_date' => $request->issue_date,
                        'issued_to' => $request->issued_to,
                    ]);

                    // 2. Decrease Master Inventory (Inventory Out)
                    $grocery->decrement('qty', $request->issued_qty);

                    return redirect()->route('requisitions.issues.index')->with('success', 'Items issued successfully.');
                });
    }

    public function createReceive($requisition_id = null) {
        // Start the query
        $pendingItems = GroceryRequisitionItem::with(['grocery', 'requisition'])
                ->whereHas('requisition', function ($q) {
                    // Usually, you only receive items from 'approved' or 'partial' requisitions
                    $q->whereIn('status', ['pending', 'partial']);
                })
                ->whereRaw('qty_received < qty_requested')

                // Improvement: Conditionally filter if $requisition_id is present
                ->when($requisition_id, function ($query) use ($requisition_id) {
                    return $query->where('grocery_requisition_id', $requisition_id);
                })
                ->get();

        return view('requisitions.receives.create', compact('pendingItems', 'requisition_id'));
    }

    // Method for the Issuing Form
    public function createIssue() {
        // Fetch all groceries with their current stock levels
        $groceries = Grocery::all();
        return view('requisitions.issues.create', compact('groceries'));
    }
}
