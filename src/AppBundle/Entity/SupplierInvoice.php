<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * SupplierInvoice
 */
class SupplierInvoice
{
    const STATUS_DRAFT = 1;
    const STATUS_CONFIRMED = 2;
    const STATUS_PAID = 3;
    const STATUS_CANCELLED = 0;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $invoiceNumber;

    /**
     * @var string|null
     */
    private $internalReference;

    /**
     * @var \DateTime
     */
    private $issueDate;

    /**
     * @var \DateTime|null
     */
    private $dueDate;

    /**
     * @var string
     */
    private $currency = 'COP';

    /**
     * @var string
     */
    private $exchangeRate = '1.0000';

    /**
     * @var string
     */
    private $subtotal = '0.00';

    /**
     * @var string
     */
    private $taxAmount = '0.00';

    /**
     * @var string
     */
    private $logisticCosts = '0.00';

    /**
     * @var string
     */
    private $totalAmount = '0.00';

    /**
     * @var string|null
     */
    private $attachmentPath;

    /**
     * @var string|null
     */
    private $notes;

    /**
     * @var int
     */
    private $estado = self::STATUS_DRAFT;

    /**
     * @var bool
     */
    private $inventoryUpdated = false;

    /**
     * @var \DateTime
     */
    private $fechaCreacion;

    /**
     * @var \DateTime|null
     */
    private $fechaActualizacion;

    /**
     * @var \AppBundle\Entity\Proveedor
     */
    private $proveedor;

    /**
     * @var \AppBundle\Entity\FosUser
     */
    private $usuarioCreacion;

    /**
     * @var \AppBundle\Entity\FosUser
     */
    private $usuarioActualizacion;

    /**
     * @var Collection
     */
    private $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->fechaCreacion = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setInvoiceNumber($invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;
        return $this;
    }

    public function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }

    public function setInternalReference($internalReference)
    {
        $this->internalReference = $internalReference;
        return $this;
    }

    public function getInternalReference()
    {
        return $this->internalReference;
    }

    public function setIssueDate($issueDate)
    {
        $this->issueDate = $issueDate;
        return $this;
    }

    public function getIssueDate()
    {
        return $this->issueDate;
    }

    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function getDueDate()
    {
        return $this->dueDate;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;
        return $this;
    }

    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;
        return $this;
    }

    public function getSubtotal()
    {
        return $this->subtotal;
    }

    public function setTaxAmount($taxAmount)
    {
        $this->taxAmount = $taxAmount;
        return $this;
    }

    public function getTaxAmount()
    {
        return $this->taxAmount;
    }

    public function setLogisticCosts($logisticCosts)
    {
        $this->logisticCosts = $logisticCosts;
        return $this;
    }

    public function getLogisticCosts()
    {
        return $this->logisticCosts;
    }

    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    public function setAttachmentPath($attachmentPath)
    {
        $this->attachmentPath = $attachmentPath;
        return $this;
    }

    public function getAttachmentPath()
    {
        return $this->attachmentPath;
    }

    public function setNotes($notes)
    {
        $this->notes = $notes;
        return $this;
    }

    public function getNotes()
    {
        return $this->notes;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
        return $this;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getEstadoText()
    {
        $states = [
            self::STATUS_CANCELLED => 'Cancelada',
            self::STATUS_DRAFT => 'Borrador',
            self::STATUS_CONFIRMED => 'Confirmada',
            self::STATUS_PAID => 'Pagada'
        ];
        return $states[$this->estado] ?? 'Desconocido';
    }

    public function setInventoryUpdated($inventoryUpdated)
    {
        $this->inventoryUpdated = $inventoryUpdated;
        return $this;
    }

    public function getInventoryUpdated()
    {
        return $this->inventoryUpdated;
    }

    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;
        return $this;
    }

    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;
        return $this;
    }

    public function getFechaActualizacion()
    {
        return $this->fechaActualizacion;
    }

    public function setProveedor(\AppBundle\Entity\Proveedor $proveedor = null)
    {
        $this->proveedor = $proveedor;
        return $this;
    }

    public function getProveedor()
    {
        return $this->proveedor;
    }

    public function setUsuarioCreacion(\AppBundle\Entity\FosUser $usuarioCreacion = null)
    {
        $this->usuarioCreacion = $usuarioCreacion;
        return $this;
    }

    public function getUsuarioCreacion()
    {
        return $this->usuarioCreacion;
    }

    public function setUsuarioActualizacion(\AppBundle\Entity\FosUser $usuarioActualizacion = null)
    {
        $this->usuarioActualizacion = $usuarioActualizacion;
        return $this;
    }

    public function getUsuarioActualizacion()
    {
        return $this->usuarioActualizacion;
    }

    public function addItem(\AppBundle\Entity\SupplierInvoiceItem $item)
    {
        $this->items[] = $item;
        $item->setSupplierInvoice($this);
        return $this;
    }

    public function removeItem(\AppBundle\Entity\SupplierInvoiceItem $item)
    {
        $this->items->removeElement($item);
        $item->setSupplierInvoice(null);
    }

    public function getItems()
    {
        return $this->items;
    }

    /**
     * Calcula los totales de la factura basándose en los items
     */
    public function calculateTotals()
    {
        $subtotal = 0;
        foreach ($this->items as $item) {
            $subtotal += floatval($item->getTotalPrice());
        }
        
        $this->subtotal = number_format($subtotal, 2, '.', '');
        $this->totalAmount = number_format($subtotal + floatval($this->taxAmount) + floatval($this->logisticCosts), 2, '.', '');
        
        return $this;
    }

    public function __toString()
    {
        return $this->invoiceNumber . ' - ' . ($this->proveedor ? $this->proveedor->getNombre() : 'Sin proveedor');
    }
}