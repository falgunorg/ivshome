<?php

namespace App\Http\Controllers;

use App\Grocery;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;

class RecipeAIController extends Controller {

    public function index() {
        $groceries = Grocery::where('user_id', auth()->id())->get();
        return view('groceries.index', compact('groceries'));
    }

    public function generate(Request $request) {
        // 1. Get available stock
        $availableItems = Grocery::where('user_id', auth()->id())
                ->where('quantity', '>', 0)
                ->get();

        if ($availableItems->isEmpty()) {
            return back()->with('error', 'Your grocery list is empty!');
        }

        // 2. Prepare the prompt
        $pantryString = $availableItems->map(fn($item) => "{$item->quantity} {$item->unit} of {$item->item_name}")->implode(', ');

        $prompt = "I have these ingredients: {$pantryString}. 
                   Suggest 3 recipes I can cook. Provide the 'name' and 'instructions' for each. 
                   Format the output as a JSON object with a 'recipes' array.";

        // 3. Call AI
        $result = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful chef. Always respond in JSON.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'response_format' => ['type' => 'json_object'],
        ]);

        $data = json_decode($result->choices[0]->message->content, true);
        $recipes = $data['recipes'];

        return view('groceries.recipes', compact('recipes', 'availableItems'));
    }
}
