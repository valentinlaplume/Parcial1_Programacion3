<?php

class Venta{
    public $id;
    public $mailUsuario;
    public $nombreHamburguesa;
    public $tipoHamburguesa;
    public $numero;
    public $cantidad;
    public $fechaAlta;

    public function __construct ($id = NULL, $mailUsuario = NULL, $nombreHamburguesa = NULL, $tipoHamburguesa = NULL, $numero = NULL, $cantidad = NULL, $fechaAlta = NULL)
    {
        $this->id = intval($id);
        $this->mailUsuario = $mailUsuario;
        $this->nombreHamburguesa = strtolower($nombreHamburguesa);
        $this->tipoHamburguesa = strtolower($tipoHamburguesa);
        $this->numero = is_numeric($numero) && $numero > -1 ? intval($numero) : 0;
        $this->cantidad = is_numeric($cantidad) && $cantidad > -1 ? intval($cantidad) : 0;
        $this->fechaAlta = $fechaAlta != '' || !is_null($fechaAlta) ? $fechaAlta : date('Y-m-d');
    }

    private function GetFechaAltaFileName(){
        $fecha = str_replace("-", "", $this->fechaAlta);
        $fecha = str_replace(" ", "_", $fecha);
        $fecha = str_replace(":", "", $fecha);
        $fecha = str_replace("pm", "", $fecha);
        $fecha = str_replace("am", "", $fecha);
        return $fecha;
    }

    /* Retorna formato file imagen solicitado */
    public function GetFileName(){
        if(!is_null($this)){
            $arr = explode('@', $this->mailUsuario);
            $mailRecortado = $arr[0];
            
            $fileName = $this->tipoHamburguesa 
            .'_'. $this->nombreHamburguesa 
            .'_'. $mailRecortado 
            .'_'. $this->GetFechaAltaFileName();
            
            return  $fileName;
        }

        echo '¡Ocurrió un Error en function GetFileName!: this es NULL. Verifique!!'. PHP_EOL;
        return NULL;
    }

    /* Obtiene el numero maximo de pedido de ventas */
    static public function GetLastNumeroPedidoVenta($objAD)
    {
        try
        {
            if(!is_null($objAD)){
                $queryIdMax = $objAD->GetQuery('SELECT MAX(numero) AS numeroPedidoMax FROM venta');
                $queryIdMax->execute();

                $result =  $queryIdMax->fetch(PDO::FETCH_ASSOC)['numeroPedidoMax'];
                
                return is_null($result) ? 1 : $result+1;
            }

            echo '¡Ocurrió un Error en function GetLastNumeroPedidoVenta!, objAD es NULL. Verifique!!' . PHP_EOL;
            return false;
        } 
        catch (PDOException $e) { 
            echo "¡Ocurrió un Error!: ".  $e->getMessage() . PHP_EOL;
        }catch (Exception $e) { 
            echo "¡Ocurrió un Error!: ".  $e->getMessage() . PHP_EOL;
        }
    }

    ///////////////////////////////////////////////////////////////////////////// CONVERTIR
    /* Convierte un array asociativo a un array de objeto Venta, retorna array */
    static private function ConvertArrayASSOC($arrASSOC){
        $arrayObj = array();
        if(!is_null($arrASSOC) && is_array($arrASSOC) && count($arrASSOC) > 0){
            foreach ($arrASSOC as $obj) {
                $objNew = new Venta(
                $obj["id"], 
                $obj["mailUsuario"], 
                $obj["nombreHamburguesa"], 
                $obj["tipoHamburguesa"], 
                $obj["numero"], 
                $obj["cantidad"],
                $obj["fechaAlta"]);

                if(!is_null($objNew)) { array_push($arrayObj, $objNew); }
            }
        }
        return $arrayObj;
    }


    //////////////////////////////////////////////////////////////////////////////// PRINT
    static public function PrintList($arr){
        if(!is_null($arr) && is_array($arr) && count($arr) > 0){
            echo '<ul>';
            foreach ($arr as $value) {
                // echo '<li>'. 'id: ' . $value->id .'</li>';
                echo '<li>'. 'Mail del Usuario: ' . $value->mailUsuario .'</li>';
                echo '<li>'. 'nombre de Hamburguesa: ' . $value->nombreHamburguesa .'</li>';
                echo '<li>'. 'Tipo de Hamburguesa: ' . $value->tipoHamburguesa .'</li>';
                echo '<li>'. 'Numero de Pedido: ' . $value->numero .'</li>';
                echo '<li>'. 'Cantidad Hamburguesas: ' . $value->cantidad .'</li>';
                echo '<li>'. 'Fecha de Alta: ' . $value->fechaAlta .'</li>';
                echo '<li>'. '----------------------------------------------' .'</li>';
                echo '<br>';
            }
            echo '</ul>';
        }
    }

