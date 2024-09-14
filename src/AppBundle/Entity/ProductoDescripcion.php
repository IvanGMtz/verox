<?php

namespace AppBundle\Entity;

/**
 * ProductoDescripcion
 */
class ProductoDescripcion
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $texto;

    /**
     * @var string
     */
    private $texto_en;

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
     * Set texto.
     *
     * @param string $texto
     *
     * @return ProductoDescripcion
     */
    public function setTexto($texto)
    {
        $this->texto = $texto;

        return $this;
    }

    /**
     * Get texto.
     *
     * @return string
     */
    public function getTexto()
    {
        return $this->texto;
    }

        /**
     * Set texto_en.
     *
     * @param string $texto_en
     *
     * @return ProductoDescripcion
     */
    public function setTextoEn($texto_en)
    {
        $this->texto_en = $texto_en;

        return $this;
    }

    /**
     * Get texto_en.
     *
     * @return string
     */
    public function getTextoEn()
    {
        return $this->texto_en;
    }

    /**
     * Set producto.
     *
     * @param \AppBundle\Entity\Producto|null $producto
     *
     * @return ProductoDescripcion
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
