<?php

namespace App\Http\Controllers;
use App\Models\DatosPlaca;

use Illuminate\Http\Request;

class DatosPlacaController extends Controller
{
    public function DatosPlaca(Request $request)
    {
        $objeto_consulta = DatosPlaca::datos_placa($request->placa);

        return response()->json([
            'data' => $objeto_consulta,
        ]);
    }
}
