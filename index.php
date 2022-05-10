<?php
/*
    PRIMER PARCIAL - PROGRAMACION III 
    Fecha: 09/05/2022
*/

date_default_timezone_set('America/Argentina/Buenos_Aires');
$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) 
{
    case "GET":
        echo 'Petición por GET'. PHP_EOL;
        switch ($_GET['opcion']) 
        {
            case 'consultarVentas':
                require_once "Parte2/ConsultasVentas.php";
            break;
            default:
                echo 'Opción por GET no válida.'. PHP_EOL;
            break;
        }
    break;
///////////////////////////////////////////////////////////////////////////
    case 'POST':
        echo 'Petición por POST'. PHP_EOL;
        switch ($_POST['opcion']) 
        {
            case 'cargarHamburgesa':
                require_once "Parte1/HamburguesaCarga.php";
            break;
            case 'consultarHamburgesa':
                require_once "Parte1/HamburgesaConsultar.php";
            break;
            case 'ventaHamburgesa':
                require_once "Parte1/AltaVenta.php";
            break;
            case 'devolver':
                require_once "Parte3/DevolverHamburguesa.php";
            break;
            default:
                echo 'Opción por POST no válida.'. PHP_EOL;
            break;
        }
    break;
///////////////////////////////////////////////////////////////////////////
    case 'PUT':
        echo 'Petición por PUT'. PHP_EOL;
        $body = json_decode(file_get_contents('php://input'), true);
        switch ($body['opcion']) 
        {
            default:
                echo 'Opción por PUT no válida.'. PHP_EOL;
            break;
        }
    break;
///////////////////////////////////////////////////////////////////////////
    case 'DELETE':
        echo 'Petición DELETE'. PHP_EOL;
        $body = json_decode(file_get_contents('php://input'), true);
        switch ($body['opcion']) 
        {
            case 'borrarVenta':
                require_once "Parte3/borrarVenta.php";
            break;
            default:
                echo 'Opción por DELETE no válida.'. PHP_EOL;
            break;
        }
    break;
///////////////////////////////////////////////////////////////////////////
    default:
        echo 'Petición no válida.'. PHP_EOL;
    break;
}

?>