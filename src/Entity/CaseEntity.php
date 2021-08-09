<?php

namespace App\Entity;

use App\Repository\CaseEntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;
use Symfony\Component\Uid\UuidV4;

/**
 * @ORM\Entity(repositoryClass=CaseEntityRepository::class)
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"caseEntity" = "CaseEntity", "residentComplaintBoardCase" = "ResidentComplaintBoardCase", "rentBoardCase" = "RentBoardCase", "fenceReviewCase" = "FenceReviewCase"})
 * @ORM\EntityListeners({"App\Logging\EntityListener\CaseListener"})
 */
abstract class CaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidV4Generator::class)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Board::class, inversedBy="caseEntities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $board;

    /**
     * @ORM\ManyToOne(targetEntity=Municipality::class, inversedBy="caseEntities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $municipality;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $caseType;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=SubBoard::class, inversedBy="caseEntities")
     */
    private $subboard;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $caseNumber;

    public function getId(): ?UuidV4
    {
        return $this->id;
    }

    public function getBoard(): ?Board
    {
        return $this->board;
    }

    public function setBoard(?Board $board): self
    {
        $this->board = $board;

        return $this;
    }

    public function getMunicipality(): ?Municipality
    {
        return $this->municipality;
    }

    public function setMunicipality(?Municipality $municipality): self
    {
        $this->municipality = $municipality;

        return $this;
    }

    public function getCaseType(): ?string
    {
        return $this->caseType;
    }

    public function setCaseType(string $caseType): self
    {
        $this->caseType = $caseType;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getSubboard(): ?SubBoard
    {
        return $this->subboard;
    }

    public function setSubboard(?SubBoard $subboard): self
    {
        $this->subboard = $subboard;

        return $this;
    }

    public function getCaseNumber(): ?string
    {
        return $this->caseNumber;
    }

    public function setCaseNumber(string $caseNumber): self
    {
        $this->caseNumber = $caseNumber;

        return $this;
    }
}
