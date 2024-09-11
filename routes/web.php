<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DropdownController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RulesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StrokeController;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('/auth/login', [AuthController::class, 'postLogin']);
Route::get('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth'])->group(function () {
    //Home Controller
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::post('/reset-qty/{strokeId}', [HomeController::class, 'resetQty'])->name('reset.qty');


    //Dropdown Controller
     Route::get('/dropdown', [DropdownController::class, 'index'])->middleware(['checkRole:IT']);
     Route::post('/dropdown/store', [DropdownController::class, 'store'])->middleware(['checkRole:IT']);
     Route::patch('/dropdown/update/{id}', [DropdownController::class, 'update'])->middleware(['checkRole:IT']);
     Route::delete('/dropdown/delete/{id}', [DropdownController::class, 'delete'])->middleware(['checkRole:IT']);

     //Rules Controller
     Route::get('/rule', [RulesController::class, 'index'])->middleware(['checkRole:IT']);
     Route::post('/rule/store', [RulesController::class, 'store'])->middleware(['checkRole:IT']);
     Route::patch('/rule/update/{id}', [RulesController::class, 'update'])->middleware(['checkRole:IT']);
     Route::delete('/rule/delete/{id}', [RulesController::class, 'delete'])->middleware(['checkRole:IT']);

     //User Controller
     Route::get('/user', [UserController::class, 'index'])->middleware(['checkRole:IT']);
     Route::post('/user/store', [UserController::class, 'store'])->middleware(['checkRole:IT']);
     Route::post('/user/store-partner', [UserController::class, 'storePartner'])->middleware(['checkRole:IT']);
     Route::patch('/user/update/{user}', [UserController::class, 'update'])->middleware(['checkRole:IT']);
     Route::get('/user/revoke/{user}', [UserController::class, 'revoke'])->middleware(['checkRole:IT']);
     Route::get('/user/access/{user}', [UserController::class, 'access'])->middleware(['checkRole:IT']);

     Route::get('/inventory/ckd', [InventoryController::class, 'indexCKD'])->name('inventory.ckd');
     Route::get('/inventory/raw-material', [InventoryController::class, 'index'])->name('inventory.index');;
     Route::get('/inventory/{id}/details', [InventoryController::class, 'show'])->name('inventory.details');
     Route::post('/inventory/planned/upload', [InventoryController::class, 'uploadPlanned'])->name('inventory.planned.upload');
     Route::get('/download/excel/format/planned', [InventoryController::class, 'downloadPlannedTemplate'])->name('inventory.planned.template');
     Route::post('/inventory/planned/update', [InventoryController::class, 'updatePlannedReceive'])->name('inventory.planned.update');

     //Master Product
     Route::get('/master/product', [ProductController::class, 'index']);
    // Route for displaying the Stroke Dies master page
    Route::get('/master/stroke', [StrokeController::class, 'index'])->name('stroke.index');

    // Route for fetching data for DataTables via Ajax
    Route::get('/master/stroke-data', [StrokeController::class, 'getStrokeDiesData'])->name('stroke.dies.data');
    Route::get('/master/stroke/{id}/edit', [StrokeController::class, 'edit']);
    Route::put('/master/stroke/{id}', [StrokeController::class, 'update']);




    });
