<?php

use App\Http\Controllers\AppropraitionController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ObjectExpenditureController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Models\Appropriation;
use App\Models\ObjectExpenditure;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/', function () {
    return Inertia::render('Dashboard', [
        'total-budget' => ObjectExpenditure::query()->sum('budget'),
        'balance' => ObjectExpenditure::query()->sum('balance')
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('users', UserController::class);
    Route::resource('employees', EmployeeController::class);
    Route::resource('utilities', BillController::class);
    Route::resource('expenditures', ObjectExpenditureController::class);
    Route::resource('appropriations', AppropraitionController::class);
});

Route::get('test', function () {
    return request()->ip();
});

// Route::get('test', function () {
//     return Appropriation::query()->with(['user', 'bill', 'objectExpenditure'])->get();
// });
require __DIR__ . '/auth.php';
