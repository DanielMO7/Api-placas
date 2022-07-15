<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatosPlacaController;

Route::post('/datos-placa', [DatosPlacaController::class, 'DatosPlaca']);
Route::post('/registrar-placa', [DatosPlacaController::class, 'InsertarDatosPLaca']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
