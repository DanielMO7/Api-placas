<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class DatosPlaca extends Model
{
    use HasFactory;

    public static function datos_placa()
    {
        try {
            $sql = 'SELECT * FROM servitek_vehiculo';
            $consulta = DB::connection()->select(DB::raw($sql));

            return [
                "status" => 1,
                "msg" => "Lista Usuarios",
                "data" => $consulta
            ];
        } catch (Throwable $e) {
            return "Error en database" . $e;
        }
    }
}
