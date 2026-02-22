<?php

namespace App\Http\Controllers;

use App\ItemType;
use Illuminate\Http\Request;
use Yajra\DataTables\Datatables;
use Illuminate\Support\Facades\Auth;

class ItemTypeController extends Controller {

    public function __construct() {
        $this->middleware('auth'); // Adjust roles as needed
    }

    public function index() {
        return view('item-types.index');
    }

    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required|string|min:2|unique:item_types,name',
        ]);

        ItemType::create($request->all());

        return response()->json([
                    'success' => true,
                    'message' => 'Item Type Created'
        ]);
    }

    public function show($id) {
        // Eager load items and their specific location details
        $itemType = ItemType::with(['items.itemLocation', 'items.cabinet', 'items.drawer'])->findOrFail($id);
        return view('item-types.show', compact('itemType'));
    }

    public function edit($id) {
        $itemType = ItemType::findOrFail($id);
        return $itemType;
    }

    public function update(Request $request, $id) {
        $this->validate($request, [
            'name' => 'required|string|min:2|unique:item_types,name,' . $id
        ]);

        $itemType = ItemType::findOrFail($id);
        $itemType->update($request->all());

        return response()->json([
                    'success' => true,
                    'message' => 'Item Type Updated'
        ]);
    }

    public function destroy($id) {
        $itemType = ItemType::findOrFail($id);
        // Add checks here if item_type is used in products before deleting
        $itemType->delete();

        return response()->json([
                    'success' => true,
                    'message' => 'Item Type Deleted'
        ]);
    }

    public function apiItemTypes() {
        $itemTypes = ItemType::all();

        return Datatables::of($itemTypes)
                        ->addColumn('action', function ($itemType) {
                            // Start with the Show button which everyone can see
                            $action = '<a href="' . route('item-types.show', $itemType->id) . '" class="btn btn-info btn-xs">' .
                                    '<i class="glyphicon glyphicon-eye-open"></i> Show</a> ';

                            // Logic check outside of the return string
                            if (auth()->user()->role == 'admin') {
                                // Append Edit Button
                                $action .= '<a onclick="editForm(' . $itemType->id . ')" class="btn btn-primary btn-xs">' .
                                        '<i class="glyphicon glyphicon-edit"></i> Edit</a> ';

                                // Append Delete Button
                                $action .= '<a onclick="deleteData(' . $itemType->id . ')" class="btn btn-danger btn-xs">' .
                                        '<i class="glyphicon glyphicon-trash"></i> Delete</a>';
                            }

                            return $action;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
    }
}
