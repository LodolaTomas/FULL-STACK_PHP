<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Firebase\JWT\JWT;

require_once './vendor/autoload.php';

require_once './clases/AccesoDatos.php';
require_once './clases/Barbijo.php';
require_once './clases/Usuario.php';
require_once './clases/Login.php';
require_once './clases/MW.php';
require_once './clases/autentificadora.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$app = new \Slim\App(["settings" => $config]);
/**********************************************/

$app->post('/usuarios', \Usuario::class . ':Alta')->add(\MW::class.'::VerificarCorreo')->add(\MW::class.'::VerificarVacio')->add(\MW::class.':VerificarNull');

$app->get('[/]', \Usuario::class . ':Traer');

$app->post('[/]', \Barbijo::class . ':Alta');

$app->group('/barbijos', function () {
    $this->get('', \Barbijo::class . ':Traer');
});

$app->group('/login', function () {
    $this->post('', \Login::class . ':VerificarLogin')->add(\MW::class.':VerificarDB')->add(\MW::class.'::VerificarVacio')->add(\MW::class.':VerificarNull');
    $this->get('', \Login::class . ':VerificarToken');
});
/*  */
$app->delete('[/]', \Barbijo::class . ':Borrar');
/*  */
$app->put('[/]', \Barbijo::class . ':Modificar');

/**********************************************/

$app->run();
