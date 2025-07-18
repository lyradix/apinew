<?php

namespace App\Dto;

use App\Entity\Artist;
use App\Entity\Scene;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

class ConcertDto
{
    public ?Artist $artist = null;

    public ?Scene $scene = null;
    
    public ?Scene $sceneFK = null;

    #[Assert\File(
        maxSize: '2M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
        mimeTypesMessage: 'Merci de télécharger une image valide (JPEG, PNG, WEBP)'
    )]

    public ?File $imageFile = null;

    public ?string $concertImage = null;
}