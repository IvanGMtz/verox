<?php

namespace AppBundle\Entity;

/**
 * MaterialNombre
 */
class MaterialNombre
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
     * @return MaterialNombre
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
}
