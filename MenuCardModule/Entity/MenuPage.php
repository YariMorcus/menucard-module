<?php

declare(strict_types=1);

namespace HetBonteHert\Module\MenuCard\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Tidi\Cms\Module\Core\Entity\Structure;
use Tidi\Cms\Module\Core\Interfaces\WysiwygAwareInterface;
use Tidi\Cms\Module\Core\Model\StructureAwareInterface;

/**
 * Class MenuPage.
 *
 * @ORM\Entity(repositoryClass="HetBonteHert\Module\MenuCard\Repository\MenuPageRepository")
 * @ORM\Table(name="menucard_page")
 */
class MenuPage implements StructureAwareInterface, WysiwygAwareInterface
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Structure
     *
     * @ORM\ManyToOne(targetEntity="Tidi\Cms\Module\Core\Entity\Structure", fetch="EAGER")
     * @ORM\JoinColumn(name="structure_id", referencedColumnName="id", nullable=false)
     */
    private $structure;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $modifiedAt;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Gedmo\Blameable(on="update")
     */
    private $modifiedBy;

    /**
     * @var MenuCard|null
     * @ORM\ManyToOne(targetEntity=MenuCard::class, fetch="EAGER")
     * @ORM\JoinColumn(name="menucard_id", referencedColumnName="id", nullable=true)
     */
    private $menuCard;

    public function __construct(Structure $structure)
    {
        $this->structure = $structure;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStructureId(): ?int
    {
        return $this->structure->getId();
    }

    /**
     * @return Structure
     */
    public function getStructure(): Structure
    {
        return $this->structure;
    }

    /**
     * @param Structure $structure
     *
     * @return $this
     */
    public function setStructure(Structure $structure): self
    {
        $this->structure = $structure;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->structure->getName();
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     *
     * @return $this
     */
    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getModifiedBy(): ?int
    {
        return $this->modifiedBy;
    }

    /**
     * @param int|null $modifiedBy
     *
     * @return $this
     */
    public function setModifiedBy(?int $modifiedBy): self
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getModifiedAt(): ?DateTime
    {
        return $this->modifiedAt;
    }

    /**
     * @param DateTime $modifiedAt
     *
     * @return $this
     */
    public function setModifiedAt(DateTime $modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getImagePrefix(): string
    {
        return sprintf('_menupage_%s_', $this->getId());
    }

    public function getMenuCard(): ?MenuCard
    {
        return $this->menuCard;
    }

    public function setMenuCard(?MenuCard $menuCard): void
    {
        $this->menuCard = $menuCard;
    }
}
