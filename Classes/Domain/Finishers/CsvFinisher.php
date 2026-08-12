<?php
declare(strict_types=1);

namespace Brightside\Formcsv\Domain\Finishers;

use Brightside\Formcsv\EventListener\AttachCsvToMailListener;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Form\Domain\Finishers\AbstractFinisher;

class CsvFinisher extends AbstractFinisher
{
    protected $shortFinisherIdentifier = 'CsvFinisher';

    protected $options = [
        'filename' => 'submission.csv',
        'excludeFields' => '',
        'attachToReceiver' => true,
        'attachToSender' => false,
    ];

    protected function executeInternal(): void
    {
        $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);

        try {
            $formRuntime = $this->finisherContext->getFormRuntime();
            $formState = $formRuntime->getFormState();
            
            $filename = trim((string)$this->parseOption('filename')) ?: 'submission.csv';
            $filename = basename($filename); // Strips out any ../ directory traversal attempts
            if (!str_ends_with(strtolower($filename), '.csv')) {
                $filename .= '.csv';
            }

            $excludeRaw = (string)$this->parseOption('excludeFields');
            $attachToReceiver = (bool)$this->parseOption('attachToReceiver');
            $attachToSender = (bool)$this->parseOption('attachToSender');

            $headers = [];
            $rowValues = [];
            $elements = $formRuntime->getFormDefinition()->getRenderablesRecursively();

            foreach ($elements as $element) {
                $type = $element->getType();
                if (in_array($type, ['Page', 'Fieldset', 'GridRow', 'GridColumn', 'StaticText', 'Honeypot'])) {
                    continue;
                }

                $identifier = $element->getIdentifier();
                if (str_contains($excludeRaw, '{' . $identifier . '}')) {
                    continue;
                }

                $label = $element->getLabel() ?: $identifier;
                $value = $formState->getFormValue($identifier);

                if (is_array($value)) {
                    $value = implode(', ', array_filter($value));
                }

                $headers[] = $label;
                $rowValues[] = (string)($value ?? '');
            }

            // Create CSV string completely in memory (PHP 8.4 safe)
            $fp = fopen('php://temp', 'r+');
            fputs($fp, "\xEF\xBB\xBF");
            fputcsv($fp, $headers, ';', '"', '\\');
            fputcsv($fp, $rowValues, ';', '"', '\\');
            rewind($fp);
            $csvContent = stream_get_contents($fp);
            fclose($fp);

            // Find the submitter's email address from their actual form input
            $userEmails = [];
            foreach ($formState->getFormValues() as $val) {
                if (is_string($val) && GeneralUtility::validEmail($val)) {
                    $userEmails[] = strtolower(trim($val));
                }
            }

            // Pass the raw CSV content string directly (No disk saving)
            AttachCsvToMailListener::addAttachmentTask(
                $csvContent,
                $filename,
                $attachToReceiver,
                $attachToSender,
                $userEmails
            );

        } catch (\Throwable $e) {
            $logger->error('CsvAttachmentFinisher Exception: ' . $e->getMessage());
        }
    }
}