<?php

namespace AppBundle\Entity;

/**
 * Proveedor
 */
class Proveedor
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
     * @var string|null
     */
    private $nit;

    /**
     * @var string|null
     */
    private $direccion;

    /**
     * @var string|null
     */
    private $telefono1;

    /**
     * @var string|null
     */
    private $telefono2;

    /**
     * @var string|null
     */
    private $email;

    /**
     * @var string|null
     */
    private $nombreContacto;

    /**
     * @var string|null
     */
    private $telefonoContacto;

    /**
     * @var \DateTime
     */
    private $fechaCreacion;

    /**
     * @var int
     */
    private $estado = '1';

    /**
     * @var \AppBundle\Entity\City
     */
    private $ciudad;

    /**
     * @var \AppBundle\Entity\Country
     */
    private $pais;

    /**
     * @var \AppBundle\Entity\FosUser
     */
    private $usuarioCreacion;


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
     * @return Proveedor
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
     * Set nit.
     *
     * @param string|null $nit
     *
     * @return Proveedor
     */
    public function setNit($nit = null)
    {
        $this->nit = $nit;

        return $this;
    }

    /**
     * Get nit.
     *
     * @return string|null
     */
    public function getNit()
    {
        return $this->nit;
    }

    /**
     * Set direccion.
     *
     * @param string|null $direccion
     *
     * @return Proveedor
     */
    public function setDireccion($direccion = null)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion.
     *
     * @return string|null
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set telefono1.
     *
     * @param string|null $telefono1
     *
     * @return Proveedor
     */
    public function setTelefono1($telefono1 = null)
    {
        $this->telefono1 = $telefono1;

        return $this;
    }

    /**
     * Get telefono1.
     *
     * @return string|null
     */
    public function getTelefono1()
    {
        return $this->telefono1;
    }

    /**
     * Set telefono2.
     *
     * @param string|null $telefono2
     *
     * @return Proveedor
     */
    public function setTelefono2($telefono2 = null)
    {
        $this->telefono2 = $telefono2;

        return $this;
    }

    /**
     * Get telefono2.
     *
     * @return string|null
     */
    public function getTelefono2()
    {
        return $this->telefono2;
    }

    /**
     * Set email.
     *
     * @param string|null $email
     *
     * @return Proveedor
     */
    public function setEmail($email = null)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set nombreContacto.
     *
     * @param string|null $nombreContacto
     *
     * @return Proveedor
     */
    public function setNombreContacto($nombreContacto = null)
    {
        $this->nombreContacto = $nombreContacto;

        return $this;
    }

    /**
     * Get nombreContacto.
     *
     * @return string|null
     */
    public function getNombreContacto()
    {
        return $this->nombreContacto;
    }

    /**
     * Set telefonoContacto.
     *
     * @param string|null $telefonoContacto
     *
     * @return Proveedor
     */
    public function setTelefonoContacto($telefonoContacto = null)
    {
        $this->telefonoContacto = $telefonoContacto;

        return $this;
    }

    /**
     * Get telefonoContacto.
     *
     * @return string|null
     */
    public function getTelefonoContacto()
    {
        return $this->telefonoContacto;
    }

    /**
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Proveedor
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
     * @return Proveedor
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
     * Set ciudad.
     *
     * @param \AppBundle\Entity\City|null $ciudad
     *
     * @return Proveedor
     */
    public function setCiudad(\AppBundle\Entity\City $ciudad = null)
    {
        $this->ciudad = $ciudad;

        return $this;
    }

    /**
     * Get ciudad.
     *
     * @return \AppBundle\Entity\City|null
     */
    public function getCiudad()
    {
        return $this->ciudad;
    }

    /**
     * Set pais.
     *
     * @param \AppBundle\Entity\Country|null $pais
     *
     * @return Proveedor
     */
    public function setPais(\AppBundle\Entity\Country $pais = null)
    {
        $this->pais = $pais;

        return $this;
    }

    /**
     * Get pais.
     *
     * @return \AppBundle\Entity\Country|null
     */
    public function getPais()
    {
        return $this->pais;
    }

    /**
     * Set usuarioCreacion.
     *
     * @param \AppBundle\Entity\FosUser|null $usuarioCreacion
     *
     * @return Proveedor
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
      $resp = $this->nombre;
      if($this->nit){$resp .= ' ('.$this->nit.')';}
      return $resp;
    }
}
