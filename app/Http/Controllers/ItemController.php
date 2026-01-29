<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Item;
use App\Models\StockMovement;

class ItemController extends Controller
{
    function index()
    {   
        $items = Item::all();
        return Inertia::render('Inventory', [
            'items' => $items]);
    }

    function store(Request $request)
{
    \Log::info('ItemController store called', ['request' => $request->all()]);

    
    $validated = $request->validate([
        'items' => 'required|array', 
        'items.*.name' => 'required|string|max:255',
        'items.*.measurement_unit' => 'required|string|max:50',
        'items.*.quantity' => 'required|numeric|min:0',
        'items.*.image' => 'nullable|image|max:2048',
    ]);

    \Log::info('Validation passed', ['validated' => $validated]);

    
    foreach ($request->items as $itemData) {

        $imagePath = null;

       
        if (isset($itemData['image'])) {
            
            $imagePath = $itemData['image']->store('items', 'public');
        }

        $item = Item::create([
            'name' => $itemData['name'],
            'measurement_unit' => $itemData['measurement_unit'],
            'quantity' => $itemData['quantity'],
            'image_path' => $imagePath, 
        ]);

        \Log::info('Item created', ['item' => $item]);
    }

    
    return redirect()->route('inventory')->with('success', 'Stock updated successfully!');
}

    public function updateStock(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:items,id',
            'items.*.adjustment' => 'required|numeric|min:0',
            'items.*.operation' => 'required|in:+,-',
        ]);

        foreach ($validated['items'] as $itemData) {
            $item = Item::find($itemData['id']);
            $previousQuantity = $item->quantity;

            if ($itemData['operation'] === '+') {
                $item->quantity += $itemData['adjustment'];
            } elseif ($itemData['operation'] === '-') {
                $newQuantity = $item->quantity - $itemData['adjustment'];
                if ($newQuantity < 0) {
                    continue; 
                }
                $item->quantity = $newQuantity;
            }

            $item->save();

            
            StockMovement::create([
                'item_id' => $item->id,
                'user_id' => auth()->id(),
                'operation' => $itemData['operation'],
                'quantity' => $itemData['adjustment'],
                'previous_quantity' => $previousQuantity,
                'new_quantity' => $item->quantity,
                'notes' => 'Bulk stock update',
            ]);
        }

        return redirect()->route('inventory')->with('success', 'Stock updated successfully!');
    }
}

