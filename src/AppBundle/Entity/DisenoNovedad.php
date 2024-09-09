<?php

namespace AppBundle\Entity;

/**
 * DisenoNovedad
 */
class DisenoNovedad
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
     * @var \AppBundle\Entity\Diseno
     */
    private $diseno;

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
     * @return DisenoNovedad
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
     * @return DisenoNovedad
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
     * @return DisenoNovedad
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
     * @return DisenoNovedad
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
     * @return DisenoNovedad
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
     * @return DisenoNovedad
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
     * @return DisenoNovedad
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
     * Set diseno.
     *
     * @param \AppBundle\Entity\Diseno|null $diseno
     *
     * @return DisenoNovedad
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
     * Set usuarioCreacion.
     *
     * @param \AppBundle\Entity\FosUser|null $usuarioCreacion
     *
     * @return DisenoNovedad
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
