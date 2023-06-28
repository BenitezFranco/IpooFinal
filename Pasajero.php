<?php

include_once("BaseDatos.php");

class Pasajero
{

    private $nombre;
    private $apellido;
    private $dni;
    private $telefono;
    private $viaje;
    private $mensajeoperacion;

    public function __construct()
    {
        $this->nombre = "";
        $this->apellido = "";
        $this->dni = "";
        $this->telefono = "";
        $this->viaje = new Viaje();
    }

    public function cargar($nombre, $apellido, $dni, $telefono)
    {
        $this->setNombre($nombre);
        $this->setApellido($apellido);
        $this->setDni($dni);
        $this->setTelefono($telefono);
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getApellido()
    {
        return $this->apellido;
    }

    public function getDni()
    {
        return $this->dni;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
    }

    public function setDni($dni)
    {
        $this->dni = $dni;
    }

    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    public function getViaje()
    {
        return $this->viaje;
    }

    public function setViaje($viaje)
    {
        $this->viaje = $viaje;
    }

    public function setmensajeoperacion($mensajeoperacion)
    {
        $this->mensajeoperacion = $mensajeoperacion;
    }
    public function getmensajeoperacion()
    {
        return $this->mensajeoperacion;
    }

    public function __toString()
    {
        $cadena= $this->getViaje()!=null ? " Viaje: ".$this->getViaje()->getCodigo(): "";
        return "\n(Nombre: " . $this->getNombre() . ", Apellido: " . $this->getApellido() . ", DNI: " . $this->getDni() . ", Telefono: " . $this->getTelefono().$cadena. ")";
    }

    //SQL

    public function insertar()
    {
        $base = new BaseDatos();
        $resp = false;
        $consultaInsertar = "INSERT INTO pasajero(pdocumento, papellido, pnombre,  ptelefono, idviaje) 
                VALUES ('" . $this->getDni() . "','" . $this->getApellido() . "','" . $this->getNombre() . "'," . intval($this->getTelefono()) . ", " . $this->getViaje()->getCodigo() . ")";

        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaInsertar)) {
                $resp =  true;
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $resp;
    }

    public function modificar()
    {
        $resp = false;
        $base = new BaseDatos();
        $consultaModifica = "UPDATE pasajero SET papellido='" . $this->getApellido() . "',pnombre='" . $this->getNombre() . "'
                           ,ptelefono=" . $this->getTelefono() . ", idviaje= " . $this->getViaje()->getCodigo();

        $consultaModifica .= " WHERE pdocumento=" . $this->getDni();
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaModifica)) {
                $resp =  true;
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $resp;
    }

    public function eliminar()
    {
        $base = new BaseDatos();
        $resp = false;
        if ($base->Iniciar()) {
            $consultaBorra = "DELETE FROM pasajero WHERE pdocumento=" . $this->getDni();
            if ($base->Ejecutar($consultaBorra)) {
                $resp =  true;
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $resp;
    }


    public function Buscar($dni)
    {
        $base = new BaseDatos();
        $consultaPasajero = "Select * from pasajero where pdocumento=" . $dni;
        $resp = false;
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaPasajero)) {
                if ($row2 = $base->Registro()) {
                    $viaje = new Viaje();
                    $viaje->Buscar($row2['idviaje']);
                    $this->cargar($row2['pnombre'], $row2['papellido'], $dni, $row2['ptelefono']);
                    $this->setViaje($viaje);
                    $resp = true;
                }
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $resp;
    }

    public static function listar($condicion = "")
    {
        $arregloPasajero = null;
        $base = new BaseDatos();
        $consultaPasajeros = "Select * from pasajero ";
        if ($condicion != "") {
            $consultaPasajeros = $consultaPasajeros . ' where ' . $condicion;
        }
        $consultaPasajeros .= " order by papellido ";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaPasajeros)) {
                $arregloPasajero = array();
                while ($row2 = $base->Registro()) {

                    $NroDoc = $row2['pdocumento'];
                    $Nombre = $row2['pnombre'];
                    $Apellido = $row2['papellido'];
                    $Telefono = $row2['ptelefono'];
                    $perso = new Pasajero();
                    $perso->cargar($Nombre, $Apellido, $NroDoc, $Telefono);
                    array_push($arregloPasajero, $perso);
                }
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $arregloPasajero;
    }
}
