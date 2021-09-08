<?php

use Composer\barbijoload\ClassLoader;
use FastRoute\RouteParser\Std;

class Barbijo
{

    public function Alta($request, $response)
    {
        $paramObj = json_decode($request->getParam("barbijo"));
        $std = new stdClass();
        if ($paramObj != null) {
            if (Barbijo::AltaDB($paramObj)) {
                $std->exito = true;
                $std->mensaje = "Agregado Correctamente";
                $retorno = $response->withJson($std, 200);
            } else {
                $std->exito = false;
                $std->mensaje = "No pudo ser Agregado!";
                $retorno = $response->withJson($std, 418);
            }
        } else {
            $std->mensaje = "No mandaste nada";
            $retorno = $response->withJson($std, 418);
        }
        return $retorno;
    }

    public static function AltaDB($obj)
    {
        $objBD = AccesoDatos::DameUnObjetoAcceso();
        $consulta = $objBD->RetornarConsulta("INSERT INTO barbijos ( color, tipo, precio)VALUES(?,?,?)");
        return $consulta->execute([$obj->color, $obj->tipo, $obj->precio]);
    }

    public function Traer($request, $response)
    {
        $list = Barbijo::TraerDB();
        $std = new stdClass();
        $std->exito = $list->exito;
        $id = $request->getParam("id_barbijo");
        $encargado = $request->getAttribute('encargado');
        $propietario = $request->getAttribute('propietario');
        $empleado = $request->getAttribute('empleado');
        if ($list->exito) {
            $std->lista = $list->lista;
            $std->mensaje = "Datos Obtenidos Correctamente";
            if ($propietario == true) {
                if ($id == null) {
                    $std->lista = $list->lista;
                } else {
                    $listaid = array_column($list->lista, 'id');
                    $buscarbarbijo = array_search($id, $listaid);
                    if ($buscarbarbijo != false) {
                        $std->lista = $list->lista[$buscarbarbijo];
                    } else {
                        $std->lista = "ID no Encontrado";
                    }
                }
            }
            if ($encargado == true) {
                $std->lista = array_map(function ($item) {
                    $item = (array)$item;
                    unset($item["id"]);
                    return $item;
                }, $list->lista);
            }
            if ($empleado == true) {
                $colores = array_column($list->lista, "color");
                $std->lista = array_count_values($colores);
            }
            $retorno = $response->withJson($std, 200);
        } else {
            $std->mensaje = "Datos No Obtenidos";
            $retorno = $response->withJson($std, 424);
        }

        return $retorno;
    }
    public static function TraerDB()
    {
        $std = new stdClass();
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM barbijos");
        $std->exito = $consulta->execute();
        $std->lista = $consulta->fetchAll(PDO::FETCH_CLASS, "barbijo");
        return $std;
    }

    public static function Borrar($request, $response)
    {

        $array =  $request->getParsedBody();
        $std = new stdClass();
        if ($array != null) {
            $id = $array["id_barbijo"];
            $std->exito = Barbijo::BorrarDB($id);
            if ($std->exito) {
                $std->mensaje = "Borrado Exitosamente";
                $respuesta = $response->withJson($std, 200);
            } else {
                $std->mensaje = "No Pudo Ser Borrado";
                $respuesta = $response->withJson($std, 418);
            }
        } else {
            $std->mensaje = "No Mandaste nada";
            $respuesta = $response->withJson($std, 418);
        }
        return $respuesta;
    }


    public static function BorrarDB($id)
    {
        $objBD = AccesoDatos::DameUnObjetoAcceso();
        $consulta = $objBD->RetornarConsulta("DELETE FROM barbijos WHERE id=?");
        $consulta->execute([$id]);
        $consulta->rowCount() > 0 ? $retorno = true : $retorno = false;
        return $retorno;
    }
    public static function Modificar($request, $response)
    {
        $barbijo = json_decode($request->getParam("barbijo"));
        $std = new stdClass();
        if ($barbijo != null) {
            $std->exito = Barbijo::ModificarDB($barbijo);
            if ($std->exito) {
                $std->mensaje = "Modificado Exitosamente";
                $respuesta = $response->withJson($std, 200);
            } else {
                $std->mensaje = "No Pudo Ser Modificado";
                $respuesta = $response->withJson($std, 418);
            }
        } else {
            $std->mensaje = "No Mandaste nada";
            $respuesta = $response->withJson($std, 418);
        }
        return $respuesta;
    }


    public static function ModificarDB($obj)
    {
        $objBD = AccesoDatos::DameUnObjetoAcceso();
        $consulta = $objBD->RetornarConsulta("UPDATE barbijos SET color= ?, tipo= ?,precio= ? WHERE id=?");
        return $consulta->execute([$obj->color, $obj->tipo, $obj->precio, $obj->id]);
    }
}
