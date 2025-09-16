<?php

namespace App\Entity;

use App\Repository\SceneRepository;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SceneRepository::class)]
class Scene
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
      #[Groups('scene:read')]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Groups(['scene:read'])]
    private ?string $nom = null;

    /**
     * @var Collection<int, artist>
     */
    #[ORM\OneToMany(targetEntity: Artist::class, mappedBy: 'sceneFK')]
    private Collection $artistFK;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(['scene:read'])]
    private ?Poi $poiFK = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $timeStamp = null;
    

    public function __construct()
    {
        $this->artistFK = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, artist>
     */
    public function getArtistFK(): Collection
    {
        return $this->artistFK;
    }

    public function addArtistFK(Artist $artistFK): static
    {
        if (!$this->artistFK->contains($artistFK)) {
            $this->artistFK->add($artistFK);
            $artistFK->setSceneFK($this);
        }

        return $this;
    }

    public function removeArtistFK(Artist $artistFK): static
    {
        if ($this->artistFK->removeElement($artistFK)) {
            // set the owning side to null (unless already changed)
            if ($artistFK->getSceneFK() === $this) {
                $artistFK->setSceneFK(null);
            }
        }

        return $this;
    }

    public function getPoiFK(): ?Poi
    {
        return $this->poiFK;
    }

    public function setPoiFK(?Poi $poiFK): static
    {
        $this->poiFK = $poiFK;

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
