<?php

class Empresa
{

    private $idempresa;
    private $enombre;
    private $edireccion;
    private $viajes;
    private $mensajeoperacion;

    public function __construct()
    {
        $this->idempresa = '';
        $this->enombre = '';
        $this->edireccion = '';
        $this->viajes = [];
    }

    public function cargar($idempresa, $enombre, $edireccion)
    {
        $this->setIdempresa($idempresa);
        $this->setEnombre($enombre);
        $this->setEdireccion($edireccion);
        $this->setViajes(Viaje::listar('idempresa =' . $this->getIdempresa()));
    }

    public function getIdempresa()
    {
        return $this->idempresa;
    }

    public function setIdempresa($idempresa)
    {
        $this->idempresa = $idempresa;
    }

    public function getEnombre()
    {
        return $this->enombre;
    }

    public function setEnombre($enombre)
    {
        $this->enombre = $enombre;
    }

    public function getEdireccion()
    {
        return $this->edireccion;
    }

    public function setEdireccion($edireccion)
    {
        $this->edireccion = $edireccion;
    }

    public function getViajes()
    {
        return $this->viajes;
    }

    public function setViajes($viajes)
    {
        $this->viajes = $viajes;
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
        return "[idempresa: " . $this->getIdempresa() . ", nombre: " . $this->getEnombre() . ", direccion:" . $this->getEdireccion() . "\nviajes: " . $this->auxToString();
    }

    private function auxToString()
    {
        $cadena = "[";
        $arreglo = $this->getViajes();
        $indice = count($arreglo);
        if ($indice == 0) {
            $cadena = "No hay viajes";
        } else {
            for ($i = 0; $i < $indice; $i++) {
                $cadena = $cadena . ($arreglo[$i]->__toString()) . ", ";
            }
            $cadena = substr($cadena, 0, -2) . "]";
        }
        return $cadena;
    }

    public function esViaje($viaje)
    {
        $viajes = $this->getViajes();
        $indice = count($viajes);
        $band = false;
        $i = 0;
        while ($i < $indice && !$band) {
            if ($viajes[$i]->getCodigo() == $viaje->getCodigo()) {
                $band = true;
            }
            $i++;
        }
        return $band;
    }

    //SQL

    public function insertar()
    {
        $base = new BaseDatos();
        $resp = false;
        $consultaInsertar = "INSERT INTO empresa(enombre, edireccion) 
                VALUES ('" . $this->getEnombre() . "','" . $this->getEdireccion() . "')";

        if ($base->Iniciar()) {

            if ($id = $base->devuelveIDInsercion($consultaInsertar)) {
                $this->setIdempresa($id);
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
        $consultaModifica = "UPDATE empresa SET enombre='" . $this->getEnombre() . "',edireccion='" . $this->getEdireccion() . "' WHERE idempresa= " . $this->getIdempresa();
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
            $consultaBorra = "DELETE FROM empresa WHERE idempresa= " . $this->getIdempresa();
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


    public function Buscar($codigo)
    {
        $base = new BaseDatos();
        $consultaempresa = "Select * from empresa where idempresa= " . intval($codigo);
        $resp = false;
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaempresa)) {
                if ($row2 = $base->Registro()) {
                    $this->cargar($codigo, $row2['enombre'], $row2['edireccion']);
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
        $arregloempresa = null;
        $base = new BaseDatos();
        $consultaempresas = "Select * from empresa ";
        if ($condicion != "") {
            $consultaempresas = $consultaempresas . ' where ' . $condicion;
        }
        $consultaempresas .= " order by idempresa ";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaempresas)) {
                $arregloempresa = array();
                while ($row2 = $base->Registro()) {

                    $idEmpresa = $row2['idempresa'];
                    $enombre = $row2['enombre'];
                    $edireccion = $row2['edireccion'];

                    $empresa = new Empresa();
                    $empresa->cargar($idEmpresa, $enombre, $edireccion);
                    array_push($arregloempresa, $empresa);
                }
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $arregloempresa;
    }
}
