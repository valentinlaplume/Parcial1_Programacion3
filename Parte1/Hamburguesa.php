<?php

class Hamburguesa {
    public $id;
    public $nombre;
    public $precio;
    public $tipo;
    public $cantidad;

    public function __construct ($id, $nombre, $precio, $tipo, $cantidad){
        $this->id =  ($id < 1) ? self::ReadInJsonIdMax() : intval($id);
        $this->nombre = strtolower($nombre);
        $this->precio = is_numeric($precio) && $precio > -1 ? floatval($precio) : 0.00;
        $this->tipo = strtolower($tipo);
        $this->cantidad = is_numeric($cantidad) && $cantidad > -1 ? intval($cantidad) : 0;
    }

    /* Retorna formato file imagen solicitado 
    - Completar el alta con imagen de la hamburguesa, guardando la imagen con el tipo y el nombre como
    identificación en la carpeta /ImagenesDeHamburguesas.
    */
    public function GetFileName(){
        if(!is_null($this)){
            $fileName = $this->tipo .'_'. $this->nombre;
            $fileName = str_replace(" ", "_", $fileName);
            return  $fileName;
        }

        echo '¡Ocurrió un Error en function GetFileName!: this es NULL. Verifique!!'. PHP_EOL;
        return NULL;
    }

    ///////////////////////////////////////////////////////////////  UPDATE
    /* Agrega o Actualiza array pasado como parametro dependiendo 
    * si existe o no en el array
    * Retorna array actualizado */
    public function UpdateArray($arr = array()){
        if (!is_null($this) && !is_null($arr) &&is_array($arr)){
            if (!$this->ExistsInArray($arr)) {
                array_push($arr, $this);
                echo "Ingresado.",PHP_EOL;
            }else{
                foreach ($arr as $objValue) {
                    if ($objValue->__Equals($this)) {
                        $objValue->cantidad += intval($this->cantidad); // suma
                        $objValue->precio = intval($this->precio); // actualiza
                        echo "Actualizado.",PHP_EOL;
                        break;
                    }
                }
            }
        }
        return $arr;
    }

    /* Actualiza array pasado como parametro dependiendo 
    * si existe o no en el array,
    * descuenta el stock
    * Retorna array con array actualizado 
    * y resultado de operacion: TRUE si ok, FALSE si no hay stock o error */
    static public function UpdateArrayByVenta($arr = array(), $Hamburguesa){
        $return = false;
        if (is_array($arr) && count($arr) > 0 && $Hamburguesa->VerifyNombreTipo($arr))
        {
            foreach ($arr as $objValue) 
            {
                if ($Hamburguesa->__Equals($objValue)) 
                {
                    if($objValue->cantidad - $Hamburguesa->cantidad < 0){ 
                        echo 'No hay stock por cantidad solicitada.', PHP_EOL;
                        break;
                    }

                    $objValue->cantidad -= intval($Hamburguesa->cantidad); // resta
                    echo "Actualizado stock Hamburguesa.", PHP_EOL;
                    self::SaveInJson($arr);
                    $return = true;
                    break;
                }
            }
        }
        return array($arr, $return);
    }

     /////////////////////////////////////////////////////////////// VERIFICACIONES
     /* Compara si son iguales entre this y el objeto pasado por parametro 
     * Retorna true si se encuentra en array, * false si no se encuentra. */
    public function __Equals($obj){
        try {
            if(!is_null($obj)) { 
                if(($this->nombre == $obj->nombre && $this->tipo == $obj->tipo)){ 
                    return true; 
                }
            }
            return false; 
        } catch (Exception $e) {
            echo "¡Ocurrió un Error!: ".  $e->getMessage() . PHP_EOL;
        }
    }
    
    /* Verifica si this ya existe en array pasado por parametro 
    * Retorna true si se encuentra en array, * false si no se encuentra. */
    public function ExistsInArray($arr){
        try {
            if(!is_null($this) && !is_null($arr) && is_array($arr) && count($arr) > 0) { 
                foreach ($arr as $value) {
                    if($this->__Equals($value)){ 
                        return true; 
                    }
                }
            }
            return false; 
        } catch (Exception $e) {
            echo "¡Ocurrió un Error!: ".  $e->getMessage() . PHP_EOL;
        }
    }

    public function VerifyNombreTipo($arr){
        try {
            if(!is_null($this) && !is_null($arr) && is_array($arr) && count($arr)>0) { 
                $flagTipo = 0;
                $flagnombre = 0;
                foreach ($arr as $value) {
                    if($this->__Equals($value) 
                       && $value->cantidad > 0){ 
                        $flagTipo = 1; 
                        $flagnombre = 1;
                        echo 'Si hay.' . PHP_EOL; 
                        return true;
                    }
                    if($this->nombre == $value->nombre){ $flagnombre = 1; }
                    if($this->tipo == $value->tipo){ $flagTipo = 1; }
                }

                if($flagTipo == 0){echo 'No existe del tipo ' . $this->tipo . PHP_EOL;}
                if($flagnombre == 0){echo 'No existe nombre ' . $this->nombre . PHP_EOL;}
            }
            return false; 
        } catch (Exception $e) {
            echo "¡Ocurrió un Error!: ".  $e->getMessage() . PHP_EOL;
        }
    }
    /////////////////////////////////////////////////////////////// MANEJO EN JSON
    static public function SaveInJson($arr = null, $fileName = "Hamburguesas.json"){
        try {
            $r = false;
            if (!is_null($arr) && is_array($arr) && count($arr) > 0) {
                $file = fopen($fileName, 'w');
                if ($file) {
                    $json = json_encode($arr, JSON_PRETTY_PRINT);
                    $r = fwrite($file, $json);
                    fclose($file);
                    if($r != false) { echo 'Se guardó en archivo '. $fileName . PHP_EOL; }
                }
            }
            return ($r == false) ? false : true;
        } catch (Exception $e) {
            echo "¡Ocurrió un Error!: ".  $e->getMessage() . PHP_EOL;
        }
    } 

    static public function ReadInJson($fileName = "Hamburguesas.json"){
        $arrayObj = array();
        try 
        {
            if(file_exists($fileName)){
                $file = fopen($fileName, 'r');
                if($file){
                    $arr = array();
                    $json = fread($file, filesize($fileName));
                    $arr = json_decode($json, true); 
                    foreach ($arr as $obj) {
                        $objNew = new Hamburguesa(
                                            $obj["id"],
                                            $obj["nombre"],
                                            $obj["precio"],
                                            $obj["tipo"],
                                            $obj["cantidad"]
                                        );
                        if(!is_null($objNew)){ array_push($arrayObj, $objNew); }
                    }
                    fclose($file);
                }
            }
        } catch (Exception $e) {
            echo "¡Ocurrió un Error!: ".  $e->getMessage() . PHP_EOL;
        }finally{
            return $arrayObj;
        }
    } 

    static private function ReadInJsonIdMax($fileName = "Hamburguesas.json"){
        $arrayObj = array();
        try 
        {
            if(file_exists($fileName)){
                $file = fopen($fileName, 'r');
                if($file){
                    $arr = array();
                    $json = fread($file, filesize($fileName));
                    $arr = json_decode($json, true); 
                    foreach ($arr as $obj) {
                        array_push($arrayObj, intval($obj["id"])); 
                    }
                    fclose($file);
                }
            }
        } catch (Exception $e) {
            echo "¡Ocurrió un Error!: ".  $e->getMessage() . PHP_EOL;
        }finally{
            return count($arrayObj) < 1 ? 1 : (max($arrayObj)+1);
        }
    } 


}

?>
    