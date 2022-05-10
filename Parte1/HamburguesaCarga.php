<?php
/*
B- (1 pt.) HamburguesaCarga.php: (por POST) 
- Se ingresa Nombre, Precio, Tipo (“simple” o “doble”), Cantidad(de unidades). 

- Se guardan los datos en en el archivo de texto Hamburguesas.json, tomando un id autoincremental
como identificador(emulado) .

- Sí el nombre y tipo ya existen , se actualiza el precio y se suma al stock existente.

- Completar el alta con imagen de la hamburguesa, guardando la imagen con el tipo y el nombre como
identificación en la carpeta /ImagenesDeHamburguesas.

*/

require_once "Hamburguesa.php";
require_once "ManejadorArchivos.php";

if(isset($_POST['nombre']) 
&& isset($_POST['precio']) 
&& isset($_POST['tipo']) 
&& isset($_POST['cantidad']) 
)
{
    $nombre = $_POST['nombre']; 
    $precio = $_POST['precio'];
    $tipo = $_POST['tipo']; // “simple” o “doble”
    $cantidad = $_POST['cantidad'];

    $obj = new Hamburguesa(0, $nombre, $precio, $tipo, $cantidad);
    $arrayHamburguesa = Hamburguesa::ReadInJson();
    $arrayHamburguesa = $obj->UpdateArray($arrayHamburguesa);
    Hamburguesa::SaveInJson($arrayHamburguesa);

    //./ImagenesDeHamburguesas

    $directory = './ImagenesDeHamburguesas';
    $fileName = $obj->GetFileName();

    $resultSaveImage = ManejadorArchivos::SaveImage($directory, $fileName, $_FILES);
    if($resultSaveImage){ echo 'Imagen de Hamburguesa guardada con exito.', PHP_EOL; }

}
else{
    echo 'Falta setear al menos el valor de un dato.';
}