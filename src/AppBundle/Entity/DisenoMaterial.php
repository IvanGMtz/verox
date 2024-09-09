<?php

namespace AppBundle\Entity;

/**
 * DisenoMaterial
 */
class DisenoMaterial
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var float
     */
    private $cantidad = 0;

    /**
     *  @var \AppBundle\Entity\ProcesoNombre
     */
    private $proceso;

    /**
     * @var int
     */
    private $estado = 1;

    /**
     * @var \AppBundle\Entity\Diseno
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
     * @return DisenoMaterial
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
     * Set estado.
     *
     * @param int $estado
     *
     * @return DisenoMaterial
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado.
     *
     * @return int
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set diseno.
     *
     * @param \AppBundle\Entity\Diseno|null $diseno
     *
     * @return DisenoMaterial
     */
    public function setDiseno(\AppBundle\Entity\Diseno $diseno = null)
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
     * Set procesoNombre.
     *
     * @param \AppBundle\Entity\ProcesoNombre|null $proceso
     *
     * @return DisenoMaterial
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
     * @var \AppBundle\Entity\Material
     */
    private $material;


    /**
     * Set material.
     *
     * @param \AppBundle\Entity\Material|null $material
     *
     * @return DisenoMaterial
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
}
