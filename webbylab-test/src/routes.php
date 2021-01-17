<?php

use Core\Router\Route;

Route::get('/', Controllers\IndexController::class);

Route::get('/film', Controllers\FilmController::class);
Route::post('/film/add', [Controllers\FilmController::class, 'add']);
Route::post('/film/delete', [Controllers\FilmController::class, 'delete']);

Route::post('/import', [Controllers\ImportController::class, 'import']);
