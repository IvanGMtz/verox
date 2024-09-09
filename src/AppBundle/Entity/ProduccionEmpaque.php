<?php

namespace AppBundle\Entity;

/**
 * ProduccionEmpaque
 */
class ProduccionEmpaque
{

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $caja;

    /**
     * @var int
     */
    private $cantidad;

    /**
     * @var string
     */
    private $marca;

    /**
     * @var string
     */
    private $notas;

    /**
     * @var string
     */
    private $curva;

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
     * Set caja.
     *
     * @param int $caja
     *
     * @return ProduccionEmpaque
     */
    public function setCaja($caja)
    {
        $this->caja = $caja;

        return $this;
    }

    /**
     * Get caja.
     *
     * @return int
     */
    public function getCaja()
    {
        return $this->caja;
    }

    /**
     * Set cantidad.
     *
     * @param int $cantidad
     *
     * @return ProduccionEmpaque
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
     * Set marca.
     *
     * @param string $marca
     *
     * @return ProduccionEmpaque
     */
    public function setMarca($marca)
    {
        $this->marca = $marca;

        return $this;
    }

    /**
     * Get marca.
     *
     * @return string
     */
    public function getMarca()
    {
        return $this->marca;
    }

    /**
     * Set notas.
     *
     * @param string $notas
     *
     * @return ProduccionEmpaque
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

    /**
     * Set curva.
     *
     * @param string $curva
     *
     * @return ProduccionEmpaque
     */
    public function setCurva($curva)
    {
        $this->curva = $curva;

        return $this;
    }

    /**
     * Get curva.
     *
     * @return string
     */
    public function getCurva()
    {
        return $this->curva;
    }

    /**
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return ProduccionEmpaque
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
     * @return ProduccionEmpaque
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
     * @return ProduccionEmpaque
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
    public function __toString()
    {
      return (string) $this->getId();
    }
}
