<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class DatosPlaca extends Model
{
    use HasFactory;

    public static function datos_placa($placa)
    {
        try {
            //Consulta Aceite
            $sql = "SELECT
	            servitek_vehiculo.numero_placa,
	
	            servitek_cambio_llanta.fecha_cambio_llanta, servitek_cambio_llanta.tipo_producto, servitek_cambio_llanta.kilometraje_actual, servitek_cambio_llanta.kilometraje_cambio_sugerido,
	            tipo_servicio.servicio AS tipo_servicio
            FROM
	            servitek_vehiculo
	            LEFT JOIN servitek_cambio_llanta ON servitek_vehiculo.id_vehiculo = servitek_cambio_llanta.id_vehiculo
	            LEFT JOIN tipo_servicio ON servitek_cambio_llanta.tipo_servicio = tipo_servicio.id_tipo_servicio
	            WHERE servitek_vehiculo.numero_placa = 'TES1' ";

            $consulta = DB::connection()->select(DB::raw($sql));

            $sql2 = "SELECT
	            servitek_vehiculo.numero_placa,
	
	            servitek_cambio_aceite.fecha_cambio_aceite, servitek_cambio_aceite.tipo_producto, servitek_cambio_aceite.kilometraje_actual, servitek_cambio_aceite.kilometraje_cambio_sugerido,
	            tipo_servicio.servicio AS tipo_servicio
            FROM
	            servitek_vehiculo
	            LEFT JOIN servitek_cambio_aceite ON servitek_vehiculo.id_vehiculo = servitek_cambio_aceite.id_vehiculo
	            LEFT JOIN tipo_servicio ON servitek_cambio_aceite.tipo_servicio = tipo_servicio.id_tipo_servicio
	            WHERE servitek_vehiculo.numero_placa =  'TES1'";

            $consulta2 = DB::connection()->select(DB::raw($sql2));

            return [
                'status' => 1,
                'placa' => $placa,
                'msg' => 'Lista Usuarios',
                'data' => $consulta,
                $consulta2,
            ];
        } catch (Throwable $e) {
            return 'Error en database' . $e;
        }
    }
}
