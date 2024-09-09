<?php

namespace AppBundle\Entity;

/**
 * InventarioMovimiento
 */
class InventarioMovimiento
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var float
     */
    private $cantidad;

    /**
     * @var string
     */
    private $tipo;

    /**
     * @var string|null
     */
    private $descripcion;

    /**
     * @var string|null
     */
    private $ref1;

    /**
     * @var string|null
     */
    private $ref2;

    /**
     * @var float|null
     */
    private $ref3;

    /**
     * @var \DateTime
     */
    private $fechaCreacion;

    /**
     * @var int
     */
    private $estado = 1;

    /**
     * @var \AppBundle\Entity\Almacen
     */
    private $almacen;

    /**
     * @var \AppBundle\Entity\AlmacenZona
     */
    private $almacenZona;

    /**
     * @var \AppBundle\Entity\AlmacenZonaInventario
     */
    private $almacenZonaInventario;

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
     * Set cantidad.
     *
     * @param float $cantidad
     *
     * @return InventarioMovimiento
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad.
     *
     * @return float
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set tipo.
     *
     * @param string $tipo
     *
     * @return InventarioMovimiento
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
     * Set descripcion.
     *
     * @param string|null $descripcion
     *
     * @return InventarioMovimiento
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
     * Set ref1.
     *
     * @param string|null $ref1
     *
     * @return InventarioMovimiento
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
     * @return InventarioMovimiento
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
     * Set ref3.
     *
     * @param float|null $ref3
     *
     * @return InventarioMovimiento
     */
    public function setRef3($ref3 = null)
    {
        $this->ref3 = $ref3;

        return $this;
    }

    /**
     * Get ref3.
     *
     * @return float|null
     */
    public function getRef3()
    {
        return $this->ref3;
    }

    /**
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return InventarioMovimiento
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
     * @return InventarioMovimiento
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
     * Set almacen.
     *
     * @param \AppBundle\Entity\Almacen|null $almacen
     *
     * @return InventarioMovimiento
     */
    public function setAlmacen(\AppBundle\Entity\Almacen $almacen = null)
    {
        $this->almacen = $almacen;

        return $this;
    }

    /**
     * Get almacen.
     *
     * @return \AppBundle\Entity\Almacen|null
     */
    public function getAlmacen()
    {
        return $this->almacen;
    }

    /**
     * Set almacenZona.
     *
     * @param \AppBundle\Entity\AlmacenZona|null $almacenZona
     *
     * @return InventarioMovimiento
     */
    public function setAlmacenZona(\AppBundle\Entity\AlmacenZona $almacenZona = null)
    {
        $this->almacenZona = $almacenZona;

        return $this;
    }

    /**
     * Get almacenZona.
     *
     * @return \AppBundle\Entity\AlmacenZona|null
     */
    public function getAlmacenZona()
    {
        return $this->almacenZona;
    }

    /**
     * Set almacenZonaInventario.
     *
     * @param \AppBundle\Entity\AlmacenZonaInventario|null $almacenZonaInventario
     *
     * @return InventarioMovimiento
     */
    public function setAlmacenZonaInventario(\AppBundle\Entity\AlmacenZonaInventario $almacenZonaInventario = null)
    {
        $this->almacenZonaInventario = $almacenZonaInventario;

        return $this;
    }

    /**
     * Get almacenZonaInventario.
     *
     * @return \AppBundle\Entity\AlmacenZonaInventario|null
     */
    public function getAlmacenZonaInventario()
    {
        return $this->almacenZonaInventario;
    }

    /**
     * Set usuarioCreacion.
     *
     * @param \AppBundle\Entity\FosUser|null $usuarioCreacion
     *
     * @return InventarioMovimiento
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
     * @var \AppBundle\Entity\Material
     */
    private $material;


    /**
     * Set material.
     *
     * @param \AppBundle\Entity\Material|null $material
     *
     * @return InventarioMovimiento
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
    /**
     * @var float
     */
    private $valorUnitario = 0;

    /**
     * @var float
     */
    private $valorTotal = 0;


    /**
     * Set valorUnitario.
     *
     * @param float $valorUnitario
     *
     * @return InventarioMovimiento
     */
    public function setValorUnitario($valorUnitario)
    {
        $this->valorUnitario = $valorUnitario;

        return $this;
    }

    /**
     * Get valorUnitario.
     *
     * @return float
     */
    public function getValorUnitario()
    {
        return $this->valorUnitario;
    }

    /**
     * Set valorTotal.
     *
     * @param float $valorTotal
     *
     * @return InventarioMovimiento
     */
    public function setValorTotal($valorTotal)
    {
        $this->valorTotal = $valorTotal;

        return $this;
    }

    /**
     * Get valorTotal.
     *
     * @return float
     */
    public function getValorTotal()
    {
        return $this->valorTotal;
    }
}
