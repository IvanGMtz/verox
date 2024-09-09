<?php

namespace AppBundle\Entity;

/**
 * InventarioOrdenNovedad
 */
class InventarioOrdenNovedad
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
     * @var bool
     */
    private $tienePendientes = 0;

    /**
     * @var string|null
     */
    private $anotaciones;

    /**
     * @var \AppBundle\Entity\InventarioOrden
     */
    private $inventarioOrden;

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
     * @return InventarioOrdenNovedad
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
     * @return InventarioOrdenNovedad
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
     * @return InventarioOrdenNovedad
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
     * @return InventarioOrdenNovedad
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
     * @return InventarioOrdenNovedad
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
     * Set tienePendientes.
     *
     * @param bool $tienePendientes
     *
     * @return InventarioOrdenNovedad
     */
    public function setTienePendientes($tienePendientes)
    {
        $this->tienePendientes = $tienePendientes;

        return $this;
    }

    /**
     * Get tienePendientes.
     *
     * @return bool
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
     * @return InventarioOrdenNovedad
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

    /**
     * Set inventarioOrden.
     *
     * @param \AppBundle\Entity\InventarioOrden|null $inventarioOrden
     *
     * @return InventarioOrdenNovedad
     */
    public function setInventarioOrden(\AppBundle\Entity\InventarioOrden $inventarioOrden = null)
    {
        $this->inventarioOrden = $inventarioOrden;

        return $this;
    }

    /**
     * Get inventarioOrden.
     *
     * @return \AppBundle\Entity\InventarioOrden|null
     */
    public function getInventarioOrden()
    {
        return $this->inventarioOrden;
    }

    /**
     * Set usuarioCreacion.
     *
     * @param \AppBundle\Entity\FosUser|null $usuarioCreacion
     *
     * @return InventarioOrdenNovedad
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
