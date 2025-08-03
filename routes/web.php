<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuratMasukController;

Route::get('/', [SuratMasukController::class, 'index'])->name('surat_masuk.index'); // Rute untuk halaman utama
Route::post('/surat-masuk', [SuratMasukController::class, 'store'])->name('surat_masuk.store'); // Rute untuk menyimpan data surat
Route::get('/surat-masuk/{id}', [SuratMasukController::class, 'show'])->name('surat_masuk.show'); // Rute untuk melihat detail surat
Route::resource('surat-masuk', SuratMasukController::class);