<?php

namespace AppBundle\Entity;

/**
 * ProductoInventario
 */
class ProductoInventario
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $ingresoDetal = '0';

    /**
     * @var int
     */
    private $egresoDetal = '0';

    /**
     * @var int
     */
    private $qtyActualDetal = '0';

    /**
     * @var \DateTime|null
     */
    private $ultimoIngresoD;

    /**
     * @var \DateTime|null
     */
    private $ultimoEgresoD;

    /**
     * @var int
     */
    private $ingresoMayorista = '0';

    /**
     * @var int
     */
    private $egresoMayorista = '0';

    /**
     * @var int
     */
    private $qtyActualMayorista = '0';

    /**
     * @var \DateTime|null
     */
    private $ultimoIngresoM;

    /**
     * @var \DateTime|null
     */
    private $ultimoEgresoM;

    /**
     * @var \AppBundle\Entity\ProductoTalla
     */
    private $producto;
    
    /**
     * @var \AppBundle\Entity\ProductoColor
     */
    private $color;

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
     * Set ingresoDetal.
     *
     * @param int $ingresoDetal
     *
     * @return ProductoInventario
     */
    public function setIngresoDetal($ingresoDetal)
    {
        $this->ingresoDetal = $ingresoDetal;

        return $this;
    }

    /**
     * Get ingresoDetal.
     *
     * @return int
     */
    public function getIngresoDetal()
    {
        return $this->ingresoDetal;
    }

    /**
     * Set egresoDetal.
     *
     * @param int $egresoDetal
     *
     * @return ProductoInventario
     */
    public function setEgresoDetal($egresoDetal)
    {
        $this->egresoDetal = $egresoDetal;

        return $this;
    }

    /**
     * Get egresoDetal.
     *
     * @return int
     */
    public function getEgresoDetal()
    {
        return $this->egresoDetal;
    }

    /**
     * Set qtyActualDetal.
     *
     * @param int $qtyActualDetal
     *
     * @return ProductoInventario
     */
    public function setQtyActualDetal($qtyActualDetal)
    {
        $this->qtyActualDetal = $qtyActualDetal;

        return $this;
    }

    /**
     * Get qtyActualDetal.
     *
     * @return int
     */
    public function getQtyActualDetal()
    {
        return $this->qtyActualDetal;
    }

    /**
     * Set ultimoIngresoD.
     *
     * @param \DateTime|null $ultimoIngresoD
     *
     * @return ProductoInventario
     */
    public function setUltimoIngresoD($ultimoIngresoD = null)
    {
        $this->ultimoIngresoD = $ultimoIngresoD;

        return $this;
    }

    /**
     * Get ultimoIngresoD.
     *
     * @return \DateTime|null
     */
    public function getUltimoIngresoD()
    {
        return $this->ultimoIngresoD;
    }

    /**
     * Set ultimoEgresoD.
     *
     * @param \DateTime|null $ultimoEgresoD
     *
     * @return ProductoInventario
     */
    public function setUltimoEgresoD($ultimoEgresoD = null)
    {
        $this->ultimoEgresoD = $ultimoEgresoD;

        return $this;
    }

    /**
     * Get ultimoEgresoD.
     *
     * @return \DateTime|null
     */
    public function getUltimoEgresoD()
    {
        return $this->ultimoEgresoD;
    }

    /**
     * Set ingresoMayorista.
     *
     * @param int $ingresoMayorista
     *
     * @return ProductoInventario
     */
    public function setIngresoMayorista($ingresoMayorista)
    {
        $this->ingresoMayorista = $ingresoMayorista;

        return $this;
    }

    /**
     * Get ingresoMayorista.
     *
     * @return int
     */
    public function getIngresoMayorista()
    {
        return $this->ingresoMayorista;
    }

    /**
     * Set egresoMayorista.
     *
     * @param int $egresoMayorista
     *
     * @return ProductoInventario
     */
    public function setEgresoMayorista($egresoMayorista)
    {
        $this->egresoMayorista = $egresoMayorista;

        return $this;
    }

    /**
     * Get egresoMayorista.
     *
     * @return int
     */
    public function getEgresoMayorista()
    {
        return $this->egresoMayorista;
    }

    /**
     * Set qtyActualMayorista.
     *
     * @param int $qtyActualMayorista
     *
     * @return ProductoInventario
     */
    public function setQtyActualMayorista($qtyActualMayorista)
    {
        $this->qtyActualMayorista = $qtyActualMayorista;

        return $this;
    }

    /**
     * Get qtyActualMayorista.
     *
     * @return int
     */
    public function getQtyActualMayorista()
    {
        return $this->qtyActualMayorista;
    }

    /**
     * Set ultimoIngresoM.
     *
     * @param \DateTime|null $ultimoIngresoM
     *
     * @return ProductoInventario
     */
    public function setUltimoIngresoM($ultimoIngresoM = null)
    {
        $this->ultimoIngresoM = $ultimoIngresoM;

        return $this;
    }

    /**
     * Get ultimoIngresoM.
     *
     * @return \DateTime|null
     */
    public function getUltimoIngresoM()
    {
        return $this->ultimoIngresoM;
    }

    /**
     * Set ultimoEgresoM.
     *
     * @param \DateTime|null $ultimoEgresoM
     *
     * @return ProductoInventario
     */
    public function setUltimoEgresoM($ultimoEgresoM = null)
    {
        $this->ultimoEgresoM = $ultimoEgresoM;

        return $this;
    }

    /**
     * Get ultimoEgresoM.
     *
     * @return \DateTime|null
     */
    public function getUltimoEgresoM()
    {
        return $this->ultimoEgresoM;
    }

    /**
     * Set producto.
     *
     * @param \AppBundle\Entity\ProductoTalla|null $producto
     *
     * @return ProductoInventario
     */
    public function setProducto(\AppBundle\Entity\ProductoTalla $producto = null)
    {
        $this->producto = $producto;

        return $this;
    }

    /**
     * Get producto.
     *
     * @return \AppBundle\Entity\ProductoTalla|null
     */
    public function getProducto()
    {
        return $this->producto;
    }
    /**
     * Set color.
     *
     * @param \AppBundle\Entity\ProductoColor
     *
     * @return ProductoInventario
     */
    public function setColor(\AppBundle\Entity\ProductoColor $color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color.
     *
     * @return \AppBundle\Entity\ProductoColor
     */
    public function getColor()
    {
        return $this->color;
    }
}
