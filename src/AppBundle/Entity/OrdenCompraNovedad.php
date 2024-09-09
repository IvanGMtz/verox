<?php

namespace AppBundle\Entity;

/**
 * OrdenCompraNovedad
 */
class OrdenCompraNovedad
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
     * @var string|null
     */
    private $descripcion;

    /**
     * @var string
     */
    private $tipo;

    /**
     * @var string|null
     */
    private $ref1;

    /**
     * @var string|null
     */
    private $ref2;

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
     * @return OrdenCompraNovedad
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
     * Set descripcion.
     *
     * @param string|null $descripcion
     *
     * @return OrdenCompraNovedad
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
     * Set tipo.
     *
     * @param string $tipo
     *
     * @return OrdenCompraNovedad
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
     * Set ref1.
     *
     * @param string|null $ref1
     *
     * @return OrdenCompraNovedad
     */
    public function setRef1($ref1 = null)
    {
        $this->ref1 = $ref1;

        return $this;
    }

    /**
     * Get ref1.
     *
     * @return string|null
     */
    public function getRef1()
    {
        return $this->ref1;
    }

    /**
     * Set ref2.
     *
     * @param string|null $ref2
     *
     * @return OrdenCompraNovedad
     */
    public function setRef2($ref2 = null)
    {
        $this->ref2 = $ref2;

        return $this;
    }

    /**
     * Get ref2.
     *
     * @return string|null
     */
    public function getRef2()
    {
        return $this->ref2;
    }

    /**
     * Set ordenCompra.
     *
     * @param \AppBundle\Entity\OrdenCompra|null $ordenCompra
     *
     * @return OrdenCompraNovedad
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
     * @return OrdenCompraNovedad
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
    /**
     * @var bool|null
     */
    private $tienePendientes = 0;

    /**
     * @var string|null
     */
    private $anotaciones;


    /**
     * Set tienePendientes.
     *
     * @param bool|null $tienePendientes
     *
     * @return OrdenCompraNovedad
     */
    public function setTienePendientes($tienePendientes = null)
    {
        $this->tienePendientes = $tienePendientes;

        return $this;
    }

    /**
     * Get tienePendientes.
     *
     * @return bool|null
     */
    public function getTienePendientes()
    {
        return $this->tienePendientes;
    }

    /**
     * Set anotaciones.
     *
     * @param string|null $anotaciones
     *
     * @return OrdenCompraNovedad
     */
    public function setAnotaciones($anotaciones = null)
    {
        $this->anotaciones = $anotaciones;

        return $this;
    }

    /**
     * Get anotaciones.
     *
     * @return string|null
     */
    public function getAnotaciones()
    {
        return $this->anotaciones;
    }
}
