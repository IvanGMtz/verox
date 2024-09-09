<?php

namespace AppBundle\Entity;

/**
 * Diseno
 */
class ProduccionOrden
{

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $referencia;
    public function __toString() {
        return 'Orden #'.$this->id.' - '.$this->referencia;
    }

    /**
     * @var \DateTime
     */
    private $fechaCreacion;

    /**
     * @var \DateTime|null
     */
    private $fechaFinalizacion;

    /**
     * @var int|null
     */
    private $duracion;

    /**
     * @var int
     */
    private $estado;

    /**
     * @var string
     */
    private $notas;


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
     * Set referencia.
     *
     * @param string $referencia
     *
     * @return ProduccionOrden
     */
    public function setReferencia($referencia)
    {
        $this->referencia = $referencia;

        return $this;
    }

    /**
     * Get referencia.
     *
     * @return string
     */
    public function getReferencia()
    {
        return $this->referencia;
    }

    /**
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return ProduccionOrden
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
     * Set fechaFinalizacion.
     *
     * @param \DateTime|null $fechaFinalizacion
     *
     * @return ProduccionOrden
     */
    public function setFechaFinalizacion($fechaFinalizacion = null)
    {
        $this->fechaFinalizacion = $fechaFinalizacion;

        return $this;
    }

    /**
     * Get fechaFinalizacion.
     *
     * @return \DateTime|null
     */
    public function getFechaFinalizacion()
    {
        return $this->fechaFinalizacion;
    }

    /**
     * Set duracion.
     *
     * @param int|null $duracion
     *
     * @return ProduccionOrden
     */
    public function setDuracion($duracion = null)
    {
        $this->duracion = $duracion;

        return $this;
    }

    /**
     * Get duracion.
     *
     * @return int|null
     */
    public function getDuracion()
    {
        return $this->duracion;
    }

    /**
     * Set estado.
     *
     * @param int $estado
     *
     * @return ProduccionOrden
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
     * Set notas.
     *
     * @param string $notas
     *
     * @return ProduccionOrden
     */
    public function setNotas($notas)
    {
        $this->notas = $notas;

        return $this;
    }

    /**
     * Get notas.
     *
     * @return string
     */
    public function getNotas()
    {
        return $this->notas;
    }
}
