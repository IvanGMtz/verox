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
}
