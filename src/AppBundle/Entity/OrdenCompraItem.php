<?php

namespace AppBundle\Entity;

/**
 * OrdenCompraItem
 */
class OrdenCompraItem
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
     * @var string
     */
    private $referencia;

    /**
     * @var float
     */
    private $valorUnidad;

    /**
     * @var float
     */
    private $valorTotal;

    /**
     * @var int
     */
    private $estado = '1';

    /**
     * @var \AppBundle\Entity\Material
     */
    private $material;

    /**
     * @var \AppBundle\Entity\OrdenCompra
     */
    private $ordenCompra;

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
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return OrdenCompraItem
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
     * @return OrdenCompraItem
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
     * Set referencia.
     *
     * @param string $referencia
     *
     * @return OrdenCompraItem
     */
    public function setReferencia($referencia)
    {
        $this->referencia = $referencia;

        return $this;
    }

    /**
     * Get referencia.
     *
     * @return string
     */
    public function getReferencia()
    {
        return $this->referencia;
    }

    /**
     * Set valorUnidad.
     *
     * @param float $valorUnidad
     *
     * @return OrdenCompraItem
     */
    public function setValorUnidad($valorUnidad)
    {
        $this->valorUnidad = $valorUnidad;

        return $this;
    }

    /**
     * Get valorUnidad.
     *
     * @return float
     */
    public function getValorUnidad()
    {
        return $this->valorUnidad;
    }

    /**
     * Set valorTotal.
     *
     * @param float $valorTotal
     *
     * @return OrdenCompraItem
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
     * Set estado.
     *
     * @param int $estado
     *
     * @return OrdenCompraItem
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
     * Set material.
     *
     * @param \AppBundle\Entity\Material|null $material
     *
     * @return OrdenCompraItem
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
     * Set ordenCompra.
     *
     * @param \AppBundle\Entity\OrdenCompra|null $ordenCompra
     *
     * @return OrdenCompraItem
     */
    public function setOrdenCompra(\AppBundle\Entity\OrdenCompra $ordenCompra = null)
    {
        $this->ordenCompra = $ordenCompra;

        return $this;
    }

    /**
     * Get ordenCompra.
     *
     * @return \AppBundle\Entity\OrdenCompra|null
     */
    public function getOrdenCompra()
    {
        return $this->ordenCompra;
    }

    /**
     * Set usuarioCreacion.
     *
     * @param \AppBundle\Entity\FosUser|null $usuarioCreacion
     *
     * @return OrdenCompraItem
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
     * @var float
     */
    private $enInventario = 0;

    /**
     * @var \DateTime|null
     */
    private $fechaIngresoInventario;


    /**
     * Set enInventario.
     *
     * @param float $enInventario
     *
     * @return OrdenCompraItem
     */
    public function setEnInventario($enInventario = null)
    {
        $this->enInventario = $enInventario;

        return $this;
    }

    /**
     * Get enInventario.
     *
     * @return float
     */
    public function getEnInventario()
    {
        return $this->enInventario;
    }

    /**
     * Set fechaIngresoInventario.
     *
     * @param \DateTime|null $fechaIngresoInventario
     *
     * @return OrdenCompraItem
     */
    public function setFechaIngresoInventario($fechaIngresoInventario = null)
    {
        $this->fechaIngresoInventario = $fechaIngresoInventario;

        return $this;
    }

    /**
     * Get fechaIngresoInventario.
     *
     * @return \DateTime|null
     */
    public function getFechaIngresoInventario()
    {
        return $this->fechaIngresoInventario;
    }
}
