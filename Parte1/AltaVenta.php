<?php
/*3-
a- (1 pts.) AltaHamburguesa.php: (por POST)
- Se recibe el email del usuario y el nombre, tipo y cantidad ,
si el ítem existe en Hamburguesas.json, y hay stock guardar en la base de datos( con la fecha, número de pedido y id autoincremental ) y se debe descontar la cantidad vendida del stock .

b- (1 pt) Completar el alta con imagen de la Hamburguesa , guardando la imagen con el tipo+nombre+mail (solo usuario
hasta el @) y fecha de la Hamburguesa en la carpeta /ImagenesDeLaHamburguesa.
*/

require_once "AccesoDatos.php";
require_once "ManejadorArchivos.php";
require_once "Hamburguesa.php";
require_once "Venta.php";

date_default_timezone_set('America/Argentina/Buenos_Aires');

/*
a- (1 pts.) AltaHamburguesa.php: (por POST)
- Se recibe el email del usuario y el nombre, tipo y cantidad ,
- Si el ítem existe en Hamburguesas.json, y hay stock guardar en la base de datos( con la fecha, número de pedido y id autoincremental ) y se debe descontar la cantidad vendida del stock .*/

$mail = $_POST['mail'];
$nombre = $_POST['nombre'];
$tipo = $_POST['tipo']; // “simple” o “doble” 
$cantidad = $_POST['cantidad'];

$arrayHamburguesa = Hamburguesa::ReadInJson();
$Hamburguesa = new Hamburguesa(null, $nombre, null, $tipo, $cantidad);

// Actualizo array de Hamburguesa por la Hamburguesa, * resto stock *
$arrayUpdateByHamburguesa = Hamburguesa::UpdateArrayByVenta($arrayHamburguesa, $Hamburguesa);
$arrHamburguesaActualizado = $arrayUpdateByHamburguesa[0]; // array Hamburguesa actualizado
$returnUpdate = $arrayUpdateByHamburguesa[1]; // result operacion update

if($returnUpdate) // Si se actualizó correctamente Hamburguesa.json descontando Hamburguesas
{
    $objAD = AccesoDatos::GetObjectAD();
    
    //Obtengo ultimo numero de Pedido de Venta
    $numeroPedidoMax = Venta::GetLastNumeroPedidoVenta($objAD);
    $Venta = new Venta(0, $mail, $nombre, $tipo, $numeroPedidoMax, $cantidad);
    
    // Inserto Venta en DB
    $resultID = $Venta->InsertIntoDB($objAD); 
    echo 'Venta guardada en DB.', PHP_EOL;
    echo 'Ultimo id Venta DB: ', $resultID, PHP_EOL;
    
    
    /*
    b- (1 pt) Completar el alta con imagen de la Venta , guardando la imagen con el 
    tipo+nombre+mail (solo usuario hasta el @) y fecha de la Venta en la carpeta /ImagenesDeLaVenta. */
    $directory = './ImagenesDeLaVenta';
    $fileName = $Venta->GetFileName();

    $resultSaveImage = ManejadorArchivos::SaveImage($directory, $fileName, $_FILES);
    if($resultSaveImage){ echo 'Imagen de Venta guardada con éxito.', PHP_EOL; }

}