<?php

namespace App\Http\Controllers;

use App\Recipe;
use Illuminate\Http\Request;
use Yajra\DataTables\Datatables;
use Illuminate\Support\Str;
use Storage;

class RecipeController extends Controller {

    public function __construct() {
        $this->middleware('role:admin,staff');
    }

    public function index() {
        return view('recipes.index');
    }

    public function store(Request $request) {
        $input = $request->all();

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // Save ONLY the filename
            $fileName = Str::slug($request->title) . '-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload/recipes'), $fileName);

            $input['image'] = $fileName;
        }

        Recipe::create($input);

        return response()->json([
                    'success' => true,
                    'message' => 'Recipe Created'
        ]);
    }

    public function update(Request $request, $id) {
        $recipe = Recipe::findOrFail($id);

        $this->validate($request, [
            'title' => 'required|string|min:2',
            'instructions' => 'required',
        ]);

        $input = $request->all();

        if ($request->hasFile('image')) {
            // 1. Delete old image: Since DB only has filename, we must prepend the path
            if ($recipe->image) {
                $oldImagePath = public_path('upload/recipes/' . $recipe->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // 2. Prepare new image
            $file = $request->file('image');
            $fileName = Str::slug($request->title) . '-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload/recipes'), $fileName);

            // 3. Save ONLY the filename
            $input['image'] = $fileName;
        } else {
            // No new image, keep the existing filename
            $input['image'] = $recipe->image;
        }

        $recipe->update($input);

        return response()->json([
                    'success' => true,
                    'message' => 'Recipe Updated'
        ]);
    }

    public function show($id) {
        $recipe = Recipe::findOrFail($id);
        return view('recipes.show', compact('recipe'));
    }

    public function edit($id) {
        // We need to fetch the recipe with its rich text content
        $recipe = Recipe::findOrFail($id);

        return response()->json([
                    'id' => $recipe->id,
                    'title' => $recipe->title,
                    'note' => $recipe->note,
                    'instructions' => $recipe->instructions,
        ]);
    }

    public function destroy($id) {
        // 1. Find the recipe or throw a 404 error
        $recipe = Recipe::findOrFail($id);

        // 2. Check if the image path is stored and the file exists on the disk
        if (!empty($recipe->image)) {
            // We use trim to ensure there are no leading/trailing slashes causing path issues
            $path = public_path(trim($recipe->image, '/'));

            if (is_file($path)) {
                unlink($path);
            }
        }

        // 3. Delete the database record
        $recipe->delete();

        return response()->json([
                    'success' => true,
                    'message' => 'Recipe and associated image deleted successfully'
        ]);
    }

    public function apiRecipes() {
        // Eager load the rich text relationship
        $recipes = Recipe::get();

        return Datatables::of($recipes)
                        ->addColumn('show_photo', function ($item) {
                            return '<img class="rounded-square" width="50" height="50" src="' . $item->show_photo . '" alt="">';
                        })
                        ->addColumn('action', function ($recipes) {
                            return '<a href="' . route('recipes.show', $recipes->id) . '" class="btn btn-info btn-xs">' .
                                    '<i class="glyphicon glyphicon-eye-open"></i> Show</a>  ' . '<a onclick="editForm(' . $recipes->id . ')" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i> Edit</a> ' .
                                    '<a onclick="deleteData(' . $recipes->id . ')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
                        })
                        ->rawColumns(['show_photo', 'action'])
                        ->make(true);
    }
}
