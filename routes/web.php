<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\HistoryController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('inventory', [ItemController::class, 'index'])->middleware(['auth','verified'])->name('inventory'); 
Route::get('history', [HistoryController::class,'index'])->middleware(['auth','verified'])->name('history'); 

Route::get('add_item', function (){
    return Inertia::render('Add_Items');
})->middleware(['auth','verified'])->name('add_item');

Route::post('/add_item_db', [ItemController::class, 'store'])->middleware(['auth','verified'])->name('add_item_db');

Route::post('/update-stock', [ItemController::class, 'updateStock'])->middleware(['auth','verified'])->name('update_stock');

    

require __DIR__.'/settings.php';
