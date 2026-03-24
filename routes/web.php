<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;

Route::get('/', function () {
    return view('search');
});

Route::get('/search', [SearchController::class, 'searchRoom']);

