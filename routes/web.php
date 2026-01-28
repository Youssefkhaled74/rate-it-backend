<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Proxy route for storage files when `public/storage` symlink is not available.
use App\Http\Controllers\PublicAssetController;
Route::get('storage-proxy/{path}', [PublicAssetController::class, 'storageProxy'])->where('path', '.*')->name('storage.proxy');

// Load admin panel routes (session-driven Blade dashboard)
// Ensure the `admin.guard` middleware runs before the admin auth middleware so
// authorization uses the `admin_web` guard (necessary for Gate::before to receive Admin).
Route::middleware(['web', 'admin.guard'])->group(function () {
    require __DIR__ . '/admin.php';
});
