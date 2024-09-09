<?php

namespace AppBundle\Entity;

/**
 * OrdenCompra
 */
class OrdenCompra
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
     * @var \DateTime|null
     */
    private $fechaActualizacion;

    /**
     * @var \DateTime|null
     */
    private $fechaAceptacion;

    /**
     * @var \DateTime|null
     */
    private $fechaRecibe;

    /**
     * @var string|null
     */
    private $direccionDestino;

    /**
     * @var float
     */
    private $valorTotal = '0.00';

    /**
     * @var string|null
     */
    private $descripcion;

    /**
     * @var int
     */
    private $estado = '1';

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $novedades;

    /**
     * @var \AppBundle\Entity\Almacen
     */
    private $almacenDestino;

    /**
     * @var \AppBundle\Entity\Proveedor
     */
    private $proveedor;

    /**
     * @var \AppBundle\Entity\FosUser
     */
    private $usuarioAceptacion;

    /**
     * @var \AppBundle\Entity\FosUser
     */
    private $usuarioActualizacion;

    /**
     * @var \AppBundle\Entity\FosUser
     */
    private $usuarioCreacion;

    /**
     * @var \AppBundle\Entity\FosUser
     */
    private $usuarioRecibe;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->novedades = new \Doctrine\Common\Collections\ArrayCollection();
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @return OrdenCompra
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
     * Set fechaActualizacion.
     *
     * @param \DateTime|null $fechaActualizacion
     *
     * @return OrdenCompra
     */
    public function setFechaActualizacion($fechaActualizacion = null)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Get fechaActualizacion.
     *
     * @return \DateTime|null
     */
    public function getFechaActualizacion()
    {
        return $this->fechaActualizacion;
    }

    /**
     * Set fechaAceptacion.
     *
     * @param \DateTime|null $fechaAceptacion
     *
     * @return OrdenCompra
     */
    public function setFechaAceptacion($fechaAceptacion = null)
    {
        $this->fechaAceptacion = $fechaAceptacion;

        return $this;
    }

    /**
     * Get fechaAceptacion.
     *
     * @return \DateTime|null
     */
    public function getFechaAceptacion()
    {
        return $this->fechaAceptacion;
    }

    /**
     * Set fechaRecibe.
     *
     * @param \DateTime|null $fechaRecibe
     *
     * @return OrdenCompra
     */
    public function setFechaRecibe($fechaRecibe = null)
    {
        $this->fechaRecibe = $fechaRecibe;

        return $this;
    }

    /**
     * Get fechaRecibe.
     *
     * @return \DateTime|null
     */
    public function getFechaRecibe()
    {
        return $this->fechaRecibe;
    }

    /**
     * Set direccionDestino.
     *
     * @param string|null $direccionDestino
     *
     * @return OrdenCompra
     */
    public function setDireccionDestino($direccionDestino = null)
    {
        $this->direccionDestino = $direccionDestino;

        return $this;
    }

    /**
     * Get direccionDestino.
     *
     * @return string|null
     */
    public function getDireccionDestino()
    {
        return $this->direccionDestino;
    }

    /**
     * Set valorTotal.
     *
     * @param float $valorTotal
     *
     * @return OrdenCompra
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

    /**
     * Set descripcion.
     *
     * @param string|null $descripcion
     *
     * @return OrdenCompra
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
     * @return OrdenCompra
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
     * Add novedade.
     *
     * @param \AppBundle\Entity\OrdenCompraNovedad $novedade
     *
     * @return OrdenCompra
     */
    public function addNovedade(\AppBundle\Entity\OrdenCompraNovedad $novedade)
    {
        $this->novedades[] = $novedade;

        return $this;
    }

    /**
     * Remove novedade.
     *
     * @param \AppBundle\Entity\OrdenCompraNovedad $novedade
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeNovedade(\AppBundle\Entity\OrdenCompraNovedad $novedade)
    {
        return $this->novedades->removeElement($novedade);
    }

    /**
     * Get novedades.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNovedades()
    {
        return $this->novedades;
    }

    /**
     * Set almacenDestino.
     *
     * @param \AppBundle\Entity\Almacen|null $almacenDestino
     *
     * @return OrdenCompra
     */
    public function setAlmacenDestino(\AppBundle\Entity\Almacen $almacenDestino = null)
    {
        $this->almacenDestino = $almacenDestino;

        return $this;
    }

    /**
     * Get almacenDestino.
     *
     * @return \AppBundle\Entity\Almacen|null
     */
    public function getAlmacenDestino()
    {
        return $this->almacenDestino;
    }

    /**
     * Set proveedor.
     *
     * @param \AppBundle\Entity\Proveedor|null $proveedor
     *
     * @return OrdenCompra
     */
    public function setProveedor(\AppBundle\Entity\Proveedor $proveedor = null)
    {
        $this->proveedor = $proveedor;

        return $this;
    }

    /**
     * Get proveedor.
     *
     * @return \AppBundle\Entity\Proveedor|null
     */
    public function getProveedor()
    {
        return $this->proveedor;
    }

    /**
     * Set usuarioAceptacion.
     *
     * @param \AppBundle\Entity\FosUser|null $usuarioAceptacion
     *
     * @return OrdenCompra
     */
    public function setUsuarioAceptacion(\AppBundle\Entity\FosUser $usuarioAceptacion = null)
    {
        $this->usuarioAceptacion = $usuarioAceptacion;

        return $this;
    }

    /**
     * Get usuarioAceptacion.
     *
     * @return \AppBundle\Entity\FosUser|null
     */
    public function getUsuarioAceptacion()
    {
        return $this->usuarioAceptacion;
    }

    /**
     * Set usuarioActualizacion.
     *
     * @param \AppBundle\Entity\FosUser|null $usuarioActualizacion
     *
     * @return OrdenCompra
     */
    public function setUsuarioActualizacion(\AppBundle\Entity\FosUser $usuarioActualizacion = null)
    {
        $this->usuarioActualizacion = $usuarioActualizacion;

        return $this;
    }

    /**
     * Get usuarioActualizacion.
     *
     * @return \AppBundle\Entity\FosUser|null
     */
    public function getUsuarioActualizacion()
    {
        return $this->usuarioActualizacion;
    }

    /**
     * Set usuarioCreacion.
     *
     * @param \AppBundle\Entity\FosUser|null $usuarioCreacion
     *
     * @return OrdenCompra
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
     * Set usuarioRecibe.
     *
     * @param \AppBundle\Entity\FosUser|null $usuarioRecibe
     *
     * @return OrdenCompra
     */
    public function setUsuarioRecibe(\AppBundle\Entity\FosUser $usuarioRecibe = null)
    {
        $this->usuarioRecibe = $usuarioRecibe;

        return $this;
    }

    /**
     * Get usuarioRecibe.
     *
     * @return \AppBundle\Entity\FosUser|null
     */
    public function getUsuarioRecibe()
    {
        return $this->usuarioRecibe;
    }
    /**
     * @var float
     */
    private $valor = 0;

    /**
     * @var float
     */
    private $impuesto = 0;

    /**
     * @var float
     */
    private $valorImpuesto = 0;


    /**
     * Set valor.
     *
     * @param float $valor
     *
     * @return OrdenCompra
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
     * Set impuesto.
     *
     * @param float $impuesto
     *
     * @return OrdenCompra
     */
    public function setImpuesto($impuesto)
    {
        $this->impuesto = $impuesto;

        return $this;
    }

    /**
     * Get impuesto.
     *
     * @return float
     */
    public function getImpuesto()
    {
        return $this->impuesto;
    }

    /**
     * Set valorImpuesto.
     *
     * @param float $valorImpuesto
     *
     * @return OrdenCompra
     */
    public function setValorImpuesto($valorImpuesto)
    {
        $this->valorImpuesto = $valorImpuesto;

        return $this;
    }

    /**
     * Get valorImpuesto.
     *
     * @return float
     */
    public function getValorImpuesto()
    {
        return $this->valorImpuesto;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $items;


    /**
     * Add item.
     *
     * @param \AppBundle\Entity\OrdenCompraItem $item
     *
     * @return OrdenCompra
     */
    public function addItem(\AppBundle\Entity\OrdenCompraItem $item)
    {
        if (!$this->items->contains($item)) {
          $item->setOrdenCompra($this);
          $this->items[] = $item;
        }

        return $this;
    }

    /**
     * Remove item.
     *
     * @param \AppBundle\Entity\OrdenCompraItem $item
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeItem(\AppBundle\Entity\OrdenCompraItem $item)
    {
        return $this->items->removeElement($item);
    }

    /**
     * Get items.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }
    /**
     * @var string|null
     */
    private $metodoPago;


    /**
     * Set metodoPago.
     *
     * @param string|null $metodoPago
     *
     * @return OrdenCompra
     */
    public function setMetodoPago($metodoPago = null)
    {
        $this->metodoPago = $metodoPago;

        return $this;
    }

    /**
     * Get metodoPago.
     *
     * @return string|null
     */
    public function getMetodoPago()
    {
        return $this->metodoPago;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $pagos;


    /**
     * Add pago.
     *
     * @param \AppBundle\Entity\OrdenCompraPago $pago
     *
     * @return OrdenCompra
     */
    public function addPago(\AppBundle\Entity\OrdenCompraPago $pago)
    {
        $this->pagos[] = $pago;

        return $this;
    }

    /**
     * Remove pago.
     *
     * @param \AppBundle\Entity\OrdenCompraPago $pago
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePago(\AppBundle\Entity\OrdenCompraPago $pago)
    {
        return $this->pagos->removeElement($pago);
    }

    /**
     * Get pagos.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPagos()
    {
        return $this->pagos;
    }
    /**
     * @var float
     */
    private $valorPagado = 0;

    /**
     * @var float
     */
    private $valorSaldo;


    /**
     * Set valorPagado.
     *
     * @param float $valorPagado
     *
     * @return OrdenCompra
     */
    public function setValorPagado($valorPagado)
    {
        $this->valorPagado = $valorPagado;

        return $this;
    }

    /**
     * Get valorPagado.
     *
     * @return float
     */
    public function getValorPagado()
    {
        return $this->valorPagado;
    }

    /**
     * Set valorSaldo.
     *
     * @param float $valorSaldo
     *
     * @return OrdenCompra
     */
    public function setValorSaldo($valorSaldo)
    {
        $this->valorSaldo = $valorSaldo;

        return $this;
    }

    /**
     * Get valorSaldo.
     *
     * @return float
     */
    public function getValorSaldo()
    {
        return $this->valorSaldo;
    }
    /**
     * @var bool|null
     */
    private $pagada = 0;

    /**
     * @var \DateTime|null
     */
    private $fechaPagada;


    /**
     * Set pagada.
     *
     * @param bool|null $pagada
     *
     * @return OrdenCompra
     */
    public function setPagada($pagada = null)
    {
        $this->pagada = $pagada;

        return $this;
    }

    /**
     * Get pagada.
     *
     * @return bool|null
     */
    public function getPagada()
    {
        return $this->pagada;
    }

    /**
     * Set fechaPagada.
     *
     * @param \DateTime|null $fechaPagada
     *
     * @return OrdenCompra
     */
    public function setFechaPagada($fechaPagada = null)
    {
        $this->fechaPagada = $fechaPagada;

        return $this;
    }

    /**
     * Get fechaPagada.
     *
     * @return \DateTime|null
     */
    public function getFechaPagada()
    {
        return $this->fechaPagada;
    }
    /**
     * @var bool|null
     */
    private $tienePendientes = 0;


    /**
     * Set tienePendientes.
     *
     * @param bool|null $tienePendientes
     *
     * @return OrdenCompra
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
}
