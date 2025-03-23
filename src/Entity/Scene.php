<?php

namespace App\Entity;

use App\Repository\SceneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SceneRepository::class)]
class Scene
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $nom = null;

    /**
     * @var Collection<int, artist>
     */
    #[ORM\OneToMany(targetEntity: artist::class, mappedBy: 'sceneFK')]
    private Collection $artistFK;

    public function __construct()
    {
        $this->artistFK = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, artist>
     */
    public function getArtistFK(): Collection
    {
        return $this->artistFK;
    }

    public function addArtistFK(artist $artistFK): static
    {
        if (!$this->artistFK->contains($artistFK)) {
            $this->artistFK->add($artistFK);
            $artistFK->setSceneFK($this);
        }

        return $this;
    }

    public function removeArtistFK(artist $artistFK): static
    {
        if ($this->artistFK->removeElement($artistFK)) {
            // set the owning side to null (unless already changed)
            if ($artistFK->getSceneFK() === $this) {
                $artistFK->setSceneFK(null);
            }
        }

        return $this;
    }
}
