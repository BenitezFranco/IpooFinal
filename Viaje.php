<?php

include_once("BaseDatos.php");
include_once('Pasajero.php');
include_once('ResponsableV.php');

class Viaje
{

    private $codigo;

    private $destino;

    private $cantidad;

    private $responsable;

    private $costo;

    private $pasajeros;

    private $empresa;

    private $mensajeoperacion;

    public function __construct()
    {
        $this->codigo = "";
        $this->destino = "";
        $this->cantidad = "";
        $this->responsable = new ResponsableV();
        $this->costo = "";
        $this->pasajeros = [];
        $this->empresa = new Empresa();
    }

    public function cargar($codigo, $destino, $cantidad, $responsable, $costo)
    {
        $this->setCodigo($codigo);
        $this->setDestino($destino);
        $this->setCantidad($cantidad);
        $this->setResponsable($responsable);
        $this->setCosto($costo);
        $this->setPasajeros(Pasajero::listar('idviaje =' . $codigo));
    }

    public function getCodigo()
    {
        return $this->codigo;
    }

    public function getDestino()
    {
        return $this->destino;
    }

    public function getCantidad()
    {
        return $this->cantidad;
    }

    public function getResponsable()
    {
        return $this->responsable;
    }

    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    public function setDestino($destino)
    {
        $this->destino = $destino;
    }

    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }

    public function setResponsable($responsable)
    {
        $this->responsable = $responsable;
    }

    public function getCosto()
    {
        return $this->costo;
    }

    public function setCosto($costo)
    {
        $this->costo = $costo;
    }


    public function getPasajeros()
    {
        return $this->pasajeros;
    }

    public function setPasajeros($pasajeros)
    {
        $this->pasajeros = $pasajeros;
    }

    public function getEmpresa()
    {
        return $this->empresa;
    }

    public function setEmpresa($empresa)
    {
        $this->empresa = $empresa;
    }

    public function setmensajeoperacion($mensajeoperacion)
    {
        $this->mensajeoperacion = $mensajeoperacion;
    }
    public function getmensajeoperacion()
    {
        return $this->mensajeoperacion;
    }

    /**
     * Agrega un pasajero al arreglo de pasajeros si todavia hay espacio
     * @param Pasajero Un Objeto de tipo Pasajero
     * @return double Retorna el costo a abonar por el pasajero
     */

    public function venderPasaje($pasajero)
    {
        $importe = null;
        if (!$this->esPasajero($pasajero)) {
            $band = $this->hayPasajesDisponible();
            if ($band) {
                $pasajero->setViaje($this);
                array_push($this->getPasajeros(), $pasajero);
                $importe = $this->getCosto();
            }
        } else {
            $importe = 0;
        }
        return $importe;
    }

    public function __toString()
    {
        return "\n(Codigo: " . $this->codigo . ", Destino: " . $this->destino . ", CantidadAsientos: " . $this->cantidad . ", Responsable: " . $this->auxToStringResponsable() . ", Costo: " . $this->getCosto() . ", Pasajeros:" . $this->auxToStringPasajeros() . ")";
    }

    private function auxToStringResponsable()
    {
        $cadena = "[";
        $responsable = $this->getResponsable();
        if ($responsable == null) {
            $cadena .= "No hay responsable]";
        } else {
            $cadena .= $responsable->__toString() . "]";
        }
        return $cadena;
    }

    private function auxToStringPasajeros()
    {
        $cadena = "[";
        $arreglo = $this->getPasajeros();
        $indice = count($arreglo);
        if ($indice == 0) {
            $cadena = "No hay pasajeros";
        } else {
            for ($i = 0; $i < $indice; $i++) {
                $cadena = $cadena . ($arreglo[$i]->__toString()) . ", ";
            }
            $cadena = substr($cadena, 0, -2) . "]";
        }
        return $cadena;
    }

    public function hayPasajesDisponible()
    {
        $hayLugar = count($this->getPasajeros()) < $this->getCantidad();
        return $hayLugar;
    }

    public function esPasajero($pasajero)
    {
        $pasajeros = $this->getPasajeros();
        $indice = count($pasajeros);
        $band = false;
        $i = 0;
        while ($i < $indice) {
            if ($pasajeros[$i]->getDni() == $pasajero->getDni()) {
                $band = true;
                break;
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
        $resp = $this->getResponsable() !== null ? intval(($this->getResponsable())->getNumero()) : "NULL";
        $empr = $this->getEmpresa() !== null ? intval(($this->getEmpresa())->getIdempresa()) : "NULL";
        $consultaInsertar = "INSERT INTO viaje(vdestino, vcantmaxpasajeros, rnumeroempleado, vimporte, idempresa) 
                VALUES ('" . $this->getDestino() . "'," . intval($this->getCantidad()) . ", " . $resp . "," . floatval($this->getCosto()) . ", " . $empr . ")";

        if ($base->Iniciar()) {

            if ($id = $base->devuelveIDInsercion($consultaInsertar)) {
                $this->setCodigo($id);
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
        $resp = $this->getResponsable();
        $emp = $this->getEmpresa();
        $consultaModifica = "UPDATE viaje SET vdestino='" . $this->getDestino() . "',vcantmaxpasajeros= " . intval($this->getCantidad()) .
            ", vimporte= " . floatval($this->getCosto());
        if ($resp != null) {
            $consultaModifica .= ", rnumeroempleado= " . intval($resp->getNumero());
        }
        $consultaModifica .= " WHERE idviaje= " . intval($this->getCodigo());
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
            $consultaBorra = "DELETE FROM viaje WHERE idviaje= " . $this->getCodigo();
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
        $consultaViaje = "SELECT * FROM viaje where idviaje= " . intval($codigo);
        $resp = false;
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaViaje)) {
                if ($row2 = $base->Registro()) {
                    $resp = new ResponsableV();
                    $resp->Buscar($row2['rnumeroempleado']);
                    $emp = new Empresa();
                    $emp->Buscar($row2['idempresa']);
                    $this->cargar($codigo, $row2['vdestino'], $row2['vcantmaxpasajeros'], $resp, $row2['vimporte'], $emp);
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
        $arregloViaje = null;
        $base = new BaseDatos();
        $consultaViajes = "Select * from viaje ";
        if ($condicion != "") {
            $consultaViajes = $consultaViajes . ' where ' . $condicion;
        }
        $consultaViajes .= " order by idviaje ";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaViajes)) {
                $arregloViaje = array();
                while ($row2 = $base->Registro()) {

                    $idviaje = $row2['idviaje'];
                    $destino = $row2['vdestino'];
                    $cantidad = $row2['vcantmaxpasajeros'];
                    $importe = $row2['vimporte'];
                    $resp = new ResponsableV();
                    $resp->Buscar($row2['rnumeroempleado']);

                    $viaje = new Viaje();
                    $viaje->cargar($idviaje, $destino, $cantidad, $resp, $importe);
                    array_push($arregloViaje, $viaje);
                }
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $arregloViaje;
    }
}
