<?php

namespace AppBundle\Entity;

/**
 * StoreUsuarios
 */
class StoreUsuarios
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $email;
    public function __toString() {
        return $this->email;
    }


    /**
     * @var string|null
     */
    private $nombre;

    /**
     * @var string|null
     */
    private $apellidos;
    /**
     * @var string|null
     */
    private $idn;

    /**
     * @var string|null
     */
    private $telefono;
    /**
     * @var string|null
     */
    private $birthday;

    /**
     * @var string|null
     */
    private $direccion;

    /**
     * @var string|null
     */
    private $estado;

    /**
     * @var string
     */
    private $tipo;

    /**
     * @var int
     */
    private $webId;

    /**
     * @var string
     */
    private $asesor;


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
     * Set email.
     *
     * @param string $email
     *
     * @return StoreUsuarios
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
     * Set nombre.
     *
     * @param string|null $nombre
     *
     * @return StoreUsuarios
     */
    public function setNombre($nombre = null)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre.
     *
     * @return string|null
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set webId.
     *
     * @param integer|null $webId
     *
     * @return StoreUsuarios
     */
    public function setWebId($webId = null)
    {
        $this->webId = $webId;

        return $this;
    }

    /**
     * Get webId.
     *
     * @return integer|null
     */
    public function getWebId()
    {
        return $this->webId;
    }

    /**
     * Set apellidos.
     *
     * @param string|null $apellidos
     *
     * @return StoreUsuarios
     */
    public function setApellidos($apellidos = null)
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    /**
     * Get apellidos.
     *
     * @return string|null
     */
    public function getApellidos()
    {
        return $this->apellidos;
    }
    /**
     * Set idn.
     *
     * @param string|null $idn
     *
     * @return StoreUsuarios
     */
    public function setIdn($idn = null)
    {
        $this->idn = $idn;

        return $this;
    }

    /**
     * Get idn.
     *
     * @return string|null
     */
    public function getIdn()
    {
        return $this->idn;
    }

    /**
     * Set telefono.
     *
     * @param string|null $telefono
     *
     * @return StoreUsuarios
     */
    public function setTelefono($telefono = null)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono.
     *
     * @return string|null
     */
    public function getTelefono()
    {
        return $this->telefono;
    }
    /**
     * Set birthday.
     *
     * @param string|null $birthday
     *
     * @return StoreUsuarios
     */
    public function setBirthday($birthday = null)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday.
     *
     * @return string|null
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set direccion.
     *
     * @param string|null $direccion
     *
     * @return StoreUsuarios
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
     * Set estado.
     *
     * @param string|null $estado
     *
     * @return StoreUsuarios
     */
    public function setEstado($estado = null)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado.
     *
     * @return string|null
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set tipo.
     *
     * @param string $tipo
     *
     * @return StoreUsuarios
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo.
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set asesor.
     *
     * @param string $asesor
     *
     * @return StoreUsuarios
     */
    public function setAsesor($asesor)
    {
        $this->asesor = $asesor;

        return $this;
    }

    /**
     * Get asesor.
     *
     * @return string
     */
    public function getAsesor()
    {
        return $this->asesor;
    }
}
