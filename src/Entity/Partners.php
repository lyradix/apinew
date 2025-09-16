<?php

namespace App\Entity;

use App\Repository\PartnersRepository;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartnersRepository::class)]
class Partners
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('partners:read')]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Groups('partners:read')]
    private ?string $title = null;

    #[ORM\Column]
    #[Groups('partners:read')]
    private ?bool $frontPage = null;

    #[ORM\Column(length: 30)]
    #[Groups('partners:read')]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups('partners:read')]
    private ?string $link = null;

    #[ORM\Column(length: 5)]
    #[Groups('partners:read')]
    private ?string $partnerId = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('partners:read')]
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function isFrontPage(): ?bool
    {
        return $this->frontPage;
    }

    public function setFrontPage(bool $frontPage): static
    {
        $this->frontPage = $frontPage;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): static
    {
        $this->link = $link;

        return $this;
    }

    public function getPartnerId(): ?string
    {
        return $this->partnerId;
    }

    public function setPartnerId(string $partnerId): static
    {
        $this->partnerId = $partnerId;

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
