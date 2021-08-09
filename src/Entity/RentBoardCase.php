<?php

namespace App\Entity;

use App\Repository\RentBoardCaseRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RentBoardCaseRepository::class)
 */
class RentBoardCase extends CaseEntity
{
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $leaseSize;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $complainant;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $complainantAddress;

    /**
     * @ORM\Column(type="integer")
     */
    private $complainantPhone;

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
     * @ORM\Column(type="boolean")
     */
    private $hasVacated;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $leaseAddress;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $leaseZip;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $leaseCity;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $leaseStarted;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $leaseAgreedRent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $leaseInteriorMaintenance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $leaseRegulatedRent;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $leaseRentAtCollectionTime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $leaseSecurityDeposit;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $previousCasesAtLease;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $prepaidRent;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $feePaid;

    public function getLeaseSize(): ?int
    {
        return $this->leaseSize;
    }

    public function setLeaseSize(?int $leaseSize): self
    {
        $this->leaseSize = $leaseSize;

        return $this;
    }

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

    public function getComplainantPhone(): ?int
    {
        return $this->complainantPhone;
    }

    public function setComplainantPhone(?int $complainantPhone): self
    {
        $this->complainantPhone = $complainantPhone;

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

    public function getHasVacated(): ?bool
    {
        return $this->hasVacated;
    }

    public function setHasVacated(bool $hasVacated): self
    {
        $this->hasVacated = $hasVacated;

        return $this;
    }

    public function getLeaseAddress(): ?string
    {
        return $this->leaseAddress;
    }

    public function setLeaseAddress(string $leaseAddress): self
    {
        $this->leaseAddress = $leaseAddress;

        return $this;
    }

    public function getLeaseZip(): ?string
    {
        return $this->leaseZip;
    }

    public function setLeaseZip(string $leaseZip): self
    {
        $this->leaseZip = $leaseZip;

        return $this;
    }

    public function getLeaseCity(): ?string
    {
        return $this->leaseCity;
    }

    public function setLeaseCity(string $leaseCity): self
    {
        $this->leaseCity = $leaseCity;

        return $this;
    }

    public function getLeaseStarted(): ?\DateTimeInterface
    {
        return $this->leaseStarted;
    }

    public function setLeaseStarted(?\DateTimeInterface $leaseStarted): self
    {
        $this->leaseStarted = $leaseStarted;

        return $this;
    }

    public function getLeaseAgreedRent(): ?int
    {
        return $this->leaseAgreedRent;
    }

    public function setLeaseAgreedRent(?int $leaseAgreedRent): self
    {
        $this->leaseAgreedRent = $leaseAgreedRent;

        return $this;
    }

    public function getLeaseInteriorMaintenance(): ?string
    {
        return $this->leaseInteriorMaintenance;
    }

    public function setLeaseInteriorMaintenance(?string $leaseInteriorMaintenance): self
    {
        $this->leaseInteriorMaintenance = $leaseInteriorMaintenance;

        return $this;
    }

    public function getLeaseRegulatedRent(): ?int
    {
        return $this->leaseRegulatedRent;
    }

    public function setLeaseRegulatedRent(?int $leaseRegulatedRent): self
    {
        $this->leaseRegulatedRent = $leaseRegulatedRent;

        return $this;
    }

    public function getLeaseRentAtCollectionTime(): ?int
    {
        return $this->leaseRentAtCollectionTime;
    }

    public function setLeaseRentAtCollectionTime(?int $leaseRentAtCollectionTime): self
    {
        $this->leaseRentAtCollectionTime = $leaseRentAtCollectionTime;

        return $this;
    }

    public function getLeaseSecurityDeposit(): ?int
    {
        return $this->leaseSecurityDeposit;
    }

    public function setLeaseSecurityDeposit(?int $leaseSecurityDeposit): self
    {
        $this->leaseSecurityDeposit = $leaseSecurityDeposit;

        return $this;
    }

    public function getPreviousCasesAtLease(): ?string
    {
        return $this->previousCasesAtLease;
    }

    public function setPreviousCasesAtLease(?string $previousCasesAtLease): self
    {
        $this->previousCasesAtLease = $previousCasesAtLease;

        return $this;
    }

    public function getPrepaidRent(): ?int
    {
        return $this->prepaidRent;
    }

    public function setPrepaidRent(?int $prepaidRent): self
    {
        $this->prepaidRent = $prepaidRent;

        return $this;
    }

    public function getFeePaid(): ?bool
    {
        return $this->feePaid;
    }

    public function setFeePaid(bool $feePaid): self
    {
        $this->feePaid = $feePaid;

        return $this;
    }
}
