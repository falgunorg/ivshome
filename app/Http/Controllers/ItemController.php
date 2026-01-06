<?php

namespace App\Http\Controllers;

use App\Category;
use App\Item;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class ItemController extends Controller {

    public function __construct() {
        $this->middleware('role:admin,staff');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $category = Category::orderBy('name', 'ASC')
                ->get()
                ->pluck('name', 'id');

        $producs = Item::all();
        return view('items.index', compact('category'));
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
        $category = Category::orderBy('name', 'ASC')
                ->get()
                ->pluck('name', 'id');

        $this->validate($request, [
            'name' => 'required|string',
            'description' => 'nullable',
            'condition' => 'nullable',
            'location' => 'nullable',
            'price' => 'nullable',
            'qty' => 'required',
            'image' => 'nullable',
            'category_id' => 'required',
        ]);

        $input = $request->all();
        $input['image'] = null;

        if ($request->hasFile('image')) {
            $input['image'] = '/upload/items/' . Str::slug($input['name'], '-') . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('/upload/items/'), $input['image']);
        }

        Item::create($input);

        return response()->json([
                    'success' => true,
                    'message' => 'Items Created'
        ]);
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
        $category = Category::orderBy('name', 'ASC')
                ->get()
                ->pluck('name', 'id');
        $item = Item::find($id);
        return $item;
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
            'name' => 'required|string',
            'description' => 'nullable',
            'condition' => 'nullable',
            'location' => 'nullable',
            'price' => 'nullable',
            'qty' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'category_id' => 'required',
        ]);

        $item = Item::findOrFail($id);

        // Do NOT overwrite image accidentally
        $input = $request->except('image');

        if ($request->hasFile('image')) {

            // ✅ Delete old image correctly
            if ($item->image && file_exists(public_path($item->image))) {
                unlink(public_path($item->image));
            }

            // ✅ Generate new image name
            $fileName = Str::slug($request->name)
                    . '-' . time()
                    . '.' . $request->image->getClientOriginalExtension();

            // ✅ Move new image
            $request->image->move(public_path('upload/items'), $fileName);

            // ✅ Save correct DB path
            $input['image'] = 'upload/items/' . $fileName;
        }

        $item->update($input);

        return response()->json([
                    'success' => true,
                    'message' => 'Item Updated Successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $item = Item::findOrFail($id);

        if (!$item->image == NULL) {
            unlink(public_path($item->image));
        }

        Item::destroy($id);

        return response()->json([
                    'success' => true,
                    'message' => 'Items Deleted'
        ]);
    }

    public function apiItems() {
        $item = Item::all();

        return Datatables::of($item)
                        ->addColumn('category_name', function ($item) {
                            return $item->category->name;
                        })
                        ->addColumn('show_photo', function ($item) {
                            if ($item->image == NULL) {
                                return 'No Image';
                            }
                            return '<img class="rounded-square" width="50" height="50" src="' . url($item->image) . '" alt="">';
                        })
                        ->addColumn('action', function ($item) {
                            return'<a onclick="editForm(' . $item->id . ')" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i> Edit</a> ' .
                                    '<a onclick="deleteData(' . $item->id . ')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
                        })
                        ->rawColumns(['category_name', 'show_photo', 'action'])->make(true);
    }
}
