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
            $sql = "SELECT servitek_vehiculo.numero_placa,
	
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

            $consulta = DB::connection()->select(DB::raw($sql), [$placa]);

            $sql2 = "SELECT servitek_vehiculo.numero_placa,

            DATE(servitek_cambio_aceite.fecha_cambio_aceite) AS fecha_cambio, 
            servitek_cambio_aceite.tipo_producto, 
            FORMAT(servitek_cambio_aceite.kilometraje_actual, 'N', 'de-de') AS kilometraje_actual, 
            FORMAT(servitek_cambio_aceite.kilometraje_cambio_sugerido, 'N', 'de-de') AS kilometraje_cambio_sugerido, 
            tipo_servicio.servicio AS tipo_servicio
        FROM
            servitek_vehiculo
            LEFT JOIN servitek_cambio_aceite ON servitek_vehiculo.id_vehiculo = servitek_cambio_aceite.id_vehiculo
            LEFT JOIN tipo_servicio ON servitek_cambio_aceite.tipo_servicio = tipo_servicio.id_tipo_servicio
            WHERE servitek_vehiculo.numero_placa = ?";

            $consulta2 = DB::connection()->select(DB::raw($sql2), [$placa]);
            
            $datos = array_merge($consulta, $consulta2);

            return $datos;
            /*return  [
                'r1'=> $consulta,
                'r2' => $consulta2
            ];*/
            //if(count($consulta) > 1 and count($consulta2) > 1){
                
            /*}
            elseif(count($consulta) == 1 and count($consulta2) > 0){
                return $consulta2;
            }
            elseif(count($consulta) > 0 and count($consulta2) == 1){
                return $consulta;
            }*/

        } catch (Throwable $e) {
            return 'Error en database' . $e;
        }
    }
}
