<?php

namespace App\Entity;

use App\Repository\EnfermedadesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EnfermedadesRepository::class)
 */
class Enfermedades
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $descripcion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $codigo;

    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $codigo_descripcion;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(?string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getCodigoDescripcion(): ?string
    {
        return $this->codigo_descripcion;
    }

    public function setCodigoDescripcion(?string $codigo_descripcion): self
    {
        $this->codigo_descripcion = $codigo_descripcion;

        return $this;
    }
}
