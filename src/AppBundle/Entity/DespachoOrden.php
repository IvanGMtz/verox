<?php

namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * DespachoOrden
 */
class DespachoOrden
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Storeusuarios
     */
    private $clienteId;

    /**
     * @var string
     */
    private $clienteTipo;

    /**
     * @var string
     */
    private $direccionEnvio;

    /**
     * @var string
     */
    private $tipoPago;

    /**
     * @var int
     */
    private $statusPago = 1;

    /**
     * @var int
     */
    private $statusOrden = 1;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $items;

    /**
     * @var float
     */
    private $costoEnvio;

    /**
     * @var float
     */
    private $total;

    /**
     * @var string
     */
    private $notas;

    /**
     * @var \DateTime
     */
    private $fechaCreacion;

    /**
     * @var \DateTime|null
     */
    private $fechaDespacho;

    /**
     * @var \AppBundle\Entity\FosUser
     */
    private $usuarioCreacion;

    /**
     * @var bool
     */
    private $anulada = false;

    /**
     * @var \DateTime|null
     */
    private $fechaAnulacion;

    /**
     * @var \DateTime|null
     */
    private $fechaPago;

    /**
     * @var float|null
     */
    private $abono1;

    /**
     * @var float|null
     */
    private $abono2;

    /**
     * @var \DateTime|null
     */
    private $fechaAbono1;

    /**
     * @var \DateTime|null
     */
    private $fechaAbono2;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set clienteId.
     *
     * @param \AppBundle\Entity\StoreUsuarios|null $clienteId
     *
     * @return DespachoOrdenItem
     */
    public function setClienteId(\AppBundle\Entity\StoreUsuarios $clienteId = null)
    {
        $this->clienteId = $clienteId;

        return $this;
    }

    /**
     * Get clienteId.
     *
     * @return \AppBundle\Entity\StoreUsuarios|null
     */
    public function getClienteId()
    {
        return $this->clienteId;
    }

    /**
     * Set clienteTipo.
     *
     * @param string $clienteTipo
     *
     * @return DespachoOrden
     */
    public function setClienteTipo($clienteTipo)
    {
        $this->clienteTipo = $clienteTipo;

        return $this;
    }

    /**
     * Get clienteTipo.
     *
     * @return string
     */
    public function getClienteTipo()
    {
        return $this->clienteTipo;
    }

    /**
     * Set direccionEnvio.
     *
     * @param string $direccionEnvio
     *
     * @return DespachoOrden
     */
    public function setDireccionEnvio($direccionEnvio)
    {
        $this->direccionEnvio = $direccionEnvio;

        return $this;
    }

    /**
     * Get direccionEnvio.
     *
     * @return string
     */
    public function getDireccionEnvio()
    {
        return $this->direccionEnvio;
    }

    /**
     * Set tipoPago.
     *
     * @param string $tipoPago
     *
     * @return DespachoOrden
     */
    public function setTipoPago($tipoPago)
    {
        $this->tipoPago = $tipoPago;

        return $this;
    }

    /**
     * Get tipoPago.
     *
     * @return string
     */
    public function getTipoPago()
    {
        return $this->tipoPago;
    }

    /**
     * Set statusPago.
     *
     * @param int $statusPago
     *
     * @return DespachoOrden
     */
    public function setStatusPago($statusPago)
    {
        $this->statusPago = $statusPago;

        return $this;
    }

    /**
     * Get statusPago.
     *
     * @return int
     */
    public function getStatusPago()
    {
        return $this->statusPago;
    }

    /**
     * Set statusOrden.
     *
     * @param int $statusOrden
     *
     * @return DespachoOrden
     */
    public function setStatusOrden($statusOrden)
    {
        $this->statusOrden = $statusOrden;

        return $this;
    }

    /**
     * Get statusOrden.
     *
     * @return int
     */
    public function getStatusOrden()
    {
        return $this->statusOrden;
    }

    /**
     * Set costoEnvio.
     *
     * @param float $costoEnvio
     *
     * @return DespachoOrden
     */
    public function setCostoEnvio($costoEnvio)
    {
        $this->costoEnvio = $costoEnvio;

        return $this;
    }

    /**
     * Get costoEnvio.
     *
     * @return float
     */
    public function getCostoEnvio()
    {
        return $this->costoEnvio;
    }

    /**
     * Set total.
     *
     * @param float $total
     *
     * @return DespachoOrden
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total.
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set notas.
     *
     * @param string $notas
     *
     * @return DespachoOrden
     */
    public function setNotas($notas)
    {
        $this->notas = $notas;

        return $this;
    }

    /**
     * Get notas.
     *
     * @return string
     */
    public function getNotas()
    {
        return $this->notas;
    }

    /**
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return DespachoOrden
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaCreacion.
     *
     * @return \DateTime
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set fechaDespacho.
     *
     * @param \DateTime|null $fechaDespacho
     *
     * @return DespachoOrden
     */
    public function setFechaDespacho($fechaDespacho = null)
    {
        $this->fechaDespacho = $fechaDespacho;

        return $this;
    }

    /**
     * Get fechaDespacho.
     *
     * @return \DateTime|null
     */
    public function getFechaDespacho()
    {
        return $this->fechaDespacho;
    }

    /**
     * Set usuarioCreacion.
     *
     * @param \AppBundle\Entity\FosUser|null $usuarioCreacion
     *
     * @return DespachoOrden
     */
    public function setUsuarioCreacion(\AppBundle\Entity\FosUser $usuarioCreacion = null)
    {
        $this->usuarioCreacion = $usuarioCreacion;

        return $this;
    }

    /**
     * Get usuarioCreacion.
     *
     * @return \AppBundle\Entity\FosUser|null
     */
    public function getUsuarioCreacion()
    {
        return $this->usuarioCreacion;
    }

    /**
     * Add item.
     *
     * @param \AppBundle\Entity\DespachoOrdenItem $item
     *
     * @return DespachoOrden
     */
    public function addItem(\AppBundle\Entity\DespachoOrdenItem $item)
    {
        if (!$this->items->contains($item)) {
          $item->setOrdenDespacho($this);
          $this->items[] = $item;
        }

        return $this;
    }

    /**
     * Remove item.
     *
     * @param \AppBundle\Entity\DespachoOrdenItem $item
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeItem(\AppBundle\Entity\DespachoOrdenItem $item)
    {
        return $this->items->removeElement($item);
    }

    /**
     * Get item.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    public function getAnulada(): bool
    {
        return $this->anulada;
    }

    public function setAnulada(bool $anulada): self
    {
        $this->anulada = $anulada;
        return $this;
    }

    public function getFechaAnulacion(): ?\DateTime
    {
        return $this->fechaAnulacion;
    }

    public function setFechaAnulacion(?\DateTime $fechaAnulacion): self
    {
        $this->fechaAnulacion = $fechaAnulacion;
        return $this;
    }

    public function getFechaPago(): ?\DateTime
    {
        return $this->fechaPago;
    }

    public function setFechaPago(?\DateTime $fechaPago): self
    {
        $this->fechaPago = $fechaPago;
        return $this;
    }

    public function getAbono1(): ?float
    {
        return $this->abono1;
    }

    public function setAbono1(?float $abono1): self
    {
        $this->abono1 = $abono1;
        return $this;
    }

    public function getAbono2(): ?float
    {
        return $this->abono2;
    }

    public function setAbono2(?float $abono2): self
    {
        $this->abono2 = $abono2;
        return $this;
    }

    public function getFechaAbono1(): ?\DateTime
    {
        return $this->fechaAbono1;
    }

    public function setFechaAbono1(?\DateTime $fechaAbono1): self
    {
        $this->fechaAbono1 = $fechaAbono1;
        return $this;
    }

    public function getFechaAbono2(): ?\DateTime
    {
        return $this->fechaAbono2;
    }

    public function setFechaAbono2(?\DateTime $fechaAbono2): self
    {
        $this->fechaAbono2 = $fechaAbono2;
        return $this;
    }

    /**
     * Calcula el total de abonos realizados
     * @return float
     */
    public function getTotalAbonos()
    {
        return ($this->abono1 ?: 0) + ($this->abono2 ?: 0);
    }

    /**
     * Calcula el saldo pendiente por pagar
     * @return float
     */
    public function getSaldoPendiente()
    {
        return $this->total - $this->getTotalAbonos();
    }

    /**
     * Verifica si tiene abonos registrados
     * @return bool
     */
    public function tieneAbonos()
    {
        return $this->abono1 !== null || $this->abono2 !== null;
    }

    /**
     * Verifica si puede registrar el primer abono
     * @param float $monto
     * @return bool
     */
    public function puedeRegistrarAbono1($monto)
    {
        return $this->abono1 === null && $monto > 0 && $monto < $this->total;
    }

    /**
     * Verifica si puede registrar el segundo abono
     * @param float $monto
     * @return bool
     */
    public function puedeRegistrarAbono2($monto)
    {
        if ($this->abono1 === null || $this->abono2 !== null) {
            return false;
        }
        return $monto > 0 && ($this->abono1 + $monto) < $this->total;
    }

    /**
     * Verifica si puede completar el pago
     * @param float $montoPagoFinal
     * @return bool
     */
    public function puedeCompletarPago($montoPagoFinal)
    {
        $saldoPendiente = $this->getSaldoPendiente();
        return abs($montoPagoFinal - $saldoPendiente) < 0.01; // Tolerancia de 1 centavo
    }

    /**
     * Obtiene el texto descriptivo del estado de pago
     * @return string
     */
    public function getEstadoPagoTexto()
    {
        switch ($this->statusPago) {
            case 1:
                return 'Por Pagar';
            case 2:
                return 'Pagado';
            case 3:
                return 'Abonado';
            default:
                return 'Estado Desconocido';
        }
    }

    /**
     * Registra un abono en la orden
     * @param float $monto
     * @param \DateTime $fecha
     * @return bool
     */
    public function registrarAbono($monto, \DateTime $fecha)
    {
        if ($this->puedeRegistrarAbono1($monto)) {
            $this->setAbono1($monto);
            $this->setFechaAbono1($fecha);
            $this->setStatusPago(3); // Estado "Abonado"
            return true;
        } elseif ($this->puedeRegistrarAbono2($monto)) {
            $this->setAbono2($monto);
            $this->setFechaAbono2($fecha);
            return true;
        }
        return false;
    }

    /**
     * Completa el pago de la orden
     * @param \DateTime $fecha
     * @return bool
     */
    public function completarPago(\DateTime $fecha)
    {
        if ($this->getSaldoPendiente() <= 0.01) { // Tolerancia de 1 centavo
            $this->setStatusPago(2); // Estado "Pagado"
            $this->setFechaPago($fecha);
            return true;
        }
        return false;
    }

}
