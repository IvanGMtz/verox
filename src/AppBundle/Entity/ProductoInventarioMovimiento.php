<?php

namespace AppBundle\Entity;

/**
 * ProductoInventarioMovimiento
 */
class ProductoInventarioMovimiento
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $producto;

    /**
     * @var string
     */
    private $color;

    /**
     * @var string
     */
    private $movimiento;

    /**
     * @var int
     */
    private $cantidad;

    /**
     * @var string
     */
    private $bodega;

    /**
     * @var string|null
     */
    private $informacion;

    /**
     * @var string
     */
    private $usuario;

    /**
     * @var \DateTime
     */
    private $fecha = 'CURRENT_TIMESTAMP';


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
     * Set producto.
     *
     * @param string $producto
     *
     * @return ProductoInventarioMovimiento
     */
    public function setProducto($producto)
    {
        $this->producto = $producto;

        return $this;
    }

    /**
     * Get producto.
     *
     * @return string
     */
    public function getProducto()
    {
        return $this->producto;
    }

    /**
     * Set color.
     *
     * @param string $color
     *
     * @return ProductoInventarioMovimiento
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
     * Set movimiento.
     *
     * @param string $movimiento
     *
     * @return ProductoInventarioMovimiento
     */
    public function setMovimiento($movimiento)
    {
        $this->movimiento = $movimiento;

        return $this;
    }

    /**
     * Get movimiento.
     *
     * @return string
     */
    public function getMovimiento()
    {
        return $this->movimiento;
    }

    /**
     * Set cantidad.
     *
     * @param int $cantidad
     *
     * @return ProductoInventarioMovimiento
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
     * Set bodega.
     *
     * @param string $bodega
     *
     * @return ProductoInventarioMovimiento
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
     * Set informacion.
     *
     * @param string|null $informacion
     *
     * @return ProductoInventarioMovimiento
     */
    public function setInformacion($informacion = null)
    {
        $this->informacion = $informacion;

        return $this;
    }

    /**
     * Get informacion.
     *
     * @return string|null
     */
    public function getInformacion()
    {
        return $this->informacion;
    }

    /**
     * Set usuario.
     *
     * @param string $usuario
     *
     * @return ProductoInventarioMovimiento
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario.
     *
     * @return string
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set fecha.
     *
     * @param \DateTime $fecha
     *
     * @return ProductoInventarioMovimiento
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha.
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }
}
