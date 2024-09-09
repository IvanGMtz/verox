<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
/**
 * Diseno
 */
class Diseno
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $referencia;

    /**
     * @var string
     */
    private $nombre;

    /**
     * @var string
     */
    private $categoria;

    /**
     * @var string
     */
    private $talla;

    /**
     * @var string
     */
    private $costoCorte;

    /**
     * @var \DateTime
     */
    private $fechaCreacion;

    /**
     * @var int
     */
    private $estado = 1;

    /**
     * @var int
     */
    private $presillas = 0;

    /**
     * @var int
     */
    private $ojales = 0;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $imagenes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */ 
    private $materiales;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
     private $procesos;

     /**
      * @var \Doctrine\Common\Collections\Collection
      */

    private $novedades;

    /**
     * @var \AppBundle\Entity\FosUser
     */
    private $usuarioCreacion;

    /**
     * @var \AppBundle\Entity\DisenoOrden
     */
    private $orden;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->imagenes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->materiales = new \Doctrine\Common\Collections\ArrayCollection();
        $this->procesos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->novedades = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set referencia.
     *
     * @param string $referencia
     *
     * @return Diseno
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
     * Set nombre.
     *
     * @param string $nombre
     *
     * @return Diseno
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
     * Set talla.
     *
     * @param string $talla
     *
     * @return Diseno
     */
    public function setTalla($talla)
    {
        $this->talla = $talla;

        return $this;
    }

    /**
     * Get talla.
     *
     * @return string
     */
    public function getTalla()
    {
        return $this->talla;
    }

    /**
     * Set costoCorte.
     *
     * @param string $costoCorte
     *
     * @return Diseno
     */
    public function setCostoCorte($costoCorte)
    {
        $this->costoCorte = $costoCorte;

        return $this;
    }

    /**
     * Get costoCorte.
     *
     * @return string
     */
    public function getCostoCorte()
    {
        return $this->costoCorte;
    }

    /**
     * Set categoria.
     *
     * @param string $categoria
     *
     * @return Diseno
     */
    public function setCategoria($categoria)
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * Get categoria.
     *
     * @return string
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Diseno
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
     * @return Diseno
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
     * Set presillas.
     *
     * @param int $presillas
     *
     * @return Diseno
     */
    public function setPresillas($presillas)
    {
        $this->presillas = $presillas;

        return $this;
    }

    /**
     * Get presillas.
     *
     * @return int
     */
    public function getPresillas()
    {
        return $this->presillas;
    }

    /**
     * Set ojales.
     *
     * @param int $ojales
     *
     * @return Diseno
     */
    public function setOjales($ojales)
    {
        $this->ojales = $ojales;

        return $this;
    }

    /**
     * Get ojales.
     *
     * @return int
     */
    public function getOjales()
    {
        return $this->ojales;
    }

    /**
     * Add imagene.
     *
     * @param \AppBundle\Entity\DisenoImagen $imagene
     *
     * @return Diseno
     */
    public function addImagene(\AppBundle\Entity\DisenoImagen $imagene)
    {
        if (!$this->imagenes->contains($imagene)) {
          $imagene->setDiseno($this);
          $this->imagenes[] = $imagene;
        }
 
        return $this;
    }

    /**
     * Remove imagene.
     *
     * @param \AppBundle\Entity\DisenoImagen $imagene
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeImagene(\AppBundle\Entity\DisenoImagen $imagene)
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
     * Add materiale.
     *
     * @param \AppBundle\Entity\DisenoMaterial $materiale
     *
     * @return Diseno
     */
    public function addMateriale(\AppBundle\Entity\DisenoMaterial $materiale)
    {
        if (!$this->materiales->contains($materiale)) {
          $materiale->setDiseno($this);
          $this->materiales[] = $materiale;
        }

        return $this;
    }

    /**
     * Remove materiale.
     *
     * @param \AppBundle\Entity\DisenoMaterial $materiale
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeMateriale(\AppBundle\Entity\DisenoMaterial $materiale)
    {
        return $this->materiales->removeElement($materiale);
    }

    /**
     * Get materiales.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMateriales()
    {
        return $this->materiales;
    }

    /**
     * Add proces.
     *
     * @param \AppBundle\Entity\ProcesoNombre $procesos
     *
     * @return Diseno
     */
    public function addProceso(\AppBundle\Entity\ProcesoNombre $proceso)
    {
        if (!$this->procesos->contains($proceso)) {
          $proceso->setDiseno($this);
          $this->procesos[] = $proceso;
        }

        return $this;
    }

    /**
     * Remove proceso.
     *
     * @param \AppBundle\Entity\ProcesoNombre $procesos
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeProceso(\AppBundle\Entity\ProcesoNombre $proceso)
    {
        return $this->procesos->removeElement($proceso);
    }

    /**
     * Get proceso.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProcesos()
    {
        return $this->procesos;
    }

    /**
     * Add novedade.
     *
     * @param \AppBundle\Entity\DisenoNovedad $novedade
     *
     * @return Diseno
     */
    public function addNovedade(\AppBundle\Entity\DisenoNovedad $novedade)
    {
        $this->novedades[] = $novedade;

        return $this;
    }

    /**
     * Remove novedade.
     *
     * @param \AppBundle\Entity\DisenoNovedad $novedade
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeNovedade(\AppBundle\Entity\DisenoNovedad $novedade)
    {
        return $this->novedades->removeElement($novedade);
    }

    /**
     * Get novedades.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNovedades(){
      $iterator = $this->novedades->getIterator();
      $iterator->uasort(function ($a, $b) {
          return ($a->getId() > $b->getId()) ? -1 : 1;
      });
      $collection = new ArrayCollection(iterator_to_array($iterator));
      return $collection;
//      usort($this->novedades, function($a, $b) {return strcmp($a->id, $b->id);});
//      return $this->novedades;
    }

    /**
     * Set usuarioCreacion.
     *
     * @param \AppBundle\Entity\FosUser|null $usuarioCreacion
     *
     * @return Diseno
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

    public function __toString(){
      return $this->referencia.' '.$this->nombre;
    }

    /**
     * Set orden.
     *
     * @param \AppBundle\Entity\DisenoOrden|null $orden
     *
     * @return Diseno
     */
    public function setOrden(\AppBundle\Entity\DisenoOrden $orden = null)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden.
     *
     * @return \AppBundle\Entity\DisenoOrden|null
     */
    public function getOrden()
    {
        return $this->orden;
    }
}
