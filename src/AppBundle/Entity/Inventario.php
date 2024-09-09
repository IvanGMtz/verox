<?php

namespace AppBundle\Entity;

/**
 * Inventario
 */
class Inventario
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var float
     */
    private $ingresoTotal = 0;

    /**
     * @var float
     */
    private $egresoTotal = 0;

    /**
     * @var float
     */
    private $cantidadActual = 0;

    /**
     * @var \DateTime|null
     */
    private $fechaUltimoIngreso;

    /**
     * @var \DateTime|null
     */
    private $fechaUltimoEgreso;

    /**
     * @var float
     */
    private $reserva;

    /**
     * @var \AppBundle\Entity\Material
     */
    private $material;


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
     * Set ingresoTotal.
     *
     * @param float $ingresoTotal
     *
     * @return Inventario
     */
    public function setIngresoTotal($ingresoTotal)
    {
        $this->ingresoTotal = $ingresoTotal;

        return $this;
    }

    /**
     * Get ingresoTotal.
     *
     * @return float
     */
    public function getIngresoTotal()
    {
        return $this->ingresoTotal;
    }

    /**
     * Set egresoTotal.
     *
     * @param float $egresoTotal
     *
     * @return Inventario
     */
    public function setEgresoTotal($egresoTotal)
    {
        $this->egresoTotal = $egresoTotal;

        return $this;
    }

    /**
     * Get egresoTotal.
     *
     * @return float
     */
    public function getEgresoTotal()
    {
        return $this->egresoTotal;
    }

    /**
     * Set cantidadActual.
     *
     * @param float $cantidadActual
     *
     * @return Inventario
     */
    public function setCantidadActual($cantidadActual)
    {
        $this->cantidadActual = $cantidadActual;

        return $this;
    }

    /**
     * Get cantidadActual.
     *
     * @return float
     */
    public function getCantidadActual()
    {
        return $this->cantidadActual;
    }

    /**
     * Set fechaUltimoIngreso.
     *
     * @param \DateTime|null $fechaUltimoIngreso
     *
     * @return Inventario
     */
    public function setFechaUltimoIngreso($fechaUltimoIngreso = null)
    {
        $this->fechaUltimoIngreso = $fechaUltimoIngreso;

        return $this;
    }

    /**
     * Get fechaUltimoIngreso.
     *
     * @return \DateTime|null
     */
    public function getFechaUltimoIngreso()
    {
        return $this->fechaUltimoIngreso;
    }

    /**
     * Set fechaUltimoEgreso.
     *
     * @param \DateTime|null $fechaUltimoEgreso
     *
     * @return Inventario
     */
    public function setFechaUltimoEgreso($fechaUltimoEgreso = null)
    {
        $this->fechaUltimoEgreso = $fechaUltimoEgreso;

        return $this;
    }

    /**
     * Get fechaUltimoEgreso.
     *
     * @return \DateTime|null
     */
    public function getFechaUltimoEgreso()
    {
        return $this->fechaUltimoEgreso;
    }

    /**
     * Set reserva.
     *
     * @param float $reserva
     *
     * @return Inventario
     */
    public function setReserva($reserva)
    {
        $this->reserva = $reserva;

        return $this;
    }

    /**
     * Get reserva.
     *
     * @return float
     */
    public function getReserva()
    {
        return $this->reserva;
    }

    /**
     * Set material.
     *
     * @param \AppBundle\Entity\Material|null $material
     *
     * @return Inventario
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
