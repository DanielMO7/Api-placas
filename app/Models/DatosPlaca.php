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
        // El try evalua si hay algun error en la consulta en general.
        try {

            // Buscamos si el id de la placa ya se encuentra registrada.
            $sql_validacion_placa = "SELECT COUNT(*) AS numero_registros FROM servitek_vehiculo WHERE servitek_vehiculo.numero_placa = ?";

            $validacion_placa = DB::connection()->select(DB::raw($sql_validacion_placa), [$request->placa]);

            //Como la validacion retorna un objeto, lo recorremos con un foreach y sacamos el contenido del count.
            foreach ($validacion_placa as $dato) {
                $contenido_db = $dato->numero_registros;
            }
            // Validamos si la palca esta registrada, si no es asi se crea un nuevo registro.
            if ($contenido_db > 0) {
                // Como el usuario ya esta registrado creamos un nuevo cambio en la database.
                try {
                    //Traemos el id del usuario.
                    $sql_traer_id = "SELECT servitek_vehiculo.id_vehiculo FROM servitek_vehiculo WHERE servitek_vehiculo.numero_placa = ?";
                    $obj_id_placa = DB::connection()->select(DB::raw($sql_traer_id), [$request->placa]);

                    foreach ($obj_id_placa as $datos) {
                        $id_placa = $datos->id_vehiculo;
                    }
                    
                    // Validamos el tipo de cambio, si es de aceite o de llanta.
                    if($request->tipo_cambio == 1){
                        $sql_insertar_cambio = "INSERT 
                                INTO servitek_cambio_aceite (
                                    id_vehiculo, 
                                    id_marca_aceite, 
                                    tipo_servicio, 
                                    kilometraje_actual, 
                                    kilometraje_cambio_sugerido,
                                    fecha_registro,
                                    fecha_cambio_aceite
                                    )
                                VALUES (?,?,?,?,?,?,?)";
    
                        // El kilometraje recomendado kilometraje actual mas 6000 km.
                        $kilometraje_cambio_sugerido = $request->kilometraje + 6000;
    
                        // Creamos una fecha de registro.
                        $date = date_create();
                        $cadena_fecha_actual = date_format($date, 'Y-m-d H:i:s');
    
                        $inyeccion_aceite = DB::connection()->select(DB::raw($sql_insertar_cambio), [
                            $id_placa,
                            $request->marca_aceite,
                            $request->tipo_cambio,
                            $request->kilometraje,
                            $kilometraje_cambio_sugerido,
                            $cadena_fecha_actual, $cadena_fecha_actual
                        ]);
                    }else{
                        $sql_insertar_cambio = "INSERT 
                                INTO servitek_cambio_llanta (
                                    id_vehiculo, 
                                    id_marca_llanta, 
                                    tipo_servicio, 
                                    kilometraje_actual, 
                                    kilometraje_cambio_sugerido,
                                    fecha_registro,
                                    fecha_cambio_llanta
                                    )
                                VALUES (?,?,?,?,?,?,?)";
    
                        // El kilometraje recomendado kilometraje actual mas 6000 km.
                        $kilometraje_cambio_sugerido = $request->kilometraje + 6000;
    
                        // Creamos una fecha de registro.
                        $date = date_create();
                        $cadena_fecha_actual = date_format($date, 'Y-m-d H:i:s');
    
                        $inyeccion_llanta = DB::connection()->select(DB::raw($sql_insertar_cambio), [
                            $id_placa,
                            $request->marca_llanta,
                            $request->tipo_cambio,
                            $request->kilometraje,
                            $kilometraje_cambio_sugerido,
                            $cadena_fecha_actual, $cadena_fecha_actual
                        ]);
                    }

                    return response()->json([
                        "status" => 1,
                        "msg" => "El cambio ha sido registrado correctamente.",
                    ]);
                } catch (Throwable $e) {
                    return 'Error al buscar id e insertar placa ' . $e;
                };
            } else {
                //Se cre un nuevo vehiculo y se insertan los datos.
                try {
                        // Insertamos un nuevo vehiculo.
                        $sql_insertar_vehiculo = "INSERT INTO servitek_vehiculo (numero_placa, tipo_vehiculo) 
                            VALUES (?, ?)";

                        $registro = DB::connection()->select(DB::raw($sql_insertar_vehiculo), [$request->placa, $request->tipo_vehiculo]);

                        // Buscamos el id del vehiculo.                        
                        $sql_traer_id = "SELECT servitek_vehiculo.id_vehiculo FROM servitek_vehiculo WHERE servitek_vehiculo.numero_placa = ?";

                        $obj_id_placa = DB::connection()->select(DB::raw($sql_traer_id), [$request->placa]);

                        foreach ($obj_id_placa as $datos) {
                            $id_placa = $datos->id_vehiculo;
                        }  

                        if($request->tipo_cambio == 1){
                            $sql_insertar_cambio = "INSERT 
                                INTO servitek_cambio_aceite (
                                    id_vehiculo, 
                                    id_marca_aceite, 
                                    tipo_servicio, 
                                    kilometraje_actual, 
                                    kilometraje_cambio_sugerido,
                                    fecha_registro,
                                    fecha_cambio_aceite
                                    )
                                VALUES (?,?,?,?,?,?,?)";
    
                            $kilometraje_cambio_sugerido = $request->kilometraje + 6000;
    
                            $date = date_create();
                            $cadena_fecha_actual = date_format($date, 'Y-m-d H:i:s');
    
                            $inyeccion_aceite = DB::connection()->select(DB::raw($sql_insertar_cambio), [
                                $id_placa,
                                $request->marca_aceite,
                                $request->tipo_cambio,
                                $request->kilometraje,
                                $kilometraje_cambio_sugerido,
                                $cadena_fecha_actual, $cadena_fecha_actual
                            ]);
                        }else{
                            $sql_insertar_cambio = "INSERT 
                                INTO servitek_cambio_llanta (
                                    id_vehiculo, 
                                    id_marca_llanta, 
                                    tipo_servicio, 
                                    kilometraje_actual, 
                                    kilometraje_cambio_sugerido,
                                    fecha_registro,
                                    fecha_cambio_llanta
                                    )
                                VALUES (?,?,?,?,?,?,?)";
    
                            $kilometraje_cambio_sugerido = $request->kilometraje + 6000;
    
                            $date = date_create();
                            $cadena_fecha_actual = date_format($date, 'Y-m-d H:i:s');
    
                            $inyeccion_llanta = DB::connection()->select(DB::raw($sql_insertar_cambio), [
                                $id_placa,
                                $request->marca_llanta,
                                $request->tipo_cambio,
                                $request->kilometraje,
                                $kilometraje_cambio_sugerido,
                                $cadena_fecha_actual, $cadena_fecha_actual
                            ]);

                        }

                        return response()->json([
                            "status" => 1,
                            "msg" => "Se creo el usuario y se registro la placa",
                        ]);
                } catch (Throwable $e) {
                    return 'Error al crear e insertar el validacion  ' . $e;
                }
            };
        } catch (Throwable $e) {
            return 'Error en validacion' . $e;
        }
    }

    public static function insertar_datos_placa_llanta($request)
    {
        return 'modelo de cambio de llanta.';
    }
}
