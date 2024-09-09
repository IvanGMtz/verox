<?php

namespace AppBundle\Entity;

/**
 * StoreBonos
 */
class StoreBonos
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $codigo;

    /**
     * @var float
     */
    private $valor;

    /**
     * @var \DateTime
     */
    private $fechaVencimiento;

    /**
     * @var string
     */
    private $estatus;

    /**
     * @var string
     */
    private $clienteTipo;

    /**
     * @var int
     */
    private $freeShipping;

    /**
     * @var \AppBundle\Entity\Producto
     */
    private $producto;

    /**
     * @var \AppBundle\Entity\StoreUsuarios
     */
    private $usuario;
    /**
     * @var \AppBundle\Entity\ProductoCategoria
     */
    private $categoria;


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
     * Set codigo.
     *
     * @param string $codigo
     *
     * @return StoreBonos
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo.
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set valor.
     *
     * @param float $valor
     *
     * @return StoreBonos
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
     * Set fechaVencimiento.
     *
     * @param \DateTime $fechaVencimiento
     *
     * @return StoreBonos
     */
    public function setFechaVencimiento($fechaVencimiento)
    {
        $this->fechaVencimiento = $fechaVencimiento;

        return $this;
    }

    /**
     * Get fechaVencimiento.
     *
     * @return \DateTime
     */
    public function getFechaVencimiento()
    {
        return $this->fechaVencimiento;
    }

    /**
     * Set estatus.
     *
     * @param string $estatus
     *
     * @return StoreBonos
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;

        return $this;
    }

    /**
     * Get estatus.
     *
     * @return string
     */
    public function getEstatus()
    {
        return $this->estatus;
    }

    /**
     * Set clienteTipo.
     *
     * @param string $clienteTipo
     *
     * @return StoreBonos
     */
    public function setCLienteTipo($clienteTipo)
    {
        $this->clienteTipo = $clienteTipo;

        return $this;
    }

    /**
     * Get clienteTipo.
     *
     * @return string
     */
    public function getCLienteTipo()
    {
        return $this->clienteTipo;
    }

    /**
     * Get freeShipping.
     *
     * @return int
     */
    public function getFreeShipping()
    {
        return $this->freeShipping;
    }

    /**
     * Set freeShipping.
     *
     * @param string $freeShipping
     *
     * @return StoreBonos
     */
    public function setFreeShipping($freeShipping)
    {
        $this->freeShipping = $freeShipping;

        return $this;
    }

    /**
     * Set producto.
     *
     * @param \AppBundle\Entity\Producto|null $producto
     *
     * @return StoreBonos
     */
    public function setProducto(\AppBundle\Entity\Producto $producto = null)
    {
        $this->producto = $producto;

        return $this;
    }

    /**
     * Get producto.
     *
     * @return \AppBundle\Entity\Producto|null
     */
    public function getProducto()
    {
        return $this->producto;
    }

    /**
     * Set usuario.
     *
     * @param \AppBundle\Entity\StoreUsuarios|null $usuario
     *
     * @return StoreBonos
     */
    public function setUsuario(\AppBundle\Entity\StoreUsuarios $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario.
     *
     * @return \AppBundle\Entity\StoreUsuarios|null
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set categoria.
     *
     * @param \AppBundle\Entity\ProductoCategoria|null $categoria
     *
     * @return StoreBonos
     */
    public function setCategoria(\AppBundle\Entity\ProductoCategoria $categoria = null)
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * Get categoria.
     *
     * @return \AppBundle\Entity\ProductoCategoria|null
     */
    public function getCategoria()
    {
        return $this->categoria;
    }
}
