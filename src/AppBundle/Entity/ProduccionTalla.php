<?php

namespace AppBundle\Entity;

/**
 * ProduccionTalla
 */
class ProduccionTalla
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $talla;

    /**
     * @var int
     */
    private $cantidad;

    /**
     * @var int
     */
    private $cantidadConfirmada;

    /**
     * @var \AppBundle\Entity\ProduccionDiseno
     */
    private $diseno;

    /**
     * @var \AppBundle\Entity\ProduccionOrden
     */
    private $ordenProduccion;


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
     * Set talla.
     *
     * @param string $talla
     *
     * @return ProduccionTalla
     */
    public function setTalla($talla)
    {
        $this->talla = $talla;

        return $this;
    }

    /**
     * Get talla.
     *
     * @return string
     */
    public function getTalla()
    {
        return $this->talla;
    }

    /**
     * Set cantidad.
     *
     * @param int $cantidad
     *
     * @return ProduccionTalla
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
     * Set cantidadConfirmada.
     *
     * @param int $cantidadConfirmada
     *
     * @return ProduccionTalla
     */
    public function setCantidadConfirmada($cantidadConfirmada)
    {
        $this->cantidadConfirmada = $cantidadConfirmada;

        return $this;
    }

    /**
     * Get cantidadConfirmada.
     *
     * @return int
     */
    public function getCantidadConfirmada()
    {
        return $this->cantidadConfirmada;
    }

    /**
     * Set diseno.
     *
     * @param \AppBundle\Entity\ProduccionDiseno|null $diseno
     *
     * @return ProduccionTalla
     */
    public function setDiseno(\AppBundle\Entity\ProduccionDiseno $diseno = null)
    {
        $this->diseno = $diseno;

        return $this;
    }

    /**
     * Get diseno.
     *
     * @return \AppBundle\Entity\Diseno|null
     */
    public function getDiseno()
    {
        return $this->diseno;
    }

    /**
     * Set ordenProduccion.
     *
     * @param \AppBundle\Entity\ProduccionOrden|null $ordenProduccion
     *
     * @return ProduccionTalla
     */
    public function setOrdenProduccion(\AppBundle\Entity\ProduccionOrden $ordenProduccion = null)
    {
        $this->ordenProduccion = $ordenProduccion;

        return $this;
    }

    /**
     * Get ordenProduccion.
     *
     * @return \AppBundle\Entity\ProduccionOrden|null
     */
    public function getOrdenProduccion()
    {
        return $this->ordenProduccion;
    }
}
