<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatosPlacaController;

Route::post('/datos-placa', [DatosPlacaController::class, 'DatosPlaca']);

Route::get('/', function () {
    return view('welcome');
});
