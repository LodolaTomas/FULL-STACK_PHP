<?php

use Firebase\JWT\JWT;

class Login
{
    public static function VerificarLogin($request, $response)
    {
        $user = json_decode($request->getParam("user"));
        $std = new stdClass();
        $validacion = Usuario::Validar($user->correo, $user->clave);
        $std->exito = $validacion->exito;
        if ($validacion->exito) {
            $std->jwt = Autentificadora::CrearJWT($validacion->obj);
            $retorno = $response->withJson($std, 200);
        } else {
            $std->jwt = null;
            $retorno = $response->withJson($std, 403);
        }
        return $retorno;
    }

    public static function VerificarToken($request, $response)
    {
        $token = $request->getHeader("token");
        $std = new stdClass();
        if (!empty($token)) {
            $std = Autentificadora::VerificarJWT($token[0]);
            if ($std->verificado) {
                $retorno = $response->withJson($std, 200);
            } else {
                $retorno = $response->withJson($std, 403);
            }
        } else {
            $std->mensaje = "No Mandaste Nada";
            $retorno = $response->withJson($std, 403);
        }
        return $retorno;
    }
}
