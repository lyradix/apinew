<?php

namespace App\Entity;

use App\Repository\DaysRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DaysRepository::class)]
class Days
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $jour = null;

    #[ORM\ManyToOne(inversedBy: 'jourFK')]
    private ?User $userFK = null;

    /**
     * @var Collection<int, Artist>
     */
    #[ORM\ManyToMany(targetEntity: Artist::class, mappedBy: 'jourFK')]
    private Collection $artistFK;

    public function __construct()
    {
        $this->artistFK = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJour(): ?string
    {
        return $this->jour;
    }

    public function setJour(string $jour): self
    {
        $this->jour = $jour;

        return $this;
    }

    public function getUserFK(): ?User
    {
        return $this->userFK;
    }

    public function setUserFK(?User $userFK): static
    {
        $this->userFK = $userFK;

        return $this;
    }

    /**
     * @return Collection<int, Artist>
     */
    public function getArtistFK(): Collection
    {
        return $this->artistFK;
    }

    public function addArtistFK(Artist $artistFK): static
    {
        if (!$this->artistFK->contains($artistFK)) {
            $this->artistFK->add($artistFK);
            $artistFK->setJourFK($this);
        }

        return $this;
    }

    public function removeArtistFK(Artist $artistFK): static
    {
        if ($this->artistFK->removeElement($artistFK)) {
            // set the owning side to null (unless already changed)
            if ($artistFK->getJourFK() === $this) {
                $artistFK->setJourFK(null);
            }
        }

        return $this;
    }
}
