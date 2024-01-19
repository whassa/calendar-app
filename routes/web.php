<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventsController;
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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/events', [EventsController::class, 'fetch'])->name('events.fetch');
    Route::post('/events', [EventsController::class, 'create'])->name('events.create');
    Route::delete('/events', [EventsController::class, 'delete'])->name('events.delete');
    Route::patch('/events', [EventsController::class, 'update'])->name('events.update');
    Route::get('/events/{id}', [EventsController::class, 'get'])->name('events.get');
});

require __DIR__.'/auth.php';
