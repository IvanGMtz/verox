<?php

namespace AppBundle\Entity;

/**
 * TiendaCliente
 */
class TiendaCliente
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
        return $this->nombre." ".$this->apellido;
    }

    /**
     * @var string
     */
    private $apellido;

    /**
     * @var int
     */
    private $identificacion;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $telefono;


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
     * @return TiendaCliente
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
     * Set apellido.
     *
     * @param string $apellido
     *
     * @return TiendaCliente
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * Get apellido.
     *
     * @return string
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set identificacion.
     *
     * @param int $identificacion
     *
     * @return TiendaCliente
     */
    public function setIdentificacion($identificacion)
    {
        $this->identificacion = $identificacion;

        return $this;
    }

    /**
     * Get identificacion.
     *
     * @return int
     */
    public function getIdentificacion()
    {
        return $this->identificacion;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return TiendaCliente
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set telefono.
     *
     * @param string $telefono
     *
     * @return TiendaCliente
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono.
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }
}
