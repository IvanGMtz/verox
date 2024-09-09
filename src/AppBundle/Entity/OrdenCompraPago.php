<?php

namespace AppBundle\Entity;

/**
 * OrdenCompraPago
 */
class OrdenCompraPago
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $fechaCreacion;

    /**
     * @var string
     */
    private $tipoPago;

    /**
     * @var float
     */
    private $valor;

    /**
     * @var string
     */
    private $referencia;

    /**
     * @var string|null
     */
    private $descripcion;

    /**
     * @var int
     */
    private $estado = '1';

    /**
     * @var \AppBundle\Entity\OrdenCompra
     */
    private $ordenCompra;

    /**
     * @var \AppBundle\Entity\FosUser
     */
    private $usuarioCreacion;


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
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return OrdenCompraPago
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
     * Set tipoPago.
     *
     * @param string $tipoPago
     *
     * @return OrdenCompraPago
     */
    public function setTipoPago($tipoPago)
    {
        $this->tipoPago = $tipoPago;

        return $this;
    }

    /**
     * Get tipoPago.
     *
     * @return string
     */
    public function getTipoPago()
    {
        return $this->tipoPago;
    }

    /**
     * Set valor.
     *
     * @param float $valor
     *
     * @return OrdenCompraPago
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor.
     *
     * @return float
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set referencia.
     *
     * @param string $referencia
     *
     * @return OrdenCompraPago
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
     * Set descripcion.
     *
     * @param string|null $descripcion
     *
     * @return OrdenCompraPago
     */
    public function setDescripcion($descripcion = null)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion.
     *
     * @return string|null
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set estado.
     *
     * @param int $estado
     *
     * @return OrdenCompraPago
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
     * Set ordenCompra.
     *
     * @param \AppBundle\Entity\OrdenCompra|null $ordenCompra
     *
     * @return OrdenCompraPago
     */
    public function setOrdenCompra(\AppBundle\Entity\OrdenCompra $ordenCompra = null)
    {
        $this->ordenCompra = $ordenCompra;

        return $this;
    }

    /**
     * Get ordenCompra.
     *
     * @return \AppBundle\Entity\OrdenCompra|null
     */
    public function getOrdenCompra()
    {
        return $this->ordenCompra;
    }

    /**
     * Set usuarioCreacion.
     *
     * @param \AppBundle\Entity\FosUser|null $usuarioCreacion
     *
     * @return OrdenCompraPago
     */
    public function setUsuarioCreacion(\AppBundle\Entity\FosUser $usuarioCreacion = null)
    {
        $this->usuarioCreacion = $usuarioCreacion;

        return $this;
    }

    /**
     * Get usuarioCreacion.
     *
     * @return \AppBundle\Entity\FosUser|null
     */
    public function getUsuarioCreacion()
    {
        return $this->usuarioCreacion;
    }
}
