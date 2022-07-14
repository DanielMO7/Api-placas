<?php

namespace App\Models;

use Facade\FlareClient\Stacktrace\Stacktrace;
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
            $sql_cambio_llanta = "SELECT servitek_vehiculo.numero_placa,
	
                DATE(servitek_cambio_llanta.fecha_cambio_llanta) AS fecha_cambio, 
                servitek_cambio_llanta.tipo_producto, 
                FORMAT(servitek_cambio_llanta.kilometraje_actual, 'N', 'de-de') AS kilometraje_actual, 
                FORMAT(servitek_cambio_llanta.kilometraje_cambio_sugerido, 'N', 'de-de') AS kilometraje_cambio_sugerido,
                tipo_servicio.servicio AS tipo_servicio
                FROM
                servitek_vehiculo
                LEFT JOIN servitek_cambio_llanta ON servitek_vehiculo.id_vehiculo = servitek_cambio_llanta.id_vehiculo
                LEFT JOIN tipo_servicio ON servitek_cambio_llanta.tipo_servicio = tipo_servicio.id_tipo_servicio
                WHERE servitek_vehiculo.numero_placa = ?";

            $consulta = DB::connection()->select(DB::raw($sql_cambio_llanta), [$placa]);
            $datos_correctos_aceite = true;
            foreach ($consulta as $dato){
                if ($dato->tipo_producto == null){
                    $datos_correctos_aceite = false;
                }
            }

            $sql_cambio_aceite = "SELECT servitek_vehiculo.numero_placa,
	
                DATE(servitek_cambio_llanta.fecha_cambio_llanta) AS fecha_cambio, 
                servitek_marca_llanta.marca_llanta AS tipo_producto,
                tipo_servicio.servicio AS tipo_servicio,
                FORMAT(servitek_cambio_llanta.kilometraje_actual, 'N', 'de-de') AS kilometraje_actual, 
                FORMAT(servitek_cambio_llanta.kilometraje_cambio_sugerido, 'N', 'de-de') AS kilometraje_cambio_sugerido
                FROM
                servitek_vehiculo
                LEFT JOIN servitek_cambio_llanta ON servitek_vehiculo.id_vehiculo = servitek_cambio_llanta.id_vehiculo
                LEFT JOIN tipo_servicio ON servitek_cambio_llanta.tipo_servicio = tipo_servicio.id_tipo_servicio
                LEFT JOIN servitek_marca_llanta ON servitek_cambio_llanta.id_marca_llanta = servitek_marca_llanta.id_marca_llanta
                WHERE servitek_vehiculo.numero_placa =   ?";

            $consulta2 = DB::connection()->select(DB::raw($sql_cambio_aceite), [$placa]);
            $datos_correctos_llanta = true;
            foreach ($consulta2 as $dato){
                if ($dato->tipo_producto == null){
                    $datos_correctos_llanta = false;
                }
            }
            if($datos_correctos_llanta and $datos_correctos_aceite){
                $datos = array_merge($consulta, $consulta2);
            }elseif($datos_correctos_llanta and !$datos_correctos_aceite){
                $datos = $consulta2;
            }else{
                $datos = $consulta;
            }
            return $datos;
        } catch (Throwable $e) {
            return 'Error en database' . $e;
        }
    }

    public static function insertar_datos_placa($request){
        $sql_insertar_datos_placa = '';

    }
}
