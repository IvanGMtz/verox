<?php

namespace AppBundle\Entity;
/**
 * ProduccionNovedad
 */
class ProduccionNovedad
{

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $novedad;

    /**
     * @var string
     */
    private $tipo;

    /**
     * @var \DateTime
     */
    private $fechaCreacion;

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
     * Set novedad.
     *
     * @param string $novedad
     *
     * @return ProduccionNovedad
     */
    public function setNovedad($novedad)
    {
        $this->novedad = $novedad;

        return $this;
    }

    /**
     * Get novedad.
     *
     * @return string
     */
    public function getNovedad()
    {
        return $this->novedad;
    }

    /**
     * Set tipo.
     *
     * @param string $tipo
     *
     * @return ProduccionNovedad
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo.
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return ProduccionNovedad
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaCreacion.
     *
     * @return \DateTime
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set diseno.
     *
     * @param \AppBundle\Entity\ProduccionDiseno|null $diseno
     *
     * @return ProduccionNovedad
     */
    public function setDiseno(\AppBundle\Entity\ProduccionDiseno $diseno = null)
    {
        $this->diseno = $diseno;

        return $this;
    }

    /**
     * Get diseno.
     *
     * @return \AppBundle\Entity\ProduccionDiseno|null
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
     * @return ProduccionNovedad
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