    //////////////////////////////////////////////////////////////////////////////// INSERT
    public function InsertIntoDB($objAD)
    {
        try
        {
            if(!is_null($objAD) && !is_null($this)){
                $sql = 'INSERT INTO venta(
                mailUsuario
                ,nombreHamburguesa
                ,tipoHamburguesa
                ,numero
                ,cantidad
                ,fechaAlta
                ) VALUES (?,?,?,?,?,?)';
                
                $sentencia = $objAD->GetQuery($sql);
                
                $sentencia->bindValue(1, $this->mailUsuario, PDO::PARAM_STR);
                $sentencia->bindValue(2, $this->nombreHamburguesa, PDO::PARAM_STR);
                $sentencia->bindValue(3, $this->tipoHamburguesa, PDO::PARAM_STR);
                $sentencia->bindValue(4, $this->numero, PDO::PARAM_INT);
                $sentencia->bindValue(5, $this->cantidad, PDO::PARAM_INT);
                $sentencia->bindValue(6, $this->fechaAlta, PDO::PARAM_STR);

                $sentencia->execute();

                $ultimoIdInsertado = $objAD->GetLastIdInserted();
                
                // actualizo id Venta insertada
                if($ultimoIdInsertado > 0) { $this->id = $ultimoIdInsertado; } 
                
                return $ultimoIdInsertado;
            }

            echo '¡Ocurrió un Error en function InsertIntoDB!, objAD o this al menos uno es NULL. Verifique!!' . PHP_EOL;
            return false;
        } 
        catch (PDOException $e) { 
            echo "¡Ocurrió un Error!: ".  $e->getMessage() . PHP_EOL;
        }catch (Exception $e) { 
            echo "¡Ocurrió un Error!: ".  $e->getMessage() . PHP_EOL;
        }
    }


    ///////////////////////////////////////////////////////////////////////////// OBTENER
    /* Retorna cantidad de Hamburguesas vendidas */
    static public function CountHamburguesaSoldByDate($objAD, $date)
    {
        try
        {
            if(!is_null($objAD))
            {
                $queryIdMax = $objAD->GetQuery("SELECT SUM(cantidad) AS cantidadVentas FROM venta
                WHERE fechaAlta = '$date'");
                $queryIdMax->execute();

                $result =  $queryIdMax->fetch(PDO::FETCH_ASSOC)['cantidadVentas'];
                
                return is_null($result) ? 0 : $result;
            }
            
            echo '¡Ocurrió un Error en function CountHamburguesaSoldByDate!, objAD es NULL. Verifique!!' . PHP_EOL;
            return false;
        } 
        catch (PDOException $e) { 
            echo "¡Ocurrió un Error!: ".  $e->getMessage() . PHP_EOL;
        }catch (Exception $e) { 
            echo "¡Ocurrió un Error!: ".  $e->getMessage() . PHP_EOL;
        }
    }

    ///////////////////////////////////////////////////////////////////////////// CONSULTAS BY
    /*
    *  Retorna array de Ventas entre Fechas pasadas y con opcion de orden
    */
    static public function GetAllBetweenDatesAndOrderBy($objAD, $fechaDesde, $fechaHasta, $campoOrdenar = 'id')
    {
        try
        {
            if(!is_null($objAD) && !is_null($fechaDesde) && !is_null($fechaHasta))
            {
                $consulta = 
                "SELECT
                    id AS id,
                    mailUsuario AS mailUsuario,
                    nombreHamburguesa AS nombreHamburguesa,
                    tipoHamburguesa AS tipoHamburguesa,
                    numero AS numero,
                    cantidad AS cantidad,
                    fechaAlta AS fechaAlta
                FROM venta
                WHERE (fechaAlta >= '$fechaDesde') 
                AND (fechaAlta <= '$fechaHasta')
                ORDER BY $campoOrdenar";

                $query = $objAD->GetQuery($consulta);

                $query->execute();

                $arrASSOC = $query->fetchAll(PDO::FETCH_ASSOC);
                $arrOBJECT = self::ConvertArrayASSOC($arrASSOC);

                if(count($arrOBJECT) > 0) { 
                    $r = $arrOBJECT;
                }
                else{
                    echo 'function GetAllBetweenDatesAndOrderBy. No existen Ventas desde '. $fechaDesde . ' hasta ' .  $fechaHasta . PHP_EOL;
                    $r = NULL;
                } 

                return $r;
            }

            echo '¡Ocurrió un error en function GetAllBetweenDatesAndOrderBy! objAD, fechaDesde o fechaHasta al menos uno es NULL. Verifique!!'. PHP_EOL;
            return array();
        } 
        catch (PDOException $e) { 
            echo "¡Ocurrió un Error!: ".  $e->getMessage() . PHP_EOL;
        }catch (Exception $e) { 
            echo "¡Ocurrió un Error!: ".  $e->getMessage() . PHP_EOL;
        }
    }


    /* Obtiene un lista objetos si coincide campo y valor */
    public static function GetAllBy($objAD, $campo, $valor)
    {
        try
        { 
            if(!is_null($objAD) && !is_null($campo) && !is_null($valor))
            {
                if(is_numeric($valor)){ 
                    $consulta = 
                    "SELECT 
                        id AS id,
                        mailUsuario AS mailUsuario,
                        nombreHamburguesa AS nombreHamburguesa,
                        tipoHamburguesa AS tipoHamburguesa,
                        numero AS numero,
                        cantidad AS cantidad,
                        fechaAlta AS fechaAlta
                    FROM venta
                    WHERE $campo = $valor";

                    $query = $objAD->GetQuery($consulta);
                }
                else if (is_string($valor)){
                    $consulta = 
                    "SELECT 
                        id AS id,
                        mailUsuario AS mailUsuario,
                        nombreHamburguesa AS nombreHamburguesa,
                        tipoHamburguesa AS tipoHamburguesa,
                        numero AS numero,
                        cantidad AS cantidad,
                        fechaAlta AS fechaAlta
                    FROM venta
                    WHERE $campo = '$valor'";

                    $query = $objAD->GetQuery($consulta);
                }

                $query->execute();
                
                $arrASSOC = $query->fetchAll(PDO::FETCH_ASSOC);
                $arrOBJECT = self::ConvertArrayASSOC($arrASSOC);

                if(count($arrOBJECT) > 0) { 
                    $r = $arrOBJECT;
                }
                else{
                    echo 'function GetAllBy. No existen Ventas con '. $campo . ' = ' .  $valor . PHP_EOL;
                    $r = NULL;
                } 

                return $r;
            }

            echo '¡Ocurrió un error en function GetAllBy! objAD, campo o valor al menos uno es NULL. Verifique!!'. PHP_EOL;
            return NULL;
        } 
        catch (PDOException $e) { 
            echo "¡Ocurrió un Error!: ".  $e->getMessage() . PHP_EOL;
        }catch (Exception $e) { 
            echo "¡Ocurrió un Error!: ".  $e->getMessage() . PHP_EOL;
        }
    }

    /* Obtiene un objeto si coincide campo y valor */
    public static function GetBy($objAD, $campo, $valor)
    {
        try
        { 
            if(!is_null($objAD) && !is_null($campo) && !is_null($valor))
            {
                if(is_numeric($valor)){ 
                    $consulta = 
                    "SELECT 
                        id AS id,
                        mailUsuario AS mailUsuario,
                        nombreHamburguesa AS nombreHamburguesa,
                        tipoHamburguesa AS tipoHamburguesa,
                        numero AS numero,
                        cantidad AS cantidad,
                        fechaAlta AS fechaAlta
                    FROM venta
                    WHERE $campo = $valor";

                    $query = $objAD->GetQuery($consulta);
                }
                else if (is_string($valor)){
                    $consulta = 
                    "SELECT 
                        id AS id,
                        mailUsuario AS mailUsuario,
                        nombreHamburguesa AS nombreHamburguesa,
                        tipoHamburguesa AS tipoHamburguesa,
                        numero AS numero,
                        cantidad AS cantidad,
                        fechaAlta AS fechaAlta
                    FROM venta
                    WHERE $campo = '$valor'";

                    $query = $objAD->GetQuery($consulta);
                }

                $query->execute();
                
                $arrASSOC = $query->fetchAll(PDO::FETCH_ASSOC);
                $arrOBJECT = self::ConvertArrayASSOC($arrASSOC);

                if(count($arrOBJECT) == 1) { 
                    $r = $arrOBJECT[0];
                }
                else{
                    echo 'function GetBy. No existe Venta con '. $campo . ' = ' .  $valor . PHP_EOL;
                    $r = NULL;
                } 

                return $r;
            }

            echo '¡Ocurrió un error en function GetBy! objAD, campo o valor al menos uno es NULL. Verifique!!'. PHP_EOL;
            return NULL;
        } 
        catch (PDOException $e) { 
            echo "¡Ocurrió un Error!: ".  $e->getMessage() . PHP_EOL;
        }catch (Exception $e) { 
            echo "¡Ocurrió un Error!: ".  $e->getMessage() . PHP_EOL;
        }
    }

    ///////////////////////////////////////////////////////////////// UPDATE
    /* Actualiza un objeto si campo y valor son iguales, objUpdate objeto actualizado */
    public static function UpdateBy($objAD, $campo, $valor, $objUpdate)
    {
        try
        { 
            if(!is_null($objAD) && !is_null($campo) && !is_null($valor) && !is_null($objUpdate))
            {
                if(is_numeric($valor)){ 
                    $consulta = 
                    "UPDATE venta SET
                        mailUsuario = '$objUpdate->mailUsuario',
                        nombreHamburguesa = '$objUpdate->nombreHamburguesa',
                        tipoHamburguesa = '$objUpdate->tipoHamburguesa',
                        cantidad = $objUpdate->cantidad
                    WHERE $campo = $valor";
                }
                else if (is_string($valor)){
                    $consulta = 
                    "UPDATE venta SET 
                        mailUsuario = '$objUpdate->mailUsuario',
                        nombreHamburguesa = '$objUpdate->nombreHamburguesa',
                        tipoHamburguesa = '$objUpdate->tipoHamburguesa',
                        cantidad = $objUpdate->cantidad
                    WHERE $campo = '$valor'";
                }
                
                $query = $objAD->GetQuery($consulta);
                
                $query->execute();

                $actualizados = $query->rowCount();
                if($actualizados == 0) { 
                    echo 'function UpdateBy. No existen Ventas con '. $campo . ' = ' .  $valor . PHP_EOL;
                }
                else{
                    echo 'Cantidad de Ventas actualizadas: '. $actualizados . PHP_EOL;
                }

                return $actualizados;
            }

            echo '¡Ocurrió un error en function UpdateBy! objAD, campo, valor o objUpdate al menos uno es NULL. Verifique!!'. PHP_EOL;
            return NULL;
        } 
        catch (PDOException $e) { 
            echo "¡Ocurrió un Error!: ".  $e->getMessage() . PHP_EOL;
        }catch (Exception $e) { 
            echo "¡Ocurrió un Error!: ".  $e->getMessage() . PHP_EOL;
        }
    }

    public function UpdateFields($mailUsuario, $nombreHamburguesa, $tipoHamburguesa, $cantidad){
        $this->mailUsuario = $mailUsuario;
        $this->nombreHamburguesa = $nombreHamburguesa;
        $this->tipoHamburguesa = $tipoHamburguesa;
        $this->cantidad = $cantidad;

        return $this;
    }

        ///////////////////////////////////////////////////////////////// DELETE
    /* Actualiza un objeto si campo y valor son iguales, objUpdate objeto actualizado */
    public static function DeleteBy($objAD, $campo, $valor)
    {
        try
        { 
            if(!is_null($objAD) && !is_null($campo) && !is_null($valor))
            {
                if(is_numeric($valor)){ 
                    $consulta = 
                    "DELETE FROM venta WHERE $campo = $valor";
                }
                else if (is_string($valor)){
                    $consulta = 
                    "DELETE FROM venta WHERE $campo = '$valor'";
                }
                
                $query = $objAD->GetQuery($consulta);
                
                $query->execute();

                $eliminados = $query->rowCount();
                if($eliminados == 0) { 
                    echo 'function DeleteBy. No existen Ventas con '. $campo . ' = ' .  $valor . PHP_EOL;
                }
                else{
                    echo 'Cantidad de Ventas eliminadas: '. $eliminados . PHP_EOL;
                }

                return $eliminados;
            }

            echo '¡Ocurrió un error en function DeleteBy! objAD, campo o valor al menos uno es NULL. Verifique!!'. PHP_EOL;
            return NULL;
        } 
        catch (PDOException $e) { 
            echo "¡Ocurrió un Error!: ".  $e->getMessage() . PHP_EOL;
        }catch (Exception $e) { 
            echo "¡Ocurrió un Error!: ".  $e->getMessage() . PHP_EOL;
        }
    }





}