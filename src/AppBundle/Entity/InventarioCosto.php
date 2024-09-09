<?php

namespace AppBundle\Entity;

/**
 * InventarioCosto
 */
class InventarioCosto
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var float
     */
    private $ingreso;

    /**
     * @var float
     */
    private $egreso;

    /**
     * @var float
     */
    private $cantidadActual;

    /**
     * @var float
     */
    private $valorSinIva;

    /**
     * @var float
     */
    private $valorConIva;

    /**
     * @var \DateTime
     */
    private $fechaUltimoIngreso;

    /**
     * @var \AppBundle\Entity\Material
     */
    private $material;

    /**
     * @var \AppBundle\Entity\OrdenCompra
     */
    private $ordenCompra;

    /**
     * @var \AppBundle\Entity\Proveedor
     */
    private $proveedor;

    /**
     * @var \AppBundle\Entity\Almacen
     */
    private $almacen;

    /**
     * @var \AppBundle\Entity\AlmacenZona
     */
    private $zona;

    public function __toString() {
        return $this->zona.' (Disponible: '.$this->cantidadActual.') Precio de Compra: $'.$this->valorConIva.' - Último ingreso a Inventario: '.$this->fechaUltimoIngreso->format('Y-m-d');
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
     * Set ingreso.
     *
     * @param float $ingreso
     *
     * @return InventarioCosto
     */
    public function setIngreso($ingreso)
    {
        $this->ingreso = $ingreso;

        return $this;
    }

    /**
     * Get ingreso.
     *
     * @return float
     */
    public function getIngreso()
    {
        return $this->ingreso;
    }

    /**
     * Set egreso.
     *
     * @param float $egreso
     *
     * @return InventarioCosto
     */
    public function setEgreso($egreso)
    {
        $this->egreso = $egreso;

        return $this;
    }

    /**
     * Get egreso.
     *
     * @return float
     */
    public function getEgreso()
    {
        return $this->egreso;
    }

    /**
     * Set cantidadActual.
     *
     * @param float $cantidadActual
     *
     * @return InventarioCosto
     */
    public function setCantidadActual($cantidadActual)
    {
        $this->cantidadActual = $cantidadActual;

        return $this;
    }

    /**
     * Get cantidadActual.
     *
     * @return float
     */
    public function getCantidadActual()
    {
        return $this->cantidadActual;
    }

    /**
     * Set valorSinIva.
     *
     * @param float $valorSinIva
     *
     * @return InventarioCosto
     */
    public function setValorSinIva($valorSinIva)
    {
        $this->valorSinIva = $valorSinIva;

        return $this;
    }

    /**
     * Get valorSinIva.
     *
     * @return float
     */
    public function getValorSinIva()
    {
        return $this->valorSinIva;
    }

    /**
     * Set valorConIva.
     *
     * @param float $valorConIva
     *
     * @return InventarioCosto
     */
    public function setValorConIva($valorConIva)
    {
        $this->valorConIva = $valorConIva;

        return $this;
    }

    /**
     * Get valorConIva.
     *
     * @return float
     */
    public function getValorConIva()
    {
        return $this->valorConIva;
    }

    /**
     * Set fechaUltimoIngreso.
     *
     * @param \DateTime $fechaUltimoIngreso
     *
     * @return InventarioCosto
     */
    public function setFechaUltimoIngreso($fechaUltimoIngreso)
    {
        $this->fechaUltimoIngreso = $fechaUltimoIngreso;

        return $this;
    }

    /**
     * Get fechaUltimoIngreso.
     *
     * @return \DateTime
     */
    public function getFechaUltimoIngreso()
    {
        return $this->fechaUltimoIngreso;
    }

    /**
     * Set material.
     *
     * @param \AppBundle\Entity\Material|null $material
     *
     * @return InventarioCosto
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
     * @return InventarioCosto
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
     * Set proveedor.
     *
     * @param \AppBundle\Entity\Proveedor|null $proveedor
     *
     * @return InventarioCosto
     */
    public function setProveedor(\AppBundle\Entity\Proveedor $proveedor = null)
    {
        $this->proveedor = $proveedor;

        return $this;
    }

    /**
     * Get alamcen.
     *
     * @return \AppBundle\Entity\Almacen|null
     */
    public function getAlmacen()
    {
        return $this->almacen;
    }

    /**
     * Set almacen.
     *
     * @param \AppBundle\Entity\Almacen|null $almacen
     *
     * @return InventarioCosto
     */
    public function setAlmacen(\AppBundle\Entity\Almacen $almacen = null)
    {
        $this->almacen = $almacen;

        return $this;
    }

    /**
     * Get proveedor.
     *
     * @return \AppBundle\Entity\Proveedor|null
     */
    public function getProveedor()
    {
        return $this->proveedor;
    }

    /**
     * Set zona.
     *
     * @param \AppBundle\Entity\AlmacenZona|null $zona
     *
     * @return InventarioCosto
     */
    public function setZona(\AppBundle\Entity\AlmacenZona $zona = null)
    {
        $this->zona = $zona;

        return $this;
    }

    /**
     * Get zona.
     *
     * @return \AppBundle\Entity\AlmacenZona|null
     */
    public function getZona()
    {
        return $this->zona;
    }
}
