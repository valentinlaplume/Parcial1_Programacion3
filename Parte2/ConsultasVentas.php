<?php
/* 2da parte 4- (1 pts.) ConsultasVentas.php: Necesito saber :
a- La cantidad de Hamburguesas vendidas en un día en particular, si no se pasa fecha, se muestran las del día de ayer.
b- El listado de ventas entre dos fechas ordenado por nombre.
c- El listado de ventas de un usuario ingresado.
d- El listado de ventas de un tipo ingresado.
*/
require_once "AccesoDatos.php";
require_once "Venta.php";


$objAD = AccesoDatos::GetObjectAD();

/*
a- La cantidad de Hamburguesas vendidas en un día en particular, si no se pasa fecha, se muestran las del día de ayer.
*/
if(isset($_GET['fecha']))
{
    $cantidadVendidas = Venta::CountHamburguesaSoldByDate($objAD, $_GET['fecha']);
    echo 'A) Cantidad de Hamburguesas vendidas en un día en particular: ', $cantidadVendidas, PHP_EOL;
}
    
//b- El listado de ventas entre dos fechas ordenado por nombre.
if(isset($_GET['fechaDesde']) && isset($_GET['fechaHasta']))
{
    echo PHP_EOL;
    $desde = $_GET['fechaDesde'] . ' 00:00';
    $hasta = $_GET['fechaHasta'] . ' 23:59';
    echo 'B) Listado de Ventas entre '.$desde.' y '.$hasta.' ordenado por nombre: '. PHP_EOL;
    $arrVentas = Venta::GetAllBetweenDatesAndOrderBy($objAD, $desde, $hasta, 'nombreHamburguesa');
    Venta::PrintList($arrVentas);
}

// c- El listado de ventas de un usuario ingresado.
if(isset($_GET['mailUsuario']))
{
    echo PHP_EOL; echo PHP_EOL;
    $mailUsuario = $_GET['mailUsuario'];
    echo 'C) Listado de Ventas mailUsuario ingresado: '. $mailUsuario . PHP_EOL;
    $arrVentasUsuario = Venta::GetAllBy($objAD, 'mailUsuario', $mailUsuario);
    Venta::PrintList($arrVentasUsuario);
}

// d- El listado de ventas de un tipo ingresado.
if(isset($_GET['tipoHamburguesa']))
{
    echo PHP_EOL; echo PHP_EOL;
    $tipoHamburguesa = $_GET['tipoHamburguesa'];
    echo 'D) Listado de Ventas tipoHamburguesa ingresado: '. $tipoHamburguesa . PHP_EOL;
    $arrVentastipo = Venta::GetAllBy($objAD, 'tipoHamburguesa', $tipoHamburguesa);
    Venta::PrintList($arrVentastipo);
}


