<?php

namespace App\Http\Controllers;

use App\Damage;
use App\Item;
use App\Customer;
use App\Exports\ExportDamage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use PDF;

class DamageController extends Controller {

    public function __construct() {
        $this->middleware('role:admin,staff');
    }

    /**
     * Display a listing of the resource.
     */
    public function index() {
        // Use get() instead of pluck() to keep all column data
        $items = Item::orderBy('name', 'ASC')->where('status', 'approved')->get(['id', 'name', 'serial_number']);

        $invoice_data = Damage::latest()->get();

        return view('damages.index', compact('items', 'invoice_data'));
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request) {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'qty' => 'required|integer|min:1',
            'date' => 'required|date',
            'image' => 'nullable|image|max:2048',
        ]);

        $item = Item::findOrFail($request->item_id);

        // 1. Check stock availability
        if ($item->qty < $request->qty) {
            return response()->json([
                        'success' => false,
                        'message' => 'Insufficient stock.'
                            ], 422);
        }

        $data = $request->only(['item_id', 'qty', 'date', 'remarks']);
        $data['user_id'] = Auth::id();

        // Default status is 'pending' (from migration), 
        // but we can be explicit here for clarity:
        $data['status'] = 'pending';

        // 2. Handle Image Upload
        if ($request->hasFile('image')) {
            $filename = \Str::uuid() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('upload/evidence'), $filename);
            $data['image'] = 'upload/evidence/' . $filename;
        }

        // 3. Create the record
        $damage = Damage::create($data);

        // 4. Admin Auto-Approval Logic
        if (auth()->user()->role == 'admin') {
            // Decrease item stock immediately for Admin
            $item->decrement('qty', $request->qty);

            // Update status to approved
            $damage->status = 'approved'; // Fixed syntax (property, not method)
            $damage->save();

            $msg = 'Damage record created and stock updated.';
        } else {
            $msg = 'Damage request submitted for Admin approval.';
        }

        return response()->json([
                    'success' => true,
                    'message' => $msg
        ]);
    }

    /**
     * Edit data (AJAX)
     */
    public function edit($id) {
        return Damage::findOrFail($id);
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, $id) {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'qty' => 'required|integer|min:1',
            'date' => 'required|date',
        ]);

        $damage = Damage::findOrFail($id);
        $item = Item::findOrFail($request->item_id);

        // check again
        if ($item->qty < $request->qty) {
            return response()->json([
                        'success' => false,
                        'message' => 'Insufficient stock.'
                            ], 422);
        }

        $damage->update($request->only(['item_id', 'qty', 'date', 'remarks']));

        return response()->json([
                    'success' => true,
                    'message' => 'Damage record updated successfully.'
        ]);
    }

    /**
     * Remove the specified resource.
     */
    public function destroy($id) {
        $damage = Damage::findOrFail($id);

        if ($damage->status == 'approved') {
            // restore stock on delete
            $damage->item->increment('qty', $damage->qty);
        }
        $damage->delete();

        return response()->json([
                    'success' => true,
                    'message' => 'Damage record deleted successfully.'
        ]);
    }

    /**
     * Datatable API
     */
    public function apiDamages() {
        $damages = Damage::with('item', 'user')->latest();

        return DataTables::of($damages)
                        ->addColumn('item_name', fn($d) => $d->item->name ?? '-')
                        ->addColumn('user_name', fn($d) => $d->user->name ?? '-')
                        ->addColumn('action', function ($d) {
                            $buttons = '';

                            // Condition: Must be the owner AND status must not be 'approved'
                            if ($d->user_id == auth()->id() && $d->status !== 'approved') {
                                $buttons .= '
                    <button onclick="editForm(' . $d->id . ')" class="btn btn-primary btn-xs">Edit</button>
                    <button onclick="deleteData(' . $d->id . ')" class="btn btn-danger btn-xs">Delete</button>
                ';
                            }

                            // Return buttons or a placeholder if conditions aren't met
                            return $buttons ?: '<span class="badge badge-secondary">No Actions</span>';
                        })
                        ->rawColumns(['action'])
                        ->make(true);
    }

    /**
     * Export all damages PDF
     */
    public function exportDamageAll() {
        $damage = Damage::with('item', 'user')->get();
        $pdf = PDF::loadView('damages.damageAllPDF', compact('damage'));
        return $pdf->download('damages.pdf');
    }

    /**
     * Export single damage PDF
     */
    public function exportDamage($id) {
        $damage = Damage::with('item', 'user')->findOrFail($id);
        $pdf = PDF::loadView('damages.damagePDF', compact('damage'));
        return $pdf->download($damage->id . '_damage.pdf');
    }

    /**
     * Export Excel
     */
    public function exportExcel() {
        return (new ExportDamage)->download('damages.xlsx');
    }
}
