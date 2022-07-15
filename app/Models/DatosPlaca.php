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
            servitek_marca_llanta.marca_llanta AS tipo_producto,
            tipo_servicio.servicio AS tipo_servicio,
            FORMAT(servitek_cambio_llanta.kilometraje_actual, 'N', 'de-de') AS kilometraje_actual, 
            FORMAT(servitek_cambio_llanta.kilometraje_cambio_sugerido, 'N', 'de-de') AS kilometraje_cambio_sugerido
            FROM
            servitek_vehiculo
            LEFT JOIN servitek_cambio_llanta ON servitek_vehiculo.id_vehiculo = servitek_cambio_llanta.id_vehiculo
            LEFT JOIN tipo_servicio ON servitek_cambio_llanta.tipo_servicio = tipo_servicio.id_tipo_servicio
            LEFT JOIN servitek_marca_llanta ON servitek_cambio_llanta.id_marca_llanta = servitek_marca_llanta.id_marca_llanta
            WHERE servitek_vehiculo.numero_placa = ?";

            $consulta = DB::connection()->select(DB::raw($sql_cambio_llanta), [$placa]);
            $datos_correctos_aceite = true;
            foreach ($consulta as $dato) {
                if ($dato->tipo_producto == null) {
                    $datos_correctos_aceite = false;
                }
            }

            $sql_cambio_aceite = "SELECT servitek_vehiculo.numero_placa,
	
            DATE(servitek_cambio_aceite.fecha_cambio_aceite) AS fecha_cambio, 
            servitek_marca_aceite.nombre_aceite AS tipo_producto,
            tipo_servicio.servicio AS tipo_servicio,
            FORMAT(servitek_cambio_aceite.kilometraje_actual, 'N', 'de-de') AS kilometraje_actual, 
            FORMAT(servitek_cambio_aceite.kilometraje_cambio_sugerido, 'N', 'de-de') AS kilometraje_cambio_sugerido
            FROM
            servitek_vehiculo
            LEFT JOIN servitek_cambio_aceite ON servitek_vehiculo.id_vehiculo = servitek_cambio_aceite.id_vehiculo
            LEFT JOIN tipo_servicio ON servitek_cambio_aceite.tipo_servicio = tipo_servicio.id_tipo_servicio
            LEFT JOIN servitek_marca_aceite ON servitek_cambio_aceite.id_marca_aceite = servitek_marca_aceite.id_marca_aceite
            WHERE servitek_vehiculo.numero_placa = ?";

            $consulta2 = DB::connection()->select(DB::raw($sql_cambio_aceite), [$placa]);
            $datos_correctos_llanta = true;
            foreach ($consulta2 as $dato) {
                if ($dato->tipo_producto == null) {
                    $datos_correctos_llanta = false;
                }
            }
            if ($datos_correctos_llanta and $datos_correctos_aceite) {
                $datos = array_merge($consulta, $consulta2);
            } elseif ($datos_correctos_llanta and !$datos_correctos_aceite) {
                $datos = $consulta2;
            } else {
                $datos = $consulta;
            }
            return $datos;
        } catch (Throwable $e) {
            return 'Error en database' . $e;
        }
    }

    public static function insertar_datos_placa_aceite($request)
    {
       
            try{
                $sql_validacion_placa = "SELECT COUNT(id_vehiculo) FROM servitek_vehiculo WHERE servitek_vehiculo.numero_placa = ?";

                $validacion_placa = DB::connection()->select(DB::raw($sql_validacion_placa), [$request->placa]);

                echo " ", count($validacion_placa)," ", $request->placa;
                /*
                if( count($validacion_placa) > 0){
                    return 'Esta placa ya se encuentra registrada';
                }else{
                    $sql_insertar_vehiculo = 'INSERT INTO servitek_vehiculo (numero_placa, tipo_vehiculo) 
                        VALUES (?, ?)';

                    $registro = DB::connection()->select(DB::raw($sql_insertar_vehiculo), [$request->placa, $request->tipo_vehiculo]);
                    
                    return response()->json([
                    "status" => 0,
                    "msg" => "La placa se encuentra registrada.",
                    "data" => $registro,
                ]);
                }*/

            }catch (Throwable $e) {
                return 'Error en validacion';
            }

            /*
                
            }*/

            /*$sql_insertar_datos_placa = 'INSERT INTO servitek_vehiculo(numero_placa, tipo_vehiculo) 
            VALUES  (?,?,?)';*/
        
    }
    /*
    public static function insertar_datos_placa_llanta($request)
    {
        try {
            $sql_insertar_datos_placa = 'INSERT INTO usuarios (nombre_usuario,email,documento,contrasena,rol)
            VALUES (:nombre_usuario,:email,:documento,:contrasena,:rol)';
        } catch (Throwable $e) {
            return 'Error en database' . $e;
        }
    }*/
}
