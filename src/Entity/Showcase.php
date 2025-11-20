<?php

namespace App\Entity;

use App\Repository\ShowcaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShowcaseRepository::class)]
class Showcase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'showcases')]
    private ?Member $creator = null;

    /**
     * @var Collection<int, Keyboard>
     */
    #[ORM\ManyToMany(targetEntity: Keyboard::class, inversedBy: 'showcases')]
    private Collection $keyboards;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom du clavier est obligatoire")]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: "Le nom doit faire au moins {{ limit }} caractères",
        maxMessage: "Le nom est trop long"
    )]
    private ?string $name = null;


    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $published = null;

    public function __construct()
    {
        $this->keyboards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreator(): ?Member
    {
        return $this->creator;
    }

    public function setCreator(?Member $creator): static
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * @return Collection<int, Keyboard>
     */
    public function getKeyboards(): Collection
    {
        return $this->keyboards;
    }

    public function addKeyboard(Keyboard $keyboard): static
    {
        if (!$this->keyboards->contains($keyboard)) {
            $this->keyboards->add($keyboard);
        }

        return $this;
    }

    public function removeKeyboard(Keyboard $keyboard): static
    {
        $this->keyboards->removeElement($keyboard);

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function isPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): static
    {
        $this->published = $published;

        return $this;
    }
}
