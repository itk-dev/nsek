<?php

namespace App\Service;

use App\Entity\MailTemplate;
use App\Repository\CaseEntityRepository;
use App\Repository\MailTemplateRepository;
use PhpOffice\PhpWord\Element\AbstractElement;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Component\Serializer\SerializerInterface;

class MailTemplateHelper
{
    /**
     * @var MailTemplateRepository
     */
    private $mailTemplateRepository;

    /**
     * @var CaseEntityRepository
     */
    private $caseEntityRepository;

    /**
     * The config.
     *
     * @var array
     */
    private $config;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(MailTemplateRepository $mailTemplateRepository, CaseEntityRepository $caseEntityRepository, SerializerInterface $serializer, Filesystem $filesystem, array $mailTemplateHelperConfig)
    {
        $this->mailTemplateRepository = $mailTemplateRepository;
        $this->caseEntityRepository = $caseEntityRepository;
        $this->serializer = $serializer;
        $this->filesystem = $filesystem;
        $this->config = $mailTemplateHelperConfig;
    }

    /**
     * Get entity for previewing a mail template.
     *
     * @return The entity
     *
     * @throws \RuntimeException
     */
    public function getPreviewEntity(MailTemplate $mailTemplate)
    {
        switch ($mailTemplate->getType()) {
            case 'decision':
                return $this->caseEntityRepository->findOneBy([]);

            case 'inspection_letter':
                return $this->caseEntityRepository->findOneBy([]);
        }

        throw new \InvalidArgumentException(sprintf('Cannot get preview entity for mail template %s of type %s', $mailTemplate->getName(), $mailTemplate->getType()));
    }

    public function getTemplateData(MailTemplate $mailTemplate, $entity): array
    {
        $templateFileName = $this->getTemplateFile($mailTemplate);
        $templateProcessor = new TemplateProcessor($templateFileName);

        return $this->getValues($entity, $templateProcessor);
    }

    /**
     * Render mail template to a file.
     *
     * @param $entity
     */
    public function renderMailTemplate(MailTemplate $mailTemplate, $entity = null): string
    {
        $templateFileName = $this->getTemplateFile($mailTemplate);
        // https://phpword.readthedocs.io/en/latest/templates-processing.html
        $templateProcessor = new TemplateProcessor($templateFileName);
        $values = $this->getValues($entity, $templateProcessor);
        foreach ($values as $name => $value) {
            if ($value instanceof AbstractElement) {
                $templateProcessor->setComplexValue($name, $value);
            } else {
                $templateProcessor->setValue($name, $value);
            }
        }
        $processedFileName = $templateProcessor->save();

        $client = HttpClient::create($this->config['http_client_options']);
        $formFields = [
            'data' => DataPart::fromPath($processedFileName),
        ];
        $formData = new FormDataPart($formFields);

        // curl --insecure --form "data=@mail_template_001.docx" https://libreoffice:9980/lool/convert-to/pdf
        // https://sdk.collaboraonline.com/docs/conversion_api.html?highlight=convert
        try {
            $response = $client->request('POST', '/lool/convert-to/pdf', [
                'headers' => $formData->getPreparedHeaders()->toArray(),
                'body' => $formData->bodyToIterable(),
            ]);
            $content = $response->getContent();
        } catch (ClientException $exception) {
            // @todo How to handle this?
        }

        $fileName = $this->filesystem->tempnam('/tmp/', 'mail_template', '.pdf');
        file_put_contents($fileName, $content);

        return $fileName;
    }

    public function getTemplateFile(MailTemplate $mailTemplate): string
    {
        return rtrim($this->config['template_file_directory'] ?? '', '/').'/'.$mailTemplate->getTemplateFilename();
    }

    public function getTemplates(string $type): array
    {
        return $this->mailTemplateRepository->findBy(['type' => $type]);
    }

    /**
     * Get values from an entity.
     *
     * @param $entity
     *
     * @return array|false|mixed|string[]
     */
    private function getValues(?object $entity, TemplateProcessor $templateProcessor)
    {
        $placeHolders = $templateProcessor->getVariables();

        if (null === $entity) {
            // We don't have any data; highlight placeholders with colored markers.
            $values = array_combine(
                $placeHolders,
                array_map(static function ($name) {
                    $marker = new TextRun();
                    $marker->addText('${'.$name.'}', ['bgColor' => 'FFFF99']);

                    return $marker;
                }, $placeHolders)
            );
        } else {
            // Serialize to csv to flatten array values and get keys separated by `.`, i.e.
            //   ['name' => ['given' => 'Anders', 'family' => 'And']]
            // will be converted to
            //   ['name.given' => 'Anders', 'name.family' => 'And']
            $csv = $this->serializer->serialize($entity, 'csv', ['groups' => ['mail_template']]);
            // We now have a csv string with two lines (the first is the header) that we split and parse.
            [$header, $row] = array_map('str_getcsv', explode(PHP_EOL, $csv, 2));
            $values = array_combine($header, $row);
        }

        // Add some default values.
        $values['now'] = (new \DateTimeImmutable('now'))->format(\DateTimeImmutable::ATOM);
        $values['today'] = (new \DateTimeImmutable('today'))->format(\DateTimeImmutable::ATOM);

        foreach ($placeHolders as $placeHolder) {
            if (preg_match('/^(?P<key>[^:]+):(?P<format>.+)$/', $placeHolder, $matches)) {
                [, $key, $format] = $matches;
                if (isset($values[$key])) {
                    $values[$placeHolder] = $this->formatValue($values[$key], $format);
                }
            }
        }

        // Set empty string values for all template variables without a value.
        $values += array_map(static function ($count) { return ''; }, $templateProcessor->getVariableCount());

        return $values;
    }

    /**
     * Format a value.
     */
    private function formatValue(string $value, string $format): string
    {
        try {
            $date = new \DateTimeImmutable($value);
            $formatter = new \IntlDateFormatter(\Locale::getDefault(), \IntlDateFormatter::NONE, \IntlDateFormatter::NONE);
            // https://unicode-org.github.io/icu/userguide/format_parse/datetime/#datetime-format-syntax
            $formatter->setPattern($format);

            return $formatter->format($date);
        } catch (\Exception $exception) {
        }

        return $value;
    }
}
