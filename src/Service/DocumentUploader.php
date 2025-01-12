<?php

namespace App\Service;

use App\Entity\Document;
use App\Entity\User;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Mime\MimeTypeGuesserInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\AsciiSlugger;

class DocumentUploader
{
    private $uploadDocumentDirectory;

    /**
     * @var Filesystem
     */
    private $filesystem;
    private $projectDirectory;
    private MimeTypeGuesserInterface $mimeTypeGuesser;
    private Security $security;

    public function __construct(string $uploadDocumentDirectory, string $projectDirectory, Filesystem $filesystem, MimeTypeGuesserInterface $mimeTypeGuesser, Security $security)
    {
        $this->projectDirectory = $projectDirectory;
        $this->uploadDocumentDirectory = $uploadDocumentDirectory;
        $this->filesystem = $filesystem;
        $this->mimeTypeGuesser = $mimeTypeGuesser;
        $this->security = $security;
    }

    /**
     * Creates and returns new document from filename.
     */
    public function createDocumentFromPath(string $filePath, string $documentName, string $documentType, ?User $user = null, bool $move = false): Document
    {
        $document = new Document();

        $fileName = basename($filePath);

        $document
            ->setOriginalFileName($fileName)
            ->setDocumentName($documentName)
            ->setUploadedBy($user ?? $this->security->getUser())
            ->setType($documentType)
        ;

        // Copy file to desired folder
        $uniqueFileName = preg_replace('/\.(?<extension>[^.]+)$/', '-'.uniqid().'.$1', $fileName);

        $targetPath = $this->getFullDirectory().'/'.$uniqueFileName;

        if ($move) {
            $this->filesystem->rename($filePath, $targetPath, true);
        } else {
            $this->filesystem->copy($filePath, $targetPath);
        }

        $document->setFilename($uniqueFileName);

        return $document;
    }

    /**
     * Creates, uploads and returns new document from a file.
     */
    public function createDocumentFromUploadedFile(UploadedFile $file, string $documentName, string $documentType): Document
    {
        try {
            $tempDir = sys_get_temp_dir();
            $file = $file->move($tempDir, $file->getClientOriginalName());

            $tempPath = $file->getRealPath();

            $document = $this->createDocumentFromPath($file->getRealPath(), $documentName, $documentType);

            // Explicitly indicate that this is created manually, i.e. display original file name on document index page.
            $document->setIsCreatedManually(true);
        } finally {
            unlink($tempPath);
        }

        return $document;
    }

    /**
     * Handles view document.
     */
    public function handleViewDocument(Document $document, bool $forceDownload = false): Response
    {
        $filepath = $this->getFilepath($document->getFilename());

        // @see https://symfonycasts.com/screencast/symfony-uploads/file-streaming
        $response = new StreamedResponse(function () use ($filepath) {
            $outputStream = fopen('php://output', 'wb');
            $fileStream = fopen($filepath, 'r');
            stream_copy_to_stream($fileStream, $outputStream);
        });

        // We use document name as filename - it may not contain / or \, so we replace those.
        $filename = $document->getDocumentName();

        $filename = str_replace(['/', '\\', '%'], '-', $filename);

        $slugger = new AsciiSlugger();
        $fallbackFilename = $slugger->slug($filename)->__toString();

        $ext = pathinfo($document->getFilename(), PATHINFO_EXTENSION);

        $filename .= '.'.$ext;
        $fallbackFilename .= '.'.$ext;

        $disposition = HeaderUtils::makeDisposition(
            $forceDownload ? HeaderUtils::DISPOSITION_ATTACHMENT : HeaderUtils::DISPOSITION_INLINE,
            $filename,
            $fallbackFilename
        );

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', $this->getMimeType($document));

        return $response;
    }

    private function getFilepath(string $filename): string
    {
        return $this->getFullDirectory().'/'.$filename;
    }

    private function getFullDirectory(): string
    {
        return $this->projectDirectory.'/'.$this->uploadDocumentDirectory;
    }

    public function replaceFileContent(Document $document, string $filePath, bool $move = false)
    {
        $targetPath = $this->getFullDirectory().'/'.$document->getFilename();

        if ($move) {
            $this->filesystem->rename($filePath, $targetPath, true);
        } else {
            $this->filesystem->copy($filePath, $targetPath);
        }
    }

    public function getDocumentFileSize(Document $document): bool|int
    {
        return filesize($this->getFilepath($document->getFilename()));
    }

    public function getFileContent(Document $document)
    {
        $filepath = $this->getFilepath($document->getFilename());

        return file_get_contents($filepath);
    }

    public function getMimeType(Document $document): string
    {
        return $this->mimeTypeGuesser->guessMimeType($this->getFilepath($document->getFilename()));
    }

    /**
     * Deletes document file.
     */
    public function deleteDocumentFile(Document $document)
    {
        $filepath = $this->getFilepath($document->getFilename());
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }

    /**
     * Format bytes as a human readable string.
     *
     * @see https://stackoverflow.com/a/2510459/2502647
     */
    public static function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }
}
