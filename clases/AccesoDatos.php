<?php

class AccesoDatos
{
    private static $_objetoAccesoDatos;
    private $_objetoPDO;
 
    //constructor que inicializa la conec
    private function __construct()
    {
        $basedatos="farmacia_bd";
        try {
            $usuario='root';
            $clave='';
            $this->_objetoPDO = new PDO('mysql:host=localhost;dbname='.$basedatos.';charset=utf8', $usuario, $clave);
        } catch (PDOException $e) {
             print "Error!!!<br/>" . $e->getMessage();
             die();
        }
    }
 
    //Realiza el prepare y lo revuelve con la consulta pasada
    public function RetornarConsulta($sql)
    {
        return $this->_objetoPDO->prepare($sql);
    }
 
    //Sirve para que le pases un nuevo PDO, fijandose que si ya hay uno, no se genere otro
    public static function DameUnObjetoAcceso()//singleton
    {
        if (!isset(self::$_objetoAccesoDatos)) {       
            self::$_objetoAccesoDatos = new AccesoDatos(); 
        }
 
        return self::$_objetoAccesoDatos;        
    }
 
    // Evita que el objeto se pueda clonar
    public function __clone()
    {
        trigger_error('La clonaci&oacute;n de este objeto no est&aacute; permitida!!!', E_USER_ERROR);
    }
}