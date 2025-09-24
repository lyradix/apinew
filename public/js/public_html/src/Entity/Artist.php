<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\ArtistRepository;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArtistRepository::class)]
class Artist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['artist:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Groups(['artist:read'])]
    private ?string $nom = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups('artist:read')]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups('artist:read')]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\Column(length: 45)]
    #[Groups('artist:read')]
    private ?string $famousSong = null;

    #[ORM\Column(length: 30)]
    #[Groups('artist:read')]
    private ?string $genre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups('artist:read')]
    private ?string $description = null;

    #[ORM\Column(length: 30)]
    #[Groups('artist:read')]
    private ?string $source = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups('artist:read')]
    private ?string $lien = null;

    #[ORM\ManyToOne(targetEntity: Scene::class, inversedBy: 'artistFK')]
    #[Groups(['artist:read'])]
    private ?Scene $sceneFK = null;
    
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('artist:read')]
    private ?string $image = null;

    #[Assert\File(
        maxSize: '2M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
        mimeTypesMessage: 'Merci de télécharger une image valide (JPEG, PNG, WEBP)'
    )]

    private ?File $imageFile = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $timeStamp = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
      $this->id = $id;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }


public function getDate(): ?\DateTimeInterface
{
    return $this->date;
}

public function setDate(?\DateTimeInterface $date): self
{
    $this->date = $date;
    return $this;
}

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function getFamousSong(): ?string
    {
        return $this->famousSong;
    }

    public function setFamousSong(string $famousSong): static
    {
        $this->famousSong = $famousSong;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(string $lien): static
    {
        $this->lien = $lien;

        return $this;
    }

    public function getSceneFK(): ?Scene
    {
        return $this->sceneFK;
    }

    public function setSceneFK(?Scene $sceneFK): self
    {
        $this->sceneFK = $sceneFK;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }
    
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }
    
    public function setImageFile(?File $imageFile): static
    {
        $this->imageFile = $imageFile;
        
        return $this;
    }

    public function getTimeStamp(): ?\DateTimeInterface
    {
        return $this->timeStamp;
    }

    public function setTimeStamp(\DateTimeInterface $timeStamp): static
    {
        $this->timeStamp = $timeStamp;

        return $this;
    }
}