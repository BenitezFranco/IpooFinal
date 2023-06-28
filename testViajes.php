<?php

include_once('Empresa.php');
include_once('Viaje.php');
include_once('Pasajero.php');
include_once('ResponsableV.php');

do {
    $opcion = menuMenus();
    switch ($opcion) {
        case 1:
            empresas();
            break;
        case 2:
            viajes();
            break;
        case 3:
            responsables();
            break;
        case 4:
            pasajeros();
        default:
            break;
    };
} while ($opcion != 5);
echo ("Fin del programa");


function empresas()
{
    do {
        $opcion = menuEmpresa();
        switch ($opcion) {
            case 1:
                ingresarEmpresa();
                break;
            case 2:
                modificarEmpresa();
                break;
            case 3:
                eliminarEmpresa();
                break;
            case 4:
                mostrarEmpresa();
            default:
                break;
        };
    } while ($opcion != 5);
}

function viajes()
{
    do {
        $opcion = menuViaje();
        switch ($opcion) {
            case 1:
                $viaje = pedirViaje();
                modificarViaje($viaje);
                break;
            case 2:
                $viaje = pedirViaje();
                mostrarViaje($viaje);
            default:
                break;
        };
    } while ($opcion != 3);
}

function responsables()
{
    do {
        $opcion = menuResponsable();
        switch ($opcion) {
            case 1:
                ingresarResponsable();
                break;
            case 2:
                $responsable = pedirResponsable();
                modificarResponsable($responsable);
                break;
            case 3:
                $responsable = pedirResponsable();
                eliminarResponsable($responsable);
                break;
            case 4:
                $responsable = pedirResponsable();
                mostrarResponsable($responsable);
            default:
                break;
        };
    } while ($opcion != 5);
}

function pasajeros()
{
    do {
        $opcion = menuPasajero();
        switch ($opcion) {
            case 1:
                modificarPasajero();
                break;
            case 2:
                mostrarPasajero();
            default:
                break;
        };
    } while ($opcion != 3);
}
//Menu de menus
function menuMenus()
{
    $band = true;
    do {
        echo
        "\nPresione:  
        1 para ingresar al menu de empresas
        2 para ingresar al menu de viajes
        3 para ingresar al menu de responsables
        4 para ingresar al menu de pasajeros
        5 para salir\n";
        $opcion =  trim(fgets(STDIN));
        if ($opcion > 0 && $opcion <= 5) {
            $band = false;
        } else {
            echo "Opcion invalida\n";
        }
    } while ($band);
    return $opcion;
}

//Funciones de Empresa

function ingresarEmpresa()
{

    $empresa = new Empresa();
    echo "Ingrese el nombre de la empresa\n";
    $nombre =  trim(fgets(STDIN));
    $empresa->setEnombre($nombre);
    echo "Ingrese la direccion de la empresa\n";
    $direccion =  trim(fgets(STDIN));
    $empresa->setEdireccion($direccion);
    if ($empresa->insertar()) {
        echo "Se pudo ingresar la empresa\n";
    } else {
        echo "No se pudo ingresar la empresa\n";
    }
}

function modificarEmpresa()
{
    $empresa = pedirEmpresa();

    if ($empresa != null) {
        echo "Ingrese el nombre de la empresa o -1 saltar paso\n";
        $nombre =  trim(fgets(STDIN));
        if ($nombre !== "-1") {
            $empresa->setEnombre($nombre);
        }
        echo "Ingrese la direccion de la empresa o -1 saltar paso\n";
        $direccion =  trim(fgets(STDIN));
        if ($direccion !== "-1") {
            $empresa->setEdireccion($direccion);
        }
        echo "Si no desea agregar un viaje ingrese -1\n";
        $resp =  trim(fgets(STDIN));
        if ($resp !== "-1") {
            ingresarViaje($empresa);
        }
        echo "Si no desea agregar un viaje ingrese -1\n";
        $resp =  trim(fgets(STDIN));
        if ($resp !== "-1") {
            $viaje = pedirViaje();
            eliminarViaje($empresa, $viaje);
        }

        $empresa->modificar();
    }
}

function eliminarEmpresa()
{
    $empresa = pedirEmpresa();
    if ($empresa != null) {
        $viajes = $empresa->getViajes();
        $indice = count($viajes);
        if ($indice > 0) {
            echo "Si no desea eliminar una empresa con sus viajes y pasajeros ingrese -1\n";
            $resp =  trim(fgets(STDIN));
            if ($resp !== "-1") {
                for ($i = 0; $i < $indice; $i++) {
                    eliminarViaje($empresa, $viajes[$i]);
                }
            }
        }
        $empresa->eliminar();
    }
}

function mostrarEmpresa()
{
    $empresa = pedirEmpresa();
    if ($empresa != null) {
        echo "" . $empresa->__toString();
    }
}

//Menu Empresa
function menuEmpresa()
{
    $band = true;
    do {
        echo
        "\nPresione:  
        1 para ingresar una empresa
        2 para modificar una empresa 
        3 para eliminar una empresa
        4 para ver la informacion de una empresa
        5 para salir\n";
        $opcion =  trim(fgets(STDIN));
        if ($opcion > 0 && $opcion <= 5) {
            $band = false;
        } else {
            echo "Opcion invalida\n";
        }
    } while ($band);
    return $opcion;
}

// Funciones viaje

function ingresarViaje($empresa)
{
    $viaje = new Viaje();
    echo "Ingrese el destino\n";
    $destino =  trim(fgets(STDIN));
    echo "Ingrese la cantidad maxima de asientos\n";
    $cantidad =  trim(fgets(STDIN));
    echo "Ingrese el costo\n";
    $costo =  trim(fgets(STDIN));

    $viaje->setDestino($destino);
    $viaje->setCantidad((int)$cantidad);
    $viaje->setCosto((float)$costo);
    agregarResponsable($viaje);
    $viaje->setEmpresa($empresa);
    if ($viaje->insertar()) {
        array_push($empresa->getViajes(), $viaje);
        echo "Se pudo ingresar el viaje\n";
    } else {
        echo "No se pudo ingresar el viaje\n";
    }
}

function modificarViaje($viaje)
{
    if ($viaje != null) {
        echo "Ingrese el destino o -1 saltar paso\n";
        $destino =  trim(fgets(STDIN));
        if ($destino !== "-1") {
            $viaje->setDestino($destino);
        }
        echo "Ingrese la cantidad de asientos o -1 saltar paso\n";
        $cantidad =  trim(fgets(STDIN));
        if ($cantidad !== "-1") {
            $viaje->setCantidad($cantidad);
        }
        echo "Ingrese el costo o -1 saltar paso\n";
        $costo =  trim(fgets(STDIN));
        if ($costo !== "-1") {
            $viaje->setCosto($costo);
        }
        echo "Si no desea cambiar el responsable ingrese -1\n";
        $resp =  trim(fgets(STDIN));
        if ($resp !== "-1") {
            agregarResponsable($viaje);
        }
        echo "Si no desea agregar un pasajero ingrese -1\n";
        $resp =  trim(fgets(STDIN));
        if ($resp !== "-1") {
            ingresarPasajero($viaje);
        }
        echo "Si no desea quitar un pasajero ingrese -1\n";
        $resp =  trim(fgets(STDIN));
        if ($resp !== "-1") {
            eliminarPasajero($viaje);
        }
        $viaje->modificar();
    }
}

function eliminarViaje($empresa, $viaje)
{
    if ($empresa->esViaje($viaje)) {
        $pasajeros = $viaje->getPasajeros();
        $indice = count($pasajeros);
        for ($i = 0; $i < $indice; $i++) {
            $pasajeros[$i]->eliminar();
        }
        $viaje->eliminar();
    } else {
        echo "Ese viaje no esta en esa empresa\n";
    }
}

function mostrarViaje($viaje)
{
    if ($viaje != null) {
        echo "" . $viaje->__toString();
    }
}

//Menu viaje
function menuViaje()
{
    $band = true;
    do {
        echo
        "\nPresione:  
        1 para modificar un viaje
        2 para ver la informacion de un viaje
        3 para salir\n";
        $opcion =  trim(fgets(STDIN));
        if ($opcion > 0 && $opcion <= 3) {
            $band = false;
        } else {
            echo "Opcion invalida\n";
        }
    } while ($band);
    return $opcion;
}

// Funciones Responsable

function ingresarResponsable()
{
    $responsable = new ResponsableV();
    echo "Ingrese la licencia\n";
    $licencia =  trim(fgets(STDIN));
    echo "Ingrese el nombre\n";
    $nombre =  trim(fgets(STDIN));
    echo "Ingrese el apellido\n";
    $apellido =  trim(fgets(STDIN));

    $responsable->setLicencia((float)$licencia);
    $responsable->setNombre($nombre);
    $responsable->setApellido($apellido);
    if ($responsable->insertar()) {
        echo "Se pudo ingresar el responsable\n";
    } else {
        echo "No se pudo ingresar el responsable\n";
    }
    return $responsable;
}

function modificarResponsable($responsable)
{
    if ($responsable != null) {
        echo "Ingrese la licencia o -1 saltar paso\n";
        $licencia =  trim(fgets(STDIN));
        echo "Ingrese el nombre o -1 saltar paso\n";
        $nombre =  trim(fgets(STDIN));
        echo "Ingrese el apellido o -1 saltar paso\n";
        $apellido =  trim(fgets(STDIN));
        if ($licencia !== "-1") {
            $responsable->setLicencia($licencia);
        }
        if ($nombre !== "-1") {
            $responsable->setNombre($nombre);
        }
        if ($apellido !== "-1") {
            $responsable->setApellido($apellido);
        }

        $responsable->modificar();
    }
}

function eliminarResponsable($responsable)
{
    if ($responsable != null) {
        $arreglo = Viaje::listar('rnumeroempleado = ' . $responsable->getNumero());
        if (count($arreglo) > 0) {
            echo "No se puede eliminar porque responsable esta en un viaje\n";
        } else {
            $responsable->eliminar();
            echo "Responsable borrado \n";
        }
    }
}

function mostrarResponsable($responsable)
{
    if ($responsable != null) {
        echo "" . $responsable->__toString();
    }
}

//Menu responsable
function menuResponsable()
{
    $band = true;
    do {
        echo
        "\nPresione:  
        1 para ingresar un responsable
        2 para modificar un responsable
        3 para eliminar un responsable
        4 para ver la informacion de un responsable
        5 para salir\n";
        $opcion =  trim(fgets(STDIN));
        if ($opcion > 0 && $opcion <= 5) {
            $band = false;
        } else {
            echo "Opcion invalida\n";
        }
    } while ($band);
    return $opcion;
}


// Funciones Pasajero

function ingresarPasajero($viaje)
{
    $pasajero = new Pasajero();
    $dni = comprobarDNIPasajero();
    echo "Ingrese el nombre\n";
    $nombre =  trim(fgets(STDIN));
    echo "Ingrese el apellido\n";
    $apellido =  trim(fgets(STDIN));
    echo "Ingrese el telefono\n";
    $telefono =  trim(fgets(STDIN));
    $pasajero->setNombre($nombre);
    $pasajero->setApellido($apellido);
    $pasajero->setDni($dni);
    $pasajero->setTelefono($telefono);
    $importe = $viaje->venderPasaje($pasajero);
    if ($importe != null) {
        if ($importe != 0) {
            if ($pasajero->insertar()) {
                echo "Se pudo ingresar el pasajero\n";
            } else {
                echo "No se pudo ingresar el pasajero\n";
            }
        } else {
            echo "El pasajero ya esta en ese viaje\n";
        }
    } else {
        echo "El Viaje esta lleno\n";
    }
}

function modificarPasajero()
{
    $pasajero = pedirPasajero();

    if ($pasajero != null) {
        echo "Ingrese el nombre o -1 saltar paso\n";
        $nombre =  trim(fgets(STDIN));
        echo "Ingrese el apellido o -1 saltar paso\n";
        $apellido =  trim(fgets(STDIN));
        echo "Ingrese el telefono o -1 saltar paso\n";
        $telefono =  trim(fgets(STDIN));
        if ($nombre !== "-1") {
            $pasajero->setNombre($nombre);
        }
        if ($apellido !== "-1") {
            $pasajero->setApellido($apellido);
        }
        if ($telefono !== "-1") {
            $pasajero->setTelefono($telefono);
        }
        $pasajero->modificar();
    }
}

function eliminarPasajero($viaje)
{
    $pasajero = pedirPasajero();
    if ($viaje->esPasajero($pasajero)) {
        $pasajero->eliminar();
    } else {
        echo "El pasajero no esta en ese viaje\n";
    }
}

function mostrarPasajero()
{
    $pasajero = pedirPasajero();
    if ($pasajero != null) {
        echo "" . $pasajero->__toString();
    }
}

//Menu Pasajero
function menuPasajero()
{
    $band = true;
    do {
        echo
        "\nPresione:  
        1 para modificar un pasajero
        2 para ver la informacion de un pasajero
        3 para salir\n";
        $opcion =  trim(fgets(STDIN));
        if ($opcion > 0 && $opcion <= 3) {
            $band = false;
        } else {
            echo "Opcion invalida\n";
        }
    } while ($band);
    return $opcion;
}


//auxiliares

function pedirEmpresa()
{
    $empresas = Empresa::listar();
    foreach ($empresas as $emp) {
        echo "" . $emp->__toString() . "\n\n\n";
    }
    $band = false;
    $empresa = null;
    do {
        echo "Ingrese el codigo de la empresa\n";
        $codigo =  trim(fgets(STDIN));
        $empresa = new Empresa();
        $band = $empresa->Buscar($codigo);

        if (!$band) {
            echo "Codigo inexistente\n";
        }
    } while (!$band);
    return $empresa;
}

function pedirViaje()
{
    $viajes = Viaje::listar();
    foreach ($viajes as $elem) {
        echo "" . $elem->__toString() . "\n\n\n";
    }
    $viaje = null;
    $band = false;
    do {
        echo "Ingrese el codigo del viaje\n";
        $codigo =  trim(fgets(STDIN));

        $viaje = new Viaje();
        $band = $viaje->Buscar($codigo);
        if (!$band) {
            echo "Codigo inexistente\n";
        }
    } while (!$band);
    return $viaje;
}

function pedirResponsable()
{
    $responsables = ResponsableV::listar();
    foreach ($responsables as $elem) {
        echo "" . $elem->__toString() . "\n\n\n";
    }
    $responsable = null;
    $band = false;
    do {
        echo "Ingrese el numero del responsable\n";
        $codigo =  trim(fgets(STDIN));

        $responsable = new ResponsableV();
        $band = $responsable->Buscar($codigo);
        if (!$band) {
            echo "Numero inexistente\n";
        }
    } while (!$band);
    return $responsable;
}
//Devuelve un pasajero que existe
function pedirPasajero()
{
    $pasajeros = Pasajero::listar();
    foreach ($pasajeros as $elem) {
        echo "" . $elem->__toString() . "\n\n\n";
    }
    $band = false;
    do {
        echo "Ingrese el DNI del pasajero\n";
        $codigo =  trim(fgets(STDIN));

        $pasajero = new Pasajero();
        $band = $pasajero->Buscar($codigo);
        if (!$band) {
            echo "Dni inexistente\n";
        }
    } while (!$band);
    return $pasajero;
}

//Devuelve un DNI inexistente
function comprobarDNIPasajero()
{
    $pasajeros = Pasajero::listar();
    foreach ($pasajeros as $elem) {
        echo "" . $elem->__toString() . "\n\n\n";
    }
    $band = false;
    do {
        echo "Ingrese el DNI del pasajero\n";
        $codigo =  trim(fgets(STDIN));
        $pasajero = new Pasajero();
        $band = !$pasajero->Buscar($codigo);
        if (!$band) {
            echo "Codigo existente\n";
        }
    } while (!$band);
    return $codigo;
}

function agregarResponsable($viaje)
{
    $responsable = pedirResponsable();
    if ($viaje != null && $responsable != null) {
        if ($viaje->getResponsable()->getNumero() != $responsable->getNumero()) {
            $viaje->setResponsable($responsable);
            echo "Se agrego el responsable\n";
        } else {
            echo "El responsable ya esta en ese viaje\n";
        }
    }
}
