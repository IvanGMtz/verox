<?php

namespace AppBundle\Entity;


/**
 * DespachoOrdenItem
 */
class DespachoOrdenItem
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $cantidad;

    /**
     * @var float
     */
    private $precio;

    /**
     * @var string
     */
    private $color;
    /**
     * @var string
     */
    private $bodega;
    
    

    /**
     * @var int
     */
    private $estatus;
 
    /**
     * @var \AppBundle\Entity\ProductoTalla
     */
    private $producto;

    /**
     * @var \AppBundle\Entity\DespachoOrden
     */
    private $ordenDespacho;


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
     * @param int $cantidad
     *
     * @return DespachoOrdenItem
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad.
     *
     * @return int
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set precio.
     *
     * @param float $precio
     *
     * @return DespachoOrdenItem
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * Get precio.
     *
     * @return float
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Set bodega.
     *
     * @param string $bodega
     *
     * @return DespachoOrdenItem
     */
    public function setBodega($bodega)
    {
        $this->bodega = $bodega;

        return $this;
    }

    /**
     * Get bodega.
     *
     * @return string
     */
    public function getBodega()
    {
        return $this->bodega;
    }
    /**
     * Set color.
     *
     * @param string $color
     *
     * @return DespachoOrdenItem
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color.
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set estatus.
     *
     * @param int $estatus
     *
     * @return DespachoOrdenItem
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;

        return $this;
    }

    /**
     * Get estatus.
     *
     * @return int
     */
    public function getEstatus()
    {
        return $this->estatus;
    }

    /**
     * Set producto.
     *
     * @param \AppBundle\Entity\ProductoTalla|null $producto
     *
     * @return DespachoOrdenItem
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
     * Set ordenDespacho.
     *
     * @param \AppBundle\Entity\DespachoOrden|null $ordenDespacho
     *
     * @return DespachoOrdenItem
     */
    public function setOrdenDespacho(\AppBundle\Entity\DespachoOrden $ordenDespacho = null)
    {
        $this->ordenDespacho = $ordenDespacho;

        return $this;
    }

    /**
     * Get ordenDespacho.
     *
     * @return \AppBundle\Entity\DespachoOrden|null
     */
    public function getOrdenDespacho()
    {
        return $this->ordenDespacho;
    }

    
}
