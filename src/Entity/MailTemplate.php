<?php

namespace App\Entity;

use App\Repository\MailTemplateRepository;
use App\Traits\BlameableEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=MailTemplateRepository::class)
 * @Vich\Uploadable
 */
class MailTemplate
{
    use BlameableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidV4Generator::class)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $templateFilename;

    /**
     * @Vich\UploadableField(mapping="mail_templates", fileNameProperty="templateFilename")
     *
     * @todo validate that the file is a Word document
     * @ Assert\File(mimeTypes = {"application/vnd.openxmlformats-officedocument.wordprocessingml.document"})
     *
     * @var File
     */
    private $templateFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    public function getId(): ?UuidV4
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

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setTemplateFile(File $templateFile = null): self
    {
        $this->templateFile = $templateFile;

        if ($templateFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getTemplateFile(): ?File
    {
        return $this->templateFile;
    }

    public function setTemplateFilename(?string $templateFilename): self
    {
        $this->templateFilename = $templateFilename;

        return $this;
    }

    public function getTemplateFilename(): ?string
    {
        return $this->templateFilename;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
