<?php

namespace AppBundle\Entity;

class DisenoOrden{

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
     * @var int
     */
    private $cantidad;

    /**
     * @var string
     */
    private $notas;

    /**
     * @var \DateTime
     */
    private $fechaCreacion;

    /**
     * @var int
     */
    private $estado;

    /**
     * @var \DateTime|null
     */
    private $fechaFinalizacion;

    /**
     * @var int|null
     */
    private $duracion;

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
     * Set referencia.
     *
     * @param string $referencia
     *
     * @return DisenoOrden
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
     * Set cantidad.
     *
     * @param int $cantidad
     *
     * @return DisenoOrden
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
     * Set notas.
     *
     * @param string $notas
     *
     * @return DisenoOrden
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
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return DisenoOrden
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
     * Set estado.
     *
     * @param int $estado
     *
     * @return DisenoOrden
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
     * Set fechaFinalizacion.
     *
     * @param \DateTime|null $fechaFinalizacion
     *
     * @return DisenoOrden
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
     * @return DisenoOrden
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
     * Set usuarioCreacion.
     *
     * @param \AppBundle\Entity\FosUser|null $usuarioCreacion
     *
     * @return DisenoOrden
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

    private function createDeleteForm($orden) {
        return $this->createFormBuilder(array('account_id' => $id))->add('account_id', 'hidden')->getForm();
}
}
