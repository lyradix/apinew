<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\ArtistRepository;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups('artist:read')]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
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

    #[ORM\ManyToOne(inversedBy: 'artistFK')]
    private ?Days $jourFK = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): static
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

    public function getJourFK(): ?Days
    {
        return $this->jourFK;
    }

    public function setJourFK(?Days $jourFK): static
    {
        $this->jourFK = $jourFK;

        return $this;
    }

   
}
