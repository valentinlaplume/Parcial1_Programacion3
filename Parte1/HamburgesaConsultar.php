<?php
/*2- (1pt.) HamburguesaConsultar.php: (por POST)

- Se ingresa Nombre, Tipo, 
- si coincide con algún registro del archivo Hamburguesas.json, retornar “Si Hay”. 
De lo contrario informar si no existe el tipo o el nombre.
*/

require_once "Hamburguesa.php";

if(isset($_POST['nombre']) 
&& isset($_POST['tipo']) 
)
{
    $nombre = $_POST['nombre']; 
    $tipo = $_POST['tipo']; // “simple” o “doble”

    $arrayHamburguesaCarga = Hamburguesa::ReadInJson();
    $hamburguesa = new Hamburguesa(null, $nombre, null, $tipo, null);
    $hamburguesa->VerifyNombreTipo($arrayHamburguesaCarga);

}
else{
    echo 'Falta setear al menos el valor de un dato.';
}