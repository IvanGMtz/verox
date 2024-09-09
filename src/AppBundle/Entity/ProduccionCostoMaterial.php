<?php

namespace AppBundle\Entity;

/**
 * ProduccionCostoMaterial
 */
class ProduccionCostoMaterial
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var float
     */
    private $cantidad;

    /**
     * @var \AppBundle\Entity\InventarioCosto
     */
    private $material;

    /**
     * @var \AppBundle\Entity\ProduccionOrden
     */
    private $ordenProduccion;

    /**
     * @var int
     */
    private $diseno;

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
     * @param float $cantidad
     *
     * @return ProduccionCostoMaterial
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
     * Set diseno.
     *
     * @param int $diseno
     *
     * @return ProduccionCostoMaterial
     */
    public function setDiseno($diseno)
    {
        $this->diseno = $diseno;

        return $this;
    }

    /**
     * Get diseno.
     *
     * @return int
     */
    public function getDiseno()
    {
        return $this->diseno;
    }

    /**
     * Set material.
     *
     * @param \AppBundle\Entity\InventarioCosto|null $material
     *
     * @return ProduccionCostoMaterial
     */
    public function setMaterial(\AppBundle\Entity\InventarioCosto $material = null)
    {
        $this->material = $material;

        return $this;
    }

    /**
     * Get material.
     *
     * @return \AppBundle\Entity\InventarioCosto|null
     */
    public function getMaterial()
    {
        return $this->material;
    }

    /**
     * Set ordenProduccion.
     *
     * @param \AppBundle\Entity\ProduccionOrden|null $ordenProduccion
     *
     * @return ProduccionCostoMaterial
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
