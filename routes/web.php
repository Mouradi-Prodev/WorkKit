<?php

use App\Models\Team;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ProfileController;

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
});


Route::middleware(['auth','verified','can:view,App\Models\Team'])->group(function () {
    Route::get('/team',[TeamController::class, 'index'])->name('team.index');
});


Route::resource('chat', ChatController::class)->only([
    'index', 'show','store'
])->middleware(['auth','verified']);

Route::get('chat/messages/{id}',[ChatController::class, 'getMessages'])
->name('chat.getMessages')->middleware(['auth','verified']);
Route::post('chatroom',[ChatController::class, 'createChatRoom'])
->name('chatroom.create')->middleware(['auth','verified']);

require __DIR__.'/auth.php';
