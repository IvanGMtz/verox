<?php

namespace AppBundle\Entity;

class ProcesoNombre{

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
