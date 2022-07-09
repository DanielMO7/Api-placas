<?php

namespace App\Http\Controllers;
use App\Models\DatosPlaca;

use Illuminate\Http\Request;

class DatosPlacaController extends Controller
{
    public function DatosPlaca(){
        $objeto_consulta = DatosPlaca::datos_placa();

        return response()->json([
            "data" => $objeto_consulta
        ]);

    }
}
