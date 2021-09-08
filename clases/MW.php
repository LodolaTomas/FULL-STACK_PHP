<?php

use Firebase\JWT\JWT;

class MW
{
    public function VerificarNull($request, $response, $next)
    {
        $user = json_decode($request->getParam("user"));
        $usuario = json_decode($request->getParam("usuario"));
        $std = new stdClass();
        if ($user != null) {
            if (isset($user->clave) && isset($user->correo)) {
                $retorno = $next($request, $response);
            } else {
                if (isset($user->clave) == true && isset($user->correo) == false) {
                    $std->mensaje = "No Existe Correo";
                } else if (isset($user->clave) == false && isset($user->correo) == true) {
                    $std->mesaje = "No Existe Clave";
                }
                $retorno = $response->withJson($std, 403);
            }
        } else if ($usuario != null) {
            if (isset($usuario->clave) && isset($usuario->correo)) {
                $retorno = $next($request, $response);
            } else {
                if (isset($usuario->clave) == true && isset($usuario->correo) == false) {
                    $std->mensaje = "No Existe Correo";
                } else if (isset($usuario->clave) == false && isset($usuario->correo) == true) {
                    $std->mesaje = "No Existe Clave";
                }
                $retorno = $response->withJson($std, 403);
            }
        } else {
            $std->mensaje = "No Mandaste nada Capo";
            $retorno = $response->withJson($std, 403);
        }
        return $retorno;
    }

    public static function VerificarVacio($request, $response, $next)
    {
        $user = json_decode($request->getParam("user"));
        $usuario = json_decode($request->getParam("usuario"));
        $std = new stdClass();
        if ($user != null) {
            if ($user->clave != "" && $user->correo != "") {
                $retorno = $next($request, $response);
            } else {
                if ($user->clave != "" && $user->correo == "") {
                    $std->mensaje = "Correo Vacio";
                } else if ($user->clave == "" && $user->correo != "") {
                    $std->mesaje = "Clave Vacio";
                }
                $retorno = $response->withJson($std, 409);
            }
        } else if ($usuario != null) {
            if ($usuario->clave != "" && $usuario->correo != "") {
                $retorno = $next($request, $response);
            } else {
                if ($usuario->clave != "" && $usuario->correo == "") {
                    $std->mensaje = "Correo Vacio";
                } else if ($usuario->clave == "" && $usuario->correo != "") {
                    $std->mesaje = "Clave Vacio";
                }
                $retorno = $response->withJson($std, 409);
            }
        } else {
            $std->mensaje = "No Mandaste nada Capo";
            $retorno = $response->withJson($std, 403);
        }
        return $retorno;
    }

    ///Verifica que el Correo y Clave exista en la Base de Datos
    public function VerificarDB($request, $response, $next)
    {
        $user = json_decode($request->getParam("user"));
        $std = new stdClass();
        if ($user != null) {
            if (Usuario::Existe($user->correo, $user->clave)) {
                $retorno = $next($request, $response);
            } else {
                $std->mensaje = "Correo y Clave Inexistentes";
                $retorno = $response->withJson($std, 409);
            }
        } else {
            $std->mensaje = "No mandaste nada Capo";
            $retorno = $response->withJson($std, 409);
        }
        return $retorno;
    }

    ///Verfica que el correo no Exista en la Base de datos
    public static function VerificarCorreo($request, $response, $next)
    {
        $usuario = json_decode($request->getParam("usuario"));
        $std = new stdClass();
        if ($usuario != null) {
            $resp = Usuario::ExisteCorreo($usuario->correo);
            if ($resp->exito) {
                $std->mensaje = "Correo Existente";
                $retorno = $response->withJson($std, 409);
            } else {
                $retorno = $next($request, $response);
            }
        } else {
            $std->mensaje = "No mandaste nada Capo";
            $retorno = $response->withJson($std, 409);
        }
        return $retorno;
    }


   

}
