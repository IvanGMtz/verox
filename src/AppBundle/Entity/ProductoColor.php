<?php

namespace AppBundle\Entity;

/**
 * ProductoColor
 */
class ProductoColor
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $nombre;
    public function __toString() {
        return ($this->nombre != null) ? $this->nombre : "";
    }

    /**
     * @var string
     */
    private $hex;

    /**
     * @var \AppBundle\Entity\Producto
     */
    private $producto;


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
     * Set nombre.
     *
     * @param string $nombre
     *
     * @return ProductoColor
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre.
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set hex.
     *
     * @param string $hex
     *
     * @return ProductoColor
     */
    public function setHex($hex)
    {
        $this->hex = $hex;

        return $this;
    }

    /**
     * Get hex.
     *
     * @return string
     */
    public function getHex()
    {
        return $this->hex;
    }

    /**
     * Set producto.
     *
     * @param \AppBundle\Entity\Producto|null $producto
     *
     * @return ProductoColor
     */
    public function setProducto(\AppBundle\Entity\Producto $producto = null)
    {
        $this->producto = $producto;

        return $this;
    }

    /**
     * Get producto.
     *
     * @return \AppBundle\Entity\Producto|null
     */
    public function getProducto()
    {
        return $this->producto;
    }
}
