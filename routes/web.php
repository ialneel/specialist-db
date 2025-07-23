<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SpecialistFormController;
use Illuminate\Support\Facades\DB; //temp
use App\Http\Controllers\AdminController;

// Public welcome page
Route::get('/', function () {
    return view('welcome');
});

// Dashboard (only logged in users)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes for all authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes for submitting the form (available to all logged in users)
    Route::get('/form', [SpecialistFormController::class, 'create'])->name('form.create');
    Route::post('/form', [SpecialistFormController::class, 'store'])->name('form.store');
});

//Admin-only routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/forms', [SpecialistFormController::class, 'index'])->name('forms.index');
    Route::get('/forms/export', [SpecialistFormController::class, 'export'])->name('forms.export');
    Route::post('/forms/bulk', [SpecialistFormController::class, 'bulkAction'])->name('forms.bulk');
    Route::get('/admin', [AdminController::class, 'index']);
});

Route::get('/db-check', function () {
    try {
        DB::connection()->getPdo();
        return '✅ Connected to SQL Server DB!';
    } catch (\Exception $e) {
        return '❌ DB Connection Failed: ' . $e->getMessage();
    }
});
Route::get('/healthz', function () {
    return response()->json([
        'status' => 'ok',
        'app'    => config('app.name'),
        'time'   => now()->toISOString(),
    ]);
});


require __DIR__.'/auth.php';
