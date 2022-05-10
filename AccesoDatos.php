<?php
class AccesoDatos
{
    private $host = 'localhost';
    private $db = 'parcial1';
    private $user = 'root'; 
    private $password = '';
    
    private static $objAD;
    private $objPDO;
 
    private function __construct()
    {
        try{ 
            // ****** chequear siempre el tema de las ';' que no sean ',' ******
            $conexionStr = 'mysql:host='.$this->host.';dbname='.$this->db.';charset=utf8';

            $this->objPDO = new PDO($conexionStr, $this->user, $this->password, 
            array(PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

            $this->objPDO->exec("SET CHARACTER SET utf8");
        } 
        catch (PDOException $e) { 
            echo "¡Ocurrió un Error!: ".  $e->getMessage() . PHP_EOL;
        }catch (Exception $e) { 
            echo "¡Ocurrió un Error!: ".  $e->getMessage() . PHP_EOL;
        }
    }
 
    public function GetQuery($sql)
    { 
        return $this->objPDO->prepare($sql); 
    }

    public function GetLastIdInserted()
    { 
        return $this->objPDO->lastInsertId(); 
    }
 
    public static function GetObjectAD()
    { 
        if (!isset(self::$objAD)) { self::$objAD = new AccesoDatos(); } 
        return self::$objAD;        
    }
 
 
    // Evita que el objeto se pueda clonar
    public function __clone()
    { 
        trigger_error('La clonación de este objeto no está permitida. ', E_USER_ERROR); 
    }
}
?>