<?php

namespace AppBundle\Entity;

/**
 * AlmacenZona
 */
class AlmacenZona
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $ubicacion;

    /**
     * @var string
     */
    private $estante;

    /**
     * @var string|null
     */
    private $bandeja;

    /**
     * @var \DateTime
     */
    private $fechaCreacion;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $inventario;

    /**
     * @var \AppBundle\Entity\Almacen
     */
    private $almacen;

    /**
     * @var \AppBundle\Entity\FosUser
     */
    private $usuarioCreacion;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->inventario = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set ubicacion.
     *
     * @param string $ubicacion
     *
     * @return AlmacenZona
     */
    public function setUbicacion($ubicacion)
    {
        $this->ubicacion = $ubicacion;

        return $this;
    }

    /**
     * Get ubicacion.
     *
     * @return string
     */
    public function getUbicacion()
    {
        return $this->ubicacion;
    }

    /**
     * Set estante.
     *
     * @param string $estante
     *
     * @return AlmacenZona
     */
    public function setEstante($estante)
    {
        $this->estante = $estante;

        return $this;
    }

    /**
     * Get estante.
     *
     * @return string
     */
    public function getEstante()
    {
        return $this->estante;
    }

    /**
     * Set bandeja.
     *
     * @param string|null $bandeja
     *
     * @return AlmacenZona
     */
    public function setBandeja($bandeja = null)
    {
        $this->bandeja = $bandeja;

        return $this;
    }

    /**
     * Get bandeja.
     *
     * @return string|null
     */
    public function getBandeja()
    {
        return $this->bandeja;
    }

    /**
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return AlmacenZona
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
     * Add inventario.
     *
     * @param \AppBundle\Entity\AlmacenZonaInventario $inventario
     *
     * @return AlmacenZona
     */
    public function addInventario(\AppBundle\Entity\AlmacenZonaInventario $inventario)
    {
        $this->inventario[] = $inventario;

        return $this;
    }

    /**
     * Remove inventario.
     *
     * @param \AppBundle\Entity\AlmacenZonaInventario $inventario
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeInventario(\AppBundle\Entity\AlmacenZonaInventario $inventario)
    {
        return $this->inventario->removeElement($inventario);
    }

    /**
     * Get inventario.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInventario()
    {
        return $this->inventario;
    }

    /**
     * Set almacen.
     *
     * @param \AppBundle\Entity\Almacen|null $almacen
     *
     * @return AlmacenZona
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
     * Set usuarioCreacion.
     *
     * @param \AppBundle\Entity\FosUser|null $usuarioCreacion
     *
     * @return AlmacenZona
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
    
    
    public function __toString() {
      $response = $this->ubicacion;
      if($this->estante){$response .= ' - '.$this->estante;}
      if($this->bandeja){$response .= ' - '.$this->bandeja;}
      return $response;
    }
}
