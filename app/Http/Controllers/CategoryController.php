<?php

namespace App\Http\Controllers;

use App\Category;
use App\Exports\ExportCategories;
use Illuminate\Http\Request;
use Yajra\DataTables\Datatables;
use PDF;

class CategoryController extends Controller {

    public function __construct() {
        $this->middleware('role:admin,staff');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $categories = Category::with('items')->get();
        return view('categories.index');
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
            'name' => 'required|string|min:2|unique:categories,name',
        ]);

        Category::create($request->all());

        return response()->json([
                    'success' => true,
                    'message' => 'Categories Created'
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
        $category = Category::find($id);
        return $category;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        // 1. Add unique validation but IGNORE this specific $id
        $this->validate($request, [
            'name' => 'required|string|min:2|unique:categories,name,' . $id
        ]);

        // 2. Find the existing category
        $category = Category::findOrFail($id);

        // 3. Update with the validated data
        $category->update($request->all());

        return response()->json([
                    'success' => true,
                    'message' => 'Category Updated' // Changed from "Categories Update"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $category = Category::findOrFail($id);

        // 1. Check if there are any items associated with this category
        // This assumes you have a 'items' relationship in your Category Model
        if ($category->items()->count() > 0) {
            return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete! This category is currently being used by ' . $category->items()->count() . ' items.'
                            ], 422); // We send 422 (Unprocessable Entity) to trigger an error in AJAX
        }

        // 2. If no items exist, proceed with deletion
        $category->delete();

        return response()->json([
                    'success' => true,
                    'message' => 'Category Deleted'
        ]);
    }

    public function apiCategories() {
        // withCount('items') adds a 'items_count' attribute to each category
        $categories = Category::withCount('items')->get();

        return Datatables::of($categories)
                        ->addColumn('action', function ($categories) {
                            return '<a onclick="editForm(' . $categories->id . ')" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i> Edit</a> ' .
                                    '<a onclick="deleteData(' . $categories->id . ')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
                        })
                        ->rawColumns(['action'])
                        ->make(true);
    }

    public function exportCategoriesAll() {
        $categories = Category::all();
        $pdf = PDF::loadView('categories.CategoriesAllPDF', compact('categories'));
        return $pdf->download('categories.pdf');
    }

    public function exportExcel() {
        return (new ExportCategories())->download('categories.xlsx');
    }
}
