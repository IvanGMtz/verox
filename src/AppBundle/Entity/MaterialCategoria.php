<?php

namespace AppBundle\Entity;

/**
 * MaterialCategoria
 */
class MaterialCategoria
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $nombre;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $materiales;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->materiales = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return MaterialCategoria
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
     * Add materiale.
     *
     * @param \AppBundle\Entity\Material $materiale
     *
     * @return MaterialCategoria
     */
    public function addMateriale(\AppBundle\Entity\Material $materiale)
    {
        $this->materiales[] = $materiale;

        return $this;
    }

    /**
     * Remove materiale.
     *
     * @param \AppBundle\Entity\Material $materiale
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeMateriale(\AppBundle\Entity\Material $materiale)
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
    
    public function __toString() {
      return $this->nombre;
    }
}
