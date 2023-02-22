<?php

namespace App\Entity;

use App\Repository\HearingPostResponseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=HearingPostResponseRepository::class)
 */
class HearingPostResponse extends HearingPost
{
    /**
     * @ORM\ManyToOne(targetEntity=Party::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"mail_template"})
     */
    private ?\App\Entity\Party $sender = null;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?\DateTimeInterface $approvedOn = null;

    /**
     * @ORM\Column(type="text", nullable=false)
     * @Groups({"mail_template"})
     */
    private ?string $response = null;

    /**
     * @ORM\Column(type="boolean", options={"default":"1"})
     */
    private bool $sendReceipt = true;

    public function getSender(): ?Party
    {
        return $this->sender;
    }

    public function setSender(Party $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getApprovedOn(): ?\DateTimeInterface
    {
        return $this->approvedOn;
    }

    public function setApprovedOn(?\DateTimeInterface $approvedOn): self
    {
        $this->approvedOn = $approvedOn;

        return $this;
    }

    public function getLoggableProperties(): array
    {
        return [
            'sender',
            'approvedOn',
            'document',
            'attachments',
        ];
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(?string $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getSendReceipt(): ?bool
    {
        return $this->sendReceipt;
    }

    public function setSendReceipt(bool $sendReceipt): self
    {
        $this->sendReceipt = $sendReceipt;

        return $this;
    }
}
