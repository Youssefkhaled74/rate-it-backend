<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Proxy route for storage files when `public/storage` symlink is not available.
use App\Http\Controllers\PublicAssetController;
Route::get('storage-proxy/{path}', [PublicAssetController::class, 'storageProxy'])->where('path', '.*')->name('storage.proxy');

// Load admin panel routes (session-driven Blade dashboard)
require __DIR__ . '/admin.php';
