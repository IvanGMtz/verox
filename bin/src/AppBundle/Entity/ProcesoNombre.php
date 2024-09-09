<?php

namespace AppBundle\Entity;

/**
 * ProcesoNombre
 */
class ProcesoNombre
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
     * @return ProcesoNombre
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
