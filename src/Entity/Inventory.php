<?php

namespace App\Entity;

use App\Repository\InventoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventoryRepository::class)]
class Inventory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    /**
     * @var Collection<int, Keyboard>
     */
    #[ORM\OneToMany(targetEntity: Keyboard::class, mappedBy: 'inventory')]
    private Collection $keyboards;

    #[ORM\OneToOne(inversedBy: 'inventory', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Member $member = null;

    public function __construct()
    {
        $this->keyboards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $keyboard->setInventory($this);
        }

        return $this;
    }

    public function removeKeyboard(Keyboard $keyboard): static
    {
        if ($this->keyboards->removeElement($keyboard)) {
            // set the owning side to null (unless already changed)
            if ($keyboard->getInventory() === $this) {
                $keyboard->setInventory(null);
            }
        }

        return $this;
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(Member $member): static
    {
        $this->member = $member;

        return $this;
    }
}
