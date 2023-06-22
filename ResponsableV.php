<?php

include_once("BaseDatos.php");

class ResponsableV
{

    private $numero;
    private $licencia;
    private $nombre;
    private $apellido;
    private $mensajeoperacion;

    public function __construct()
    {
        $this->numero = "";
        $this->licencia = "";
        $this->nombre = "";
        $this->apellido = "";
    }

    public function cargar($numero, $licencia, $nombre, $apellido)
    {
        $this->setNumero($numero);
        $this->setLicencia($licencia);
        $this->setNombre($nombre);
        $this->setApellido($apellido);
    }

    public function getNumero()
    {
        return $this->numero;
    }

    public function getLicencia()
    {
        return $this->licencia;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getApellido()
    {
        return $this->apellido;
    }

    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    public function setLicencia($licencia)
    {
        $this->licencia = $licencia;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
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
        return "(" . $this->getNumero() . ", " . $this->getLicencia() . ", " . $this->getNombre() . ", " . $this->getApellido() . ")";
    }


    //SQL

    public function insertar()
    {
        $base = new BaseDatos();
        $resp = false;
        $consultaInsertar = "INSERT INTO responsable(rnumerolicencia, rnombre,  rapellido) 
                VALUES (" . floatval($this->getLicencia()) . ",'" . $this->getNombre() . "','" . $this->getApellido() . "')";

        if ($base->Iniciar()) {

            if ($id = $base->devuelveIDInsercion($consultaInsertar)) {
                $this->setNumero($id);
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
        $consultaModifica = "UPDATE responsable SET rapellido='" . $this->getApellido() . "',rnombre='" . $this->getNombre() . "'
                           ,rnumerolicencia='" . $this->getLicencia() . "' WHERE rnumeroempleado=" . $this->getNumero();
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
            $consultaBorra = "DELETE FROM responsable WHERE rnumeroempleado=" . $this->getNumero();
            if ($base->Ejecutar($consultaBorra)) {
                $resp =  true;
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $resp;
    }


    public function Buscar($numero)
    {
        $base = new BaseDatos();
        $consultaResponsable = "Select * from responsable where rnumeroempleado=" . $numero;
        $resp = false;
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaResponsable)) {
                if ($row2 = $base->Registro()) {
                    $this->cargar($numero, $row2['rnumerolicencia'], $row2['rnombre'], $row2['rapellido']);
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
        $arregloResponsable = null;
        $base = new BaseDatos();
        $consultaResponsables = "Select * from responsable ";
        if ($condicion != "") {
            $consultaResponsables = $consultaResponsables . ' where ' . $condicion;
        }
        $consultaResponsables .= " order by rapellido ";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaResponsables)) {
                $arregloResponsable = array();
                while ($row2 = $base->Registro()) {

                    $Numero = $row2['rnumeroempleado'];
                    $Nombre = $row2['rnombre'];
                    $Apellido = $row2['rapellido'];
                    $Licencia = $row2['rnumerolicencia'];

                    $perso = new ResponsableV();
                    $perso->cargar($Numero, $Licencia, $Nombre, $Apellido);
                    array_push($arregloResponsable, $perso);
                }
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $arregloResponsable;
    }
}
