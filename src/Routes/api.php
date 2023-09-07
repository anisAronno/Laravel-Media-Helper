<?php

use AnisAronno\MediaHelper\Http\Controllers\ImageController;

Route::resource('image', ImageController::class, ['except' => ['update']]);
Route::post('/image/update/{image}', [ImageController::class, 'update'])->name('image.update');
Route::post('image/delete-all', [ImageController::class, 'groupDelete'])->name('image.destroy.all');