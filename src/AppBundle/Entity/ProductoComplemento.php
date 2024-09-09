<?php

namespace AppBundle\Entity;

/**
 * ProductoComplemento
 */
class ProductoComplemento
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Producto
     */
    private $complemento;

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
     * Set complemento.
     *
     * @param \AppBundle\Entity\Producto|null $complemento
     *
     * @return ProductoComplemento
     */
    public function setComplemento(\AppBundle\Entity\Producto $complemento = null)
    {
        $this->complemento = $complemento;

        return $this;
    }

    /**
     * Get complemento.
     *
     * @return \AppBundle\Entity\Producto|null
     */
    public function getComplemento()
    {
        return $this->complemento;
    }

    /**
     * Set producto.
     *
     * @param \AppBundle\Entity\Producto|null $producto
     *
     * @return ProductoComplemento
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
