<?php

namespace App\Entity;

use App\Repository\PoiRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PoiRepository::class)]

class Poi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 30)]
    #[Groups(['poi:read'])]
    private ?string $type = null;

    #[ORM\Column(type: 'json', nullable: false)]
    private ?array $properties = null;

    #[ORM\Column(type: 'geometry', nullable: true, options: ['geometry_type' => 'POINT', 'srid' => 4326])]
    private ?Point $geometry = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'La longitude ne doit pas être vide')]
    #[Assert\Regex(
        pattern: '/^-?\d+(\.\d+)?$/',
        message: 'La longitude doit être un nombre valide'
    )]
    private ?string $longitude = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'La latitude ne doit pas être vide')]
    #[Assert\Regex(
        pattern: '/^-?\d+(\.\d+)?$/',
        message: 'La latitude doit être un nombre valide'
    )]
    private ?string $latitude = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getProperties(): ?array
    {
        return $this->properties;
    }

    public function setProperties(array $properties): self
    {
        $this->properties = $properties;

        return $this;
    }

    public function getGeometry(): ?Point
    {
        return $this->geometry;
    }

    public function setGeometry(Point $geometry): self
    {
        $this->geometry = $geometry;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }
    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;
        return $this;
    }
}
