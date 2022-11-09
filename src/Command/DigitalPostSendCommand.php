<?php

namespace App\Command;

use App\Entity\DigitalPost;
use App\Repository\DigitalPostRepository;
use App\Service\DigitalPostHelper;
use App\Service\DocumentUploader;
use App\Service\IdentificationHelper;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'tvist1:digital-post:send',
    description: 'Send unsent digital post',
)]
class DigitalPostSendCommand extends Command
{
    private int $maxNumberOfRetries = 10;

    public function __construct(private DigitalPostHelper $digitalPostHelper, private DigitalPostRepository $digitalPostRepository, private DocumentUploader $documentUploader, private EntityManagerInterface $entityManager, private LoggerInterface $databaseLogger)
    {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $statuses = [null, DigitalPost::STATUS_ERROR];
        $digitalPosts = $this->digitalPostRepository->findBy(['status' => $statuses]);

        $io->info(sprintf('Number of digital posts: %d', count($digitalPosts)));

        foreach ($digitalPosts as $index => $digitalPost) {
            $io->title(sprintf('% 3d/%d %s:%s', $index + 1, count($digitalPosts), get_class($digitalPost), $digitalPost->getId()));

            try {
                $content = $this->documentUploader->getFileContent($digitalPost->getDocument());
                $attachments = [];
                foreach ($digitalPost->getAttachments() as $attachment) {
                    $attachments[$attachment->getDocument()->getDocumentName()] = $this->documentUploader->getFileContent($attachment->getDocument());
                }

                $previousResults = $digitalPost->getData()['results'] ?? [];
                $results = [];
                foreach ($digitalPost->getRecipients() as $recipient) {
                    $recipientKey = $recipient->getId()->toRfc4122();
                    $io->info(sprintf('%s (%s: %s)', $recipient->getName(), $recipient->getIdentifierType(), $recipient->getIdentifier()));

                    $previousResult = $previousResults[$recipientKey] ?? [];

                    $result = null;
                    if (true === ($previousResult['result'] ?? null)) {
                        $io->info(sprintf('Already sent to %s', $recipient->getName()));
                        $result = $previousResult;
                    } else {
                        try {
                            switch ($recipient->getIdentifierType()) {
                            case IdentificationHelper::IDENTIFIER_TYPE_CPR:
                                $result = $this->digitalPostHelper->sendDigitalPostCPR(
                                    $recipient->getIdentifier(),
                                    $recipient->getName(),
                                    $recipient->getAddress(),
                                    $digitalPost->getSubject(),
                                    $content,
                                    $attachments
                                );
                                break;

                             case IdentificationHelper::IDENTIFIER_TYPE_CVR:
                                 $result = $this->digitalPostHelper->sendDigitalPostCVR(
                                     $recipient->getIdentifier(),
                                     $recipient->getName(),
                                     $recipient->getAddress(),
                                     $digitalPost->getSubject(),
                                     $content,
                                     $attachments
                                 );
                                 break;

                            default:
                                $result = [
                                    'result' => 'error',
                                    'message' => sprintf(
                                        'Unhandled identifier type: %s',
                                        $recipient->getIdentifierType()
                                    ),
                                ];
                                $io->error($result['message']);
                                break;
                            }
                        } catch (\Exception $exception) {
                            $this->databaseLogger->error($exception->getMessage());
                            $result = [
                                'result' => 'exception',
                                'message' => $exception->getMessage(),
                                'exception' => $exception,
                            ];
                            $io->error($result['message']);
                        }
                    }
                    $results[$recipientKey] = $result;
                }

                // Bookkeeping.
                $digitalPost->addData(['results' => $results]);

                $resultValues = array_unique(array_column($results, 'result'));

                $now = new \DateTimeImmutable();
                // The digital post has been sent to all recipients if all result values are true.
                $sent = 1 === count($resultValues) && true === $resultValues[0];
                $digitalPost->setStatus($sent ? DigitalPost::STATUS_SENT : DigitalPost::STATUS_ERROR);
                if (DigitalPost::STATUS_SENT === $digitalPost->getStatus()) {
                    $digitalPost->setSentAt($now);
                }

                // Keep track of posts and fail when max number of retries exceeded.
                $postStatuses = $digitalPost->getData()['post_statuses'] ?? [];
                $postStatuses[] = [
                    'created_at' => $now->format($now::ATOM),
                    'status' => $digitalPost->getStatus(),
                ];
                $digitalPost->addData(['post_statuses' => $postStatuses]);

                if (count($postStatuses) >= $this->maxNumberOfRetries) {
                    $digitalPost->setStatus(DigitalPost::STATUS_FAILED);
                }

                $this->entityManager->persist($digitalPost);
                $this->entityManager->flush();

                $io->info(sprintf('Status: %s', $digitalPost->getStatus()));
            } catch (\Exception $exception) {
                $digitalPost->setData([
                   'exception' => [
                       'message' => $exception->getMessage(),
                   ],
                ]);
                $this->databaseLogger->error($exception->getMessage());
                $io->error(sprintf('Error: %s', $exception->getMessage()));
            }
        }

        return Command::SUCCESS;
    }
}
