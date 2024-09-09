<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
/**
 * Producto
 */
class Producto
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $nombre;
    public function __toString() {
        return $this->nombre;
    }

    /**
     * @var float
     */
    private $precioMayorista;

    /**
     * @var float
     */
    private $precioDetal;

    /**
     * @var string
     */
    private $referencia;
    /**
     * @var string
     */
    private $etiqueta;
    /**
     * @var string
     */
    private $marca;

    /**
     * @var string
     */
    private $estado;

    /**
     * @var \AppBundle\Entity\ProductoCategoria
     */
    private $categoria;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $imagenes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $descripciones;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $tallas;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $colores;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $complementos;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->imagenes = new ArrayCollection();
        $this->tallas = new ArrayCollection();
        $this->colores = new ArrayCollection();
        $this->descripciones = new ArrayCollection();
        $this->complementos = new ArrayCollection();
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
     * Set nombre.
     *
     * @param string $nombre
     *
     * @return Producto
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre.
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set precioMayorista.
     *
     * @param float $precioMayorista
     *
     * @return Producto
     */
    public function setPrecioMayorista($precioMayorista)
    {
        $this->precioMayorista = $precioMayorista;

        return $this;
    }

    /**
     * Get precioMayorista.
     *
     * @return float
     */
    public function getPrecioMayorista()
    {
        return $this->precioMayorista;
    }

    /**
     * Set precioDetal.
     *
     * @param float $precioDetal
     *
     * @return Producto
     */
    public function setPrecioDetal($precioDetal)
    {
        $this->precioDetal = $precioDetal;

        return $this;
    }

    /**
     * Get precioDetal.
     *
     * @return float
     */
    public function getPrecioDetal()
    {
        return $this->precioDetal;
    }

    /**
     * Set referencia.
     *
     * @param string $referencia
     *
     * @return Producto
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
     * Set etiqueta.
     *
     * @param string $etiqueta
     *
     * @return Producto
     */
    public function setEtiqueta($etiqueta)
    {
        $this->etiqueta = $etiqueta;

        return $this;
    }

    /**
     * Get etiqueta.
     *
     * @return string
     */
    public function getEtiqueta()
    {
        return $this->etiqueta;
    }
    /**
     * Set marca.
     *
     * @param string $marca
     * @return Producto
     */
    public function setMarca($marca)
    {
        $this->marca = $marca;

        return $this;
    }

    /**
     * Get marca.
     *
     * @return string
     */
    public function getMarca()
    {
        return $this->marca;
    }

    /**
     * Set estado.
     *
     * @param string $estado
     *
     * @return Producto
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado.
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set categoria.
     *
     * @param \AppBundle\Entity\ProductoCategoria|null $categoria
     *
     * @return Producto
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

    /**
     * Add imagene.
     *
     * @param \AppBundle\Entity\ProductoImagen $imagene
     *
     * @return Producto
     */
    public function addImagene(\AppBundle\Entity\ProductoImagen $imagene)
    {
        if (!$this->imagenes->contains($imagene)) {
          $imagene->setProducto($this);
          $this->imagenes[] = $imagene;
        }
        return $this;
    }

    /**
     * Remove imagene.
     *
     * @param \AppBundle\Entity\ProductoImagen $imagene
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeImagene(\AppBundle\Entity\ProductoImagen $imagene)
    {
        return $this->imagenes->removeElement($imagene);
    }

    /**
     * Get imagenes.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImagenes()
    {
        return $this->imagenes;
    }

    /**
     * Add talla.
     *
     * @param \AppBundle\Entity\ProductoTalla $talla
     *
     * @return Producto
     */
    public function addTalla(\AppBundle\Entity\ProductoTalla $talla)
    {
        if (!$this->tallas->contains($talla)) {
          $talla->setProducto($this);
          $this->tallas[] = $talla;
        }

        return $this;
    }

    /**
     * Remove talla.
     *
     * @param \AppBundle\Entity\ProductoTalla $talla
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeTalla(\AppBundle\Entity\ProductoTalla $talla)
    {
        return $this->tallas->removeElement($talla);
    }

    /**
     * Get talla.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTallas()
    {
        return $this->tallas;
    }

    /**
     * Add complemento.
     *
     * @param \AppBundle\Entity\ProductoComplemento $complemento
     *
     * @return Producto
     */
    public function addComplemento(\AppBundle\Entity\ProductoComplemento $complemento)
    {
        if (!$this->complementos->contains($complemento)) {
            $complemento->setProducto($this);
            $this->complementos[] = $complemento;
        }
        return $this;
    }

    /**
     * Remove complemento.
     *
     * @param \AppBundle\Entity\ProductoComplemento $complemento
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeComplemento(\AppBundle\Entity\ProductoComplemento $complemento)
    {
        return $this->complementos->removeElement($complemento);
    }

    /**
     * Get complemento.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComplementos()
    {
        return $this->complementos;
    }


     /**
     * Add color.
     *
     * @param \AppBundle\Entity\ProductoColor $color
     *
     * @return Producto
     */
    public function addColor(\AppBundle\Entity\ProductoColor $color)
    {
        if (!$this->colores->contains($color)) {
          $color->setProducto($this);
          $this->colores[] = $color;
        }

        return $this;
    }

    /**
     * Remove color.
     *
     * @param \AppBundle\Entity\ProductoColor $color
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeColor(\AppBundle\Entity\ProductoColor $color)
    {
        return $this->colores->removeElement($color);
    }

    /**
     * Get color.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getColores()
    {
        return $this->colores;
    }

    /**
     * Add descripcion.
     *
     * @param \AppBundle\Entity\ProductoDescripcion $descripcione
     *
     * @return Producto
     */
    public function addDescripcion(\AppBundle\Entity\ProductoDescripcion $descripcione)
    {
        if (!$this->descripciones->contains($descripcione)) {
          $descripcione->setProducto($this);
          $this->descripciones[] = $descripcione;
        }

        return $this;
    }

    /**
     * Remove descripcion.
     *
     * @param \AppBundle\Entity\ProductoDescripcion $descripcione
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeDescripcion(\AppBundle\Entity\ProductoDescripcion $descripcione)
    {
        return $this->descripciones->removeElement($descripcione);
    }

    /**
     * Get descripcion.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDescripciones()
    {
        return $this->descripciones;
    }
    

    /**
     * Add colore.
     *
     * @param \AppBundle\Entity\ProductoColor $colore
     *
     * @return Producto
     */
    public function addColore(\AppBundle\Entity\ProductoColor $colore)
    {
        $this->colores[] = $colore;

        return $this;
    }

    /**
     * Remove colore.
     *
     * @param \AppBundle\Entity\ProductoColor $colore
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeColore(\AppBundle\Entity\ProductoColor $colore)
    {
        return $this->colores->removeElement($colore);
    }

    /**
     * Add descripcione.
     *
     * @param \AppBundle\Entity\ProductoDescripcion $descripcione
     *
     * @return Producto
     */
    public function addDescripcione(\AppBundle\Entity\ProductoDescripcion $descripcione)
    {
        $this->descripciones[] = $descripcione;

        return $this;
    }

    /**
     * Remove descripcione.
     *
     * @param \AppBundle\Entity\ProductoDescripcion $descripcione
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeDescripcione(\AppBundle\Entity\ProductoDescripcion $descripcione)
    {
        return $this->descripciones->removeElement($descripcione);
    }
}
