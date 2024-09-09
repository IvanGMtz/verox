<?php

namespace AppBundle\Entity;

/**
 * InventarioOrdenDescarga
 */
class InventarioOrdenDescarga
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var float
     */
    private $cantidad = 0;

    /**
     * @var float
     */
    private $valorUnitario = 0;

    /**
     * @var float
     */
    private $valorTotal = 0;

    /**
     * @var \DateTime
     */
    private $fechaCreacion;

    /**
     * @var int
     */
    private $estado = 1;

    /**
     * @var \AppBundle\Entity\Almacen
     */
    private $almacen;

    /**
     * @var \AppBundle\Entity\AlmacenZona
     */
    private $almacenZona;

    /**
     * @var \AppBundle\Entity\InventarioCosto
     */
    private $almacenZonaInventario;

    /**
     * @var \AppBundle\Entity\InventarioOrden
     */
    private $inventarioOrden;

    /**
     * @var \AppBundle\Entity\InventarioOrdenItem
     */
    private $inventarioOrdenItem;

    /**
     * @var \AppBundle\Entity\Material
     */
    private $material;

    /**
     * @var \AppBundle\Entity\FosUser
     */
    private $usuarioCreacion;


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
     * Set cantidad.
     *
     * @param float $cantidad
     *
     * @return InventarioOrdenDescarga
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad.
     *
     * @return float
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set valorUnitario.
     *
     * @param float $valorUnitario
     *
     * @return InventarioOrdenDescarga
     */
    public function setValorUnitario($valorUnitario)
    {
        $this->valorUnitario = $valorUnitario;

        return $this;
    }

    /**
     * Get valorUnitario.
     *
     * @return float
     */
    public function getValorUnitario()
    {
        return $this->valorUnitario;
    }

    /**
     * Set valorTotal.
     *
     * @param float $valorTotal
     *
     * @return InventarioOrdenDescarga
     */
    public function setValorTotal($valorTotal)
    {
        $this->valorTotal = $valorTotal;

        return $this;
    }

    /**
     * Get valorTotal.
     *
     * @return float
     */
    public function getValorTotal()
    {
        return $this->valorTotal;
    }

    /**
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return InventarioOrdenDescarga
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
     * Set estado.
     *
     * @param int $estado
     *
     * @return InventarioOrdenDescarga
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado.
     *
     * @return int
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set almacen.
     *
     * @param \AppBundle\Entity\Almacen|null $almacen
     *
     * @return InventarioOrdenDescarga
     */
    public function setAlmacen(\AppBundle\Entity\Almacen $almacen = null)
    {
        $this->almacen = $almacen;

        return $this;
    }

    /**
     * Get almacen.
     *
     * @return \AppBundle\Entity\Almacen|null
     */
    public function getAlmacen()
    {
        return $this->almacen;
    }

    /**
     * Set almacenZona.
     *
     * @param \AppBundle\Entity\AlmacenZona|null $almacenZona
     *
     * @return InventarioOrdenDescarga
     */
    public function setAlmacenZona(\AppBundle\Entity\AlmacenZona $almacenZona = null)
    {
        $this->almacenZona = $almacenZona;

        return $this;
    }

    /**
     * Get almacenZona.
     *
     * @return \AppBundle\Entity\AlmacenZona|null
     */
    public function getAlmacenZona()
    {
        return $this->almacenZona;
    }

    /**
     * Set almacenZonaInventario.
     *
     * @param \AppBundle\Entity\InventarioCosto|null $almacenZonaInventario
     *
     * @return InventarioOrdenDescarga
     */
    public function setAlmacenZonaInventario(\AppBundle\Entity\InventarioCosto $almacenZonaInventario = null)
    {
        $this->almacenZonaInventario = $almacenZonaInventario;

        return $this;
    }

    /**
     * Get almacenZonaInventario.
     *
     * @return \AppBundle\Entity\InventarioCosto|null
     */
    public function getAlmacenZonaInventario()
    {
        return $this->almacenZonaInventario;
    }

    /**
     * Set inventarioOrden.
     *
     * @param \AppBundle\Entity\InventarioOrden|null $inventarioOrden
     *
     * @return InventarioOrdenDescarga
     */
    public function setInventarioOrden(\AppBundle\Entity\InventarioOrden $inventarioOrden = null)
    {
        $this->inventarioOrden = $inventarioOrden;

        return $this;
    }

    /**
     * Get inventarioOrden.
     *
     * @return \AppBundle\Entity\InventarioOrden|null
     */
    public function getInventarioOrden()
    {
        return $this->inventarioOrden;
    }

    /**
     * Set inventarioOrdenItem.
     *
     * @param \AppBundle\Entity\InventarioOrdenItem|null $inventarioOrdenItem
     *
     * @return InventarioOrdenDescarga
     */
    public function setInventarioOrdenItem(\AppBundle\Entity\InventarioOrdenItem $inventarioOrdenItem = null)
    {
        $this->inventarioOrdenItem = $inventarioOrdenItem;

        return $this;
    }

    /**
     * Get inventarioOrdenItem.
     *
     * @return \AppBundle\Entity\InventarioOrdenItem|null
     */
    public function getInventarioOrdenItem()
    {
        return $this->inventarioOrdenItem;
    }

    /**
     * Set material.
     *
     * @param \AppBundle\Entity\Material|null $material
     *
     * @return InventarioOrdenDescarga
     */
    public function setMaterial(\AppBundle\Entity\Material $material = null)
    {
        $this->material = $material;

        return $this;
    }

    /**
     * Get material.
     *
     * @return \AppBundle\Entity\Material|null
     */
    public function getMaterial()
    {
        return $this->material;
    }

    /**
     * Set usuarioCreacion.
     *
     * @param \AppBundle\Entity\FosUser|null $usuarioCreacion
     *
     * @return InventarioOrdenDescarga
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
}
