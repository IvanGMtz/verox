<?php

namespace AppBundle\Entity;

/**
 * MaterialMedida
 */
class MaterialMedida
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
     * @return MaterialMedida
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
