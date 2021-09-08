<?php

use FastRoute\RouteParser\Std;

class Usuario
{
    public function Alta($request, $response)
    {
        $paramObj = json_decode($request->getParam("usuario"));
        $std = new stdClass();
        $uploadedFile = $request->getUploadedFiles();
        if (!empty($uploadedFile)) {
            $extension = explode(".", $uploadedFile['foto']->getClientFilename());
            $paramObj->foto = "";

            if (Usuario::AltaDB($paramObj)) {
                $id_db = Usuario::ObtenerID();
                $paramObj->id = intval($id_db->latsid);
                $path = $paramObj->correo . "_" . $paramObj->id . "." . $extension[1];
                $uploadedFile['foto']->moveTo("fotos/" . $path);
                $paramObj->foto = $path;
                if (Usuario::ModificarDB($paramObj)) {
                    $std->exito = true;
                    $std->mensaje = "Agregado Correctamente";
                    $retorno = $response->withJson($std, 200);
                }
            } else {
                $std->exito = true;
                $std->mensaje = "No pudo ser Agregado";
                $retorno = $response->withJson($std, 418);
            }
        } else {
            $std->mensaje = "No mandaste foto";
            $retorno = $response->withJson($std, 418);
        }

        return $retorno;
    }

    public static function AltaDB($obj)
    {
        $objBD = AccesoDatos::DameUnObjetoAcceso();
        $consulta = $objBD->RetornarConsulta("INSERT INTO usuarios ( correo, clave, nombre, apellido, perfil, foto)VALUES(?,?,?,?,?,?)");
        return $consulta->execute([$obj->correo, $obj->clave, $obj->nombre, $obj->apellido, $obj->perfil, $obj->foto]);
    }
    public static function ObtenerID()
    {
        $objBD = AccesoDatos::DameUnObjetoAcceso();
        $consulta = $objBD->RetornarConsulta("SELECT LAST_INSERT_ID() as latsid");
        $consulta->execute();
        return  $consulta->fetchobject();
    }

    public static function ModificarDB($obj)
    {
        $objBD = AccesoDatos::DameUnObjetoAcceso();
        $consulta = $objBD->RetornarConsulta("UPDATE usuarios SET foto= ? WHERE id=?");
        return $consulta->execute([$obj->foto, $obj->id]);
    }
    public function Traer($request, $response)
    {
        $list = Usuario::TraerDB();
        $std = new stdClass();
        $std->exito = $list->exito;
        if ($list->exito) {
            $std->lista = $list->lista;
            $std->mensaje = "Datos Obtenidos Correctamente";
            $apellido = $request->getParam("apellido");
            $encargado = $request->getAttribute('encargado');
            $propietario = $request->getAttribute('propietario');
            $empleado = $request->getAttribute('empleado');
            if ($propietario == true) {
                if ($apellido == null) {
                    $apellido = array_column($list->lista, "apellido");
                    $std->lista = array_count_values($apellido);
                } else {
                    $cantidad = Usuario::TraerUnoBD($apellido);
                    if ($cantidad != null) {
                        $std->lista = $apellido . ": " . $cantidad;
                    } else {
                        $std->mensaje = "Esa apellido no tiene un usuario existente";
                    }
                }
            }
            if ($encargado == true) {
                $std->lista = array_column($list->lista, "clave", "id");
            }
            if ($empleado == true) {

                $std->lista = array_map(function ($item) {
                    $item = (array)$item;
                    unset($item["id"]);
                    unset($item["correo"]);
                    unset($item["clave"]);
                    unset($item["perfil"]);
                    return $item;
                }, $list->lista);
            }
            $retorno = $response->withJson($std, 200);
        } else {
            $std->mensaje = "Datos No Obtenidos";
            $retorno = $response->withJson($std, 424);
        }

        return $retorno;
    }

    public static function TraerUnoBD($apellido)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM usuarios WHERE apellido=?");
        $consulta->execute([$apellido]);
        return $consulta->rowCount();
    }

    public static function TraerDB()
    {
        $std = new stdClass();
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM usuarios");
        $std->exito = $consulta->execute();
        $std->lista = $consulta->fetchAll(PDO::FETCH_CLASS, "Usuario");
        return $std;
    }

    public static function Validar($correo, $clave)
    {
        $std = new stdClass();
        $objetoAccesoDato = AccesoDatos::DameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM usuarios WHERE correo=? AND clave=?");
        $std->exito = $consulta->execute([$correo, $clave]);
        $std->obj = $consulta->fetchObject('Usuario');
        return $std;
    }

    public static function Existe($correo, $clave)
    {
        $objBD = AccesoDatos::DameUnObjetoAcceso();
        $consulta = $objBD->RetornarConsulta("SELECT * FROM usuarios WHERE correo=? AND clave=?");
        $consulta->execute([$correo, $clave]);
        $consulta->rowCount() > 0 ? $exito = true :  $exito = false;
        return $exito;
    }
    public static function ExisteCorreo($correo)
    {
        $objBD = AccesoDatos::DameUnObjetoAcceso();
        $consulta = $objBD->RetornarConsulta("SELECT * FROM usuarios WHERE correo=?");
        $consulta->execute([$correo]);
        $std = new stdClass();
        if ($consulta->rowCount() > 0) {
            $std->exito = true;
            $std->obj = $consulta->fetchObject("Usuario");
        } else {
            $std->exito = false;
        }
        return $std;
    }
}
