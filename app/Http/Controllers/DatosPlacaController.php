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
    public function InsertarDatosPLaca(Request $request)
    {
        if($request->tipo_cambio == 1){
            $objeto_consulta = DatosPlaca::insertar_datos_placa_aceite($request);

            return json_encode($objeto_consulta);

        }else{
            $objeto_consulta = DatosPlaca::insertar_datos_placa_llanta($request);
            
            return $objeto_consulta;
        }
    }
}
