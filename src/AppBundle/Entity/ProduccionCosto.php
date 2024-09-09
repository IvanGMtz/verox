<?php

namespace AppBundle\Entity;
/**
 * ProduccionCosto
 */
class ProduccionCosto
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var float
     */
    private $costo;

    /**
     * @var float
     */
    private $costo2;

    /**
     * @var float
     */
    private $costo3;

    /**
     * @var float
     */
    private $costo4;
    /**
     * @var string
     */
    private $descripcion;

    /**
     * @var \AppBundle\Entity\ProcesoNombre
     */
    private $proceso;

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
     * Set costo.
     *
     * @param float $costo
     *
     * @return ProduccionCosto
     */
    public function setCosto($costo)
    {
        $this->costo = $costo;

        return $this;
    }

    /**
     * Get costo.
     *
     * @return float
     */
    public function getCosto()
    {
        return $this->costo;
    }

    /**
     * Set costo2.
     *
     * @param float $costo2
     *
     * @return ProduccionCosto
     */
    public function setCosto2($costo2)
    {
        $this->costo2 = $costo2;

        return $this;
    }

    /**
     * Get costo2.
     *
     * @return float
     */
    public function getCosto2()
    {
        return $this->costo2;
    }

    /**
     * Set costo3.
     *
     * @param float $costo3
     *
     * @return ProduccionCosto
     */
    public function setCosto3($costo3)
    {
        $this->costo3 = $costo3;

        return $this;
    }

    /**
     * Get costo3.
     *
     * @return float
     */
    public function getCosto3()
    {
        return $this->costo3;
    }

    /**
     * Set costo4.
     *
     * @param float $costo4
     *
     * @return ProduccionCosto
     */
    public function setCosto4($costo4)
    {
        $this->costo4 = $costo4;

        return $this;
    }

    /**
     * Get costo4.
     *
     * @return float
     */
    public function getCosto4()
    {
        return $this->costo4;
    }
    /**
     * Set descripcion.
     *
     * @param float $descripcion
     *
     * @return ProduccionCosto
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion.
     *
     * @return float
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set proceso.
     *
     * @param \AppBundle\Entity\ProcesoNombre|null $proceso
     *
     * @return ProduccionCosto
     */
    public function setProceso(\AppBundle\Entity\ProcesoNombre $proceso = null)
    {
        $this->proceso = $proceso;

        return $this;
    }

    /**
     * Get proceso.
     *
     * @return \AppBundle\Entity\ProcesoNombre|null
     */
    public function getProceso()
    {
        return $this->proceso;
    }

    /**
     * Set ordenProduccion.
     *
     * @param \AppBundle\Entity\ProduccionOrden|null $ordenProduccion
     *
     * @return ProduccionCosto
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
