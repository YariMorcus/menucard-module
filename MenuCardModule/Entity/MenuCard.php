<?php

namespace HetBonteHert\Module\MenuCard\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use HetBonteHert\Module\MenuCard\Repository\MenuCardRepository;

/**
 * @ORM\Entity(repositoryClass=MenuCardRepository::class)
 * @ORM\Table(name="menucard_menucard")
 */
class MenuCard
{
    /**
     * @var int|null
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var Collection<int, Dish>
     * @ORM\ManyToMany(targetEntity=Dish::class, inversedBy="menuCards", fetch="EAGER")
     */
    private $dishes;

    public function __construct()
    {
        $this->dishes       = new ArrayCollection();
    }

    public function getImagePrefix(): string
    {
        return sprintf('_mcm_menucard_%s_', $this->getId());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Dish>
     */
    public function getDishes(): Collection
    {
        return $this->dishes;
    }

    public function addDish(Dish $dish): self
    {
        if (!$this->dishes->contains($dish)) {
            $this->dishes[] = $dish;
        }

        return $this;
    }

    public function removeDish(Dish $dish): self
    {
        $this->dishes->removeElement($dish);

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }
}
