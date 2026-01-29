<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\StockMovement;
use Inertia\Inertia;


class HistoryController extends Controller
{
    function index()
    {   
        $history = StockMovement::join('items', 'stock_movements.item_id', '=', 'items.id')
        ->select(
            'stock_movements.*',        
            'items.name as item_name',  
        )
        ->get();
        return Inertia::render('History', [
            'history' =>$history]);
    }

}
