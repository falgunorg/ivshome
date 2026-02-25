<?php

namespace App\Http\Controllers;

use App\Grocery;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use OpenAI\Laravel\Facades\OpenAI;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Auth;

class GroceryController extends Controller {

    public function index() {
        return view('groceries.index');
    }

    // DataTables API for Groceries
    public function apiGroceries() {
        $groceries = Grocery::query();
        return DataTables::of($groceries)
                        ->addColumn('show_photo', function ($g) {
                            $url = asset($g->image ?? 'assets/img/no-image.png');
                            return '<img src="' . $url . '" class="img-thumbnail" width="50">';
                        })
                        ->addColumn('action', function ($g) {
                            return '<a onclick="editForm(' . $g->id . ')" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i></a> ' .
                                    '<a onclick="deleteData(' . $g->id . ')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></a>';
                        })
                        ->rawColumns(['show_photo', 'action'])->make(true);
    }

    public function store(Request $request) {
        $data = $request->all();
        $data['user_id'] = Auth::id();
        if ($request->hasFile('image')) {
            $data['image'] = '/upload/groceries/' . time() . '.' . $request->image->extension();
            $request->image->move(public_path('upload/groceries'), $data['image']);
        }
        Grocery::create($data);
        return response()->json(['success' => true, 'message' => 'Grocery Added']);
    }

    // AI Logic: What menu do you prefer today?
    public function suggestMenuORIGIN() {
        $stock = Grocery::where('qty', '>', 0)->get(['name', 'qty', 'unit']);
        $pantryList = $stock->map(fn($g) => "{$g->qty} {$g->unit} of {$g->name}")->implode(', ');

        $result = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a master chef. Create 3 recipes. Return ONLY JSON with a "recipes" array containing "name" and "instructions".'],
                ['role' => 'user', 'content' => "I have: $pantryList. What can I cook?"],
            ],
            'response_format' => ['type' => 'json_object'],
        ]);

        $recipes = json_decode($result->choices[0]->message->content, true)['recipes'];
        return view('groceries.ai_results', compact('recipes'));
    }

    public function suggestMenu() {
        $stock = Grocery::where('qty', '>', 0)->get(['name', 'qty', 'unit']);
        $pantryList = $stock->map(fn($g) => "{$g->qty} {$g->unit} of {$g->name}")->implode(', ');

        // 1. Use the current stable model
        $result = Gemini::generativeModel(model: 'gemini-2.5-flash')
                ->generateContent("I have: $pantryList. 
            Suggest 5 recipes. 
            Return with Bengali Language.
            Return ONLY a valid JSON object with a 'recipes' array. 
            Each recipe needs 'name' and 'instructions'. 
            No conversational text, no markdown backticks.");

        $responseBody = $result->text();

        // 2. Clean the response (removes ```json ... ``` if the AI adds it)
        $cleanJson = trim(str_replace(['```json', '```'], '', $responseBody));
        $data = json_decode($cleanJson, true);

        // 3. Fallback if JSON is broken
        if (!$data || !isset($data['recipes'])) {
            $recipes = [
                ['name' => 'Simple Sauté', 'instructions' => 'Mix your available ingredients in a pan with oil and spices.']
            ];
        } else {
            $recipes = $data['recipes'];
        }


        return view('groceries.ai_results', compact('recipes'));
    }

    public function edit($id) {
        $grocery = Grocery::find($id);
        return $grocery;
    }

    public function update(Request $request, $id) {
        $grocery = Grocery::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($grocery->image && file_exists(public_path($grocery->image))) {
                unlink(public_path($grocery->image));
            }
            $data['image'] = '/upload/groceries/' . time() . '.' . $request->image->extension();
            $request->image->move(public_path('upload/groceries'), $data['image']);
        }

        $grocery->update($data);
        return response()->json(['success' => true, 'message' => 'Grocery Updated']);
    }

    public function destroy($id) {
        $grocery = Grocery::findOrFail($id);
        if ($grocery->image && file_exists(public_path($grocery->image))) {
            unlink(public_path($grocery->image));
        }
        $grocery->delete();
        return response()->json(['success' => true, 'message' => 'Grocery Deleted']);
    }
}
