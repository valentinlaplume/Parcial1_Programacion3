<?php

/*
7- (2 pts.) DevolverHamburguesa.php 
- Guardar en el archivo (devoluciones.json y cupones.json):

a- Se ingresa el número de pedido y la causa de la devolución.
El número de pedido debe existir, 
Se ingresa una foto del cliente enojado, esto debe generar un cupón de descuento con el 10% de descuento para la próxima
compra.
*/

require_once "AccesoDatos.php";
require_once "ManejadorArchivos.php";
require_once "Venta.php";
require_once "Devolucion.php";
require_once "Cupon.php";

// trae array asociativo

if(isset($_POST['numero']) 
&& isset($_POST['causa']) 
)
{
    $objAD = AccesoDatos::GetObjectAD();    
    $venta = Venta::GetBy($objAD, 'numero', $_POST['numero']);

    if(!is_null($venta)){

        $dev = new Devolucion($_POST['numero'], $_POST['causa']);
        $cupon = new Cupon($_POST['numero']);

        if(!is_null($dev)){
            $arrDev = array($dev);
            $arrCup = array($cupon);
            if(Devolucion::SaveInJson($arrDev) && Cupon::SaveInJson($arrCup))
            {
                $directory = './ClientesDevoluciones';
                $fileName = $dev->GetFileName();
                
                $resultSaveImage = ManejadorArchivos::SaveImage($directory, $fileName, $_FILES);
                if($resultSaveImage){ echo 'Imagen de Cliente guardada con exito.', PHP_EOL; }
            }
        }
    }

}
else{
    echo 'No fueron seteados valores por metodo POST en "DevolverHamburgesa.php". Verifique!!', PHP_EOL;
}

