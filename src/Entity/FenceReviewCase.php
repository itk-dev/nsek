<?php

namespace App\Entity;

use App\Repository\FenceReviewCaseRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FenceReviewCaseRepository::class)
 */
class FenceReviewCase extends CaseEntity
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $complainant;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $complainantAddress;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $complainantZip;

    /**
     * @ORM\Column(type="string", length=255, options={"default": "submitted"})
     */
    private $caseState = 'submitted';

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $complainantCPR;

    /**
     * @ORM\Column(type="text")
     */
    private $conditions;

    /**
     * @ORM\Column(type="text")
     */
    private $complainantClaim;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $complainantCadastralNumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $accused;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $accusedAddress;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $accusedCPR;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $accusedCadastralNumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $accusedZip;

    public function getComplainant(): ?string
    {
        return $this->complainant;
    }

    public function setComplainant(?string $complainant): self
    {
        $this->complainant = $complainant;

        return $this;
    }

    public function getComplainantAddress(): ?string
    {
        return $this->complainantAddress;
    }

    public function setComplainantAddress(?string $complainantAddress): self
    {
        $this->complainantAddress = $complainantAddress;

        return $this;
    }

    public function getComplainantZip(): ?string
    {
        return $this->complainantZip;
    }

    public function setComplainantZip(?string $complainantZip): self
    {
        $this->complainantZip = $complainantZip;

        return $this;
    }

    public function getCaseState(): ?string
    {
        return $this->caseState;
    }

    public function setCaseState(string $caseState): self
    {
        $this->caseState = $caseState;

        return $this;
    }

    public function getComplainantCPR(): ?string
    {
        return $this->complainantCPR;
    }

    public function setComplainantCPR(string $complainantCPR): self
    {
        $this->complainantCPR = $complainantCPR;

        return $this;
    }

    public function getConditions(): ?string
    {
        return $this->conditions;
    }

    public function setConditions(string $conditions): self
    {
        $this->conditions = $conditions;

        return $this;
    }

    public function getComplainantClaim(): ?string
    {
        return $this->complainantClaim;
    }

    public function setComplainantClaim(string $complainantClaim): self
    {
        $this->complainantClaim = $complainantClaim;

        return $this;
    }

    public function getComplainantCadastralNumber(): ?string
    {
        return $this->complainantCadastralNumber;
    }

    public function setComplainantCadastralNumber(string $complainantCadastralNumber): self
    {
        $this->complainantCadastralNumber = $complainantCadastralNumber;

        return $this;
    }

    public function getAccused(): ?string
    {
        return $this->accused;
    }

    public function setAccused(string $accused): self
    {
        $this->accused = $accused;

        return $this;
    }

    public function getAccusedAddress(): ?string
    {
        return $this->accusedAddress;
    }

    public function setAccusedAddress(string $accusedAddress): self
    {
        $this->accusedAddress = $accusedAddress;

        return $this;
    }

    public function getAccusedCPR(): ?string
    {
        return $this->accusedCPR;
    }

    public function setAccusedCPR(string $accusedCPR): self
    {
        $this->accusedCPR = $accusedCPR;

        return $this;
    }

    public function getAccusedCadastralNumber(): ?string
    {
        return $this->accusedCadastralNumber;
    }

    public function setAccusedCadastralNumber(string $accusedCadastralNumber): self
    {
        $this->accusedCadastralNumber = $accusedCadastralNumber;

        return $this;
    }

    public function getAccusedZip(): ?string
    {
        return $this->accusedZip;
    }

    public function setAccusedZip(string $accusedZip): self
    {
        $this->accusedZip = $accusedZip;

        return $this;
    }
}
