<?php

namespace AppBundle\Entity;

/**
 * ProduccionBordado
 */
class ProduccionBordado
{

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $cantidad;

    /**
     * @var int
     */
    private $cantidadConfirmada;

    /**
     * @var int
     */
    private $estado;

    /**
     * @var string
     */
    private $tipo;

    /**
     * @var \DateTime
     */
    private $fechaCreacion;

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
     * Set cantidad.
     *
     * @param int $cantidad
     *
     * @return ProduccionBordado
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
     * @return ProduccionBordado
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
     * Set estado.
     *
     * @param int $estado
     *
     * @return ProduccionBordado
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
     * Set tipo.
     *
     * @param string $tipo
     *
     * @return ProduccionBordado
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
     * @return ProduccionBordado
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
     * Set ordenProduccion.
     *
     * @param \AppBundle\Entity\ProduccionOrden|null $ordenProduccion
     *
     * @return ProduccionBordado
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
