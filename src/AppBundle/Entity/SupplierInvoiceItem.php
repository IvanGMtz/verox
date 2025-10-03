<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SupplierInvoiceItem
 */
class SupplierInvoiceItem
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $productoId;

    /**
     * @var int
     */
    private $productoTallaId;

    /**
     * @var int
     */
    private $productoColorId;

    /**
     * @var string
     */
    private $quantity = '0.00';

    /**
     * @var string
     */
    private $unitPrice = '0.0000';

    /**
     * @var string
     */
    private $totalPrice = '0.00';

    /**
     * @var string|null
     */
    private $notes;

    /**
     * @var \AppBundle\Entity\SupplierInvoice
     */
    private $supplierInvoice;

    public function getId()
    {
        return $this->id;
    }

    public function setProductoId($productoId)
    {
        $this->productoId = $productoId;
        return $this;
    }

    public function getProductoId()
    {
        return $this->productoId;
    }

    public function setProductoTallaId($productoTallaId)
    {
        $this->productoTallaId = $productoTallaId;
        return $this;
    }

    public function getProductoTallaId()
    {
        return $this->productoTallaId;
    }

    public function setProductoColorId($productoColorId)
    {
        $this->productoColorId = $productoColorId;
        return $this;
    }

    public function getProductoColorId()
    {
        return $this->productoColorId;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        $this->calculateTotal();
        return $this;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;
        $this->calculateTotal();
        return $this;
    }

    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;
        return $this;
    }

    public function getTotalPrice()
    {
        return $this->totalPrice;
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

    public function setSupplierInvoice(\AppBundle\Entity\SupplierInvoice $supplierInvoice = null)
    {
        $this->supplierInvoice = $supplierInvoice;
        return $this;
    }

    public function getSupplierInvoice()
    {
        return $this->supplierInvoice;
    }

    /**
     * Calcula el total del item automáticamente
     */
    private function calculateTotal()
    {
        if ($this->quantity && $this->unitPrice) {
            $this->totalPrice = number_format(floatval($this->quantity) * floatval($this->unitPrice), 2, '.', '');
        }
    }

    /**
     * Obtiene información del producto (solo si necesitas cargarlo)
     */
    public function getProductoInfo($em)
    {
        $producto = $em->getRepository('AppBundle:Producto')->find($this->productoId);
        $talla = $em->getRepository('AppBundle:ProductoTalla')->find($this->productoTallaId);
        $color = $em->getRepository('AppBundle:ProductoColor')->find($this->productoColorId);
        
        if ($producto && $talla && $color) {
            return [
                'producto' => $producto,
                'talla' => $talla,
                'color' => $color,
                'nombre_completo' => $producto->getReferencia() . ' - ' . $producto->getNombre() . 
                                   ' | Talla: ' . $talla->getNombre() . ' | Color: ' . $color->getNombre()
            ];
        }
        
        return null;
    }

    public function __toString()
    {
        return 'Item ID: ' . $this->id . ' - Cant: ' . $this->quantity;
    }
}