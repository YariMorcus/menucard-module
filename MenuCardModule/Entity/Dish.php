<?php

namespace HetBonteHert\Module\MenuCard\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use HetBonteHert\Module\MenuCard\Repository\DishRepository;
use Money\Currency;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;
use Tidi\Cms\Module\Core\Interfaces\WysiwygAwareInterface;

/**
 * @ORM\Entity(repositoryClass=DishRepository::class)
 * @ORM\Table(name="menucard_dish")
 */
class Dish implements WysiwygAwareInterface
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
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var Money
     * @ORM\Embedded(class="Money\Money")
     * @Assert\NotBlank()
     */
    private $price;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $active = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $highlighted = false;

    /**
     * @var Category|null
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="dishes")
     */
    private $category;

    /**
     * @var Collection<int, MenuCard>
     * @ORM\ManyToMany(targetEntity=MenuCard::class, mappedBy="dishes")
     */
    private $menuCards;

    public function __construct()
    {
        $this->menuCards            = new ArrayCollection();
        $this->price                = new Money(0, new Currency('EUR'));
    }

    /**
     * @return string
     */
    public function getImagePrefix()
    {
        return sprintf('_mcm_dish_%s_', $this->getId());
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return $this
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Money
     */
    public function getPrice(): Money
    {
        return $this->price;
    }

    /**
     * @param Money $money
     *
     * @return $this
     */
    public function setPrice(Money $money): self
    {
        $this->price = $money;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): ?bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return $this
     */
    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHighlighted(): ?bool
    {
        return $this->highlighted;
    }

    /**
     * @param bool $highlighted
     *
     * @return $this
     */
    public function setHighlighted(bool $highlighted): self
    {
        $this->highlighted = $highlighted;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function getCategoryId(): ?int
    {
        if (null === $this->category) {
            return null;
        }

        return $this->category->getId();
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        if ($category instanceof Category) {
            $category->addDish($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, MenuCard>
     */
    public function getMenuCards(): Collection
    {
        return $this->menuCards;
    }

    public function addMenuCard(MenuCard $menuCard): self
    {
        if (!$this->menuCards->contains($menuCard)) {
            $this->menuCards[] = $menuCard;
            $menuCard->addDish($this);
        }

        return $this;
    }

    public function removeMenuCard(MenuCard $menuCard): self
    {
        if ($this->menuCards->removeElement($menuCard)) {
            $menuCard->removeDish($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getName() ?? '';
    }
}
