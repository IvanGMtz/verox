<?php

namespace AppBundle\Entity;

/**
 * InventarioOrdenItem
 */
class InventarioOrdenItem
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $fechaCreacion;

    /**
     * @var float
     */
    private $cantidad;

    /**
     * @var int
     */
    private $estado = 1;

    /**
     * @var bool|null
     */
    private $entregado = 0;

    /**
     * @var \DateTime|null
     */
    private $fechaEntregado;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $descargas;

    /**
     * @var \AppBundle\Entity\InventarioOrden
     */
    private $inventarioOrden;

    /**
     * @var \AppBundle\Entity\Material
     */
    private $material;

    /**
     * @var \AppBundle\Entity\Material
     */
    private $categoria;

    /**
     * @var \AppBundle\Entity\FosUser
     */
    private $usuarioCreacion;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->descargas = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return InventarioOrdenItem
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
     * Set cantidad.
     *
     * @param float $cantidad
     *
     * @return InventarioOrdenItem
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
     * Set estado.
     *
     * @param int $estado
     *
     * @return InventarioOrdenItem
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
     * Set entregado.
     *
     * @param bool|null $entregado
     *
     * @return InventarioOrdenItem
     */
    public function setEntregado($entregado = null)
    {
        $this->entregado = $entregado;

        return $this;
    }

    /**
     * Get entregado.
     *
     * @return bool|null
     */
    public function getEntregado()
    {
        return $this->entregado;
    }

    /**
     * Set fechaEntregado.
     *
     * @param \DateTime|null $fechaEntregado
     *
     * @return InventarioOrdenItem
     */
    public function setFechaEntregado($fechaEntregado = null)
    {
        $this->fechaEntregado = $fechaEntregado;

        return $this;
    }

    /**
     * Get fechaEntregado.
     *
     * @return \DateTime|null
     */
    public function getFechaEntregado()
    {
        return $this->fechaEntregado;
    }

    /**
     * Add descarga.
     *
     * @param \AppBundle\Entity\InventarioOrdenDescarga $descarga
     *
     * @return InventarioOrdenItem
     */
    public function addDescarga(\AppBundle\Entity\InventarioOrdenDescarga $descarga)
    {
        $this->descargas[] = $descarga;

        return $this;
    }

    /**
     * Remove descarga.
     *
     * @param \AppBundle\Entity\InventarioOrdenDescarga $descarga
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeDescarga(\AppBundle\Entity\InventarioOrdenDescarga $descarga)
    {
        return $this->descargas->removeElement($descarga);
    }

    /**
     * Get descargas.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDescargas()
    {
        return $this->descargas;
    }

    /**
     * Set inventarioOrden.
     *
     * @param \AppBundle\Entity\InventarioOrden|null $inventarioOrden
     *
     * @return InventarioOrdenItem
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
     * Set material.
     *
     * @param \AppBundle\Entity\Material|null $material
     *
     * @return InventarioOrdenItem
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
     * Set categoria.
     *
     * @param \AppBundle\Entity\Material|null $categoria
     *
     * @return InventarioOrdenItem
     */
    public function setCategoria(\AppBundle\Entity\Material $categoria = null)
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * Get categoria.
     *
     * @return \AppBundle\Entity\Material|null
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * Set usuarioCreacion.
     *
     * @param \AppBundle\Entity\FosUser|null $usuarioCreacion
     *
     * @return InventarioOrdenItem
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

    public $disponible = 0;

    public $alcanza = false;

}
