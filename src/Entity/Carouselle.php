<?php

namespace App\Entity;

use App\Repository\CarouselleRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Entity\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: CarouselleRepository::class)]
class Carouselle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $CarouselleImage = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Vich\UploadableField(mapping: 'Carouselle', fileNameProperty: 'CarouselleImage')]
    #[Assert\Image]
    private ?File $carouselleImageFile = null;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCarouselleImage(): ?string
    {
        return $this->CarouselleImage;
    }

    public function setCarouselleImage(?string $CarouselleImage): static
    {
        $this->CarouselleImage = $CarouselleImage;

        return $this;
    }

    public function getCarouselleImageFile(): ?File
    {
        return $this->carouselleImageFile;
    }

    public function setCarouselleImageFile(?File $carouselleImageFile): static
    {
        $this->carouselleImageFile = $carouselleImageFile;

        return $this;
    }
}
