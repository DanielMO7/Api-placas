<?php

namespace App\Http\Controllers;
use App\Models\DatosPlaca;

use Illuminate\Http\Request;

class DatosPlacaController extends Controller
{
    public function DatosPlaca(Request $request)
    {
        $objeto_consulta = DatosPlaca::datos_placa($request->placa);

        return json_encode($objeto_consulta);
    }
}
