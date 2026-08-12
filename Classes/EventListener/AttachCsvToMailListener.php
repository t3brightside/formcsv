<?php
declare(strict_types=1);

namespace Brightside\Formcsv\EventListener;

use Symfony\Component\Mailer\Event\MessageEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;

class AttachCsvToMailListener
{
    private static array $tasks = [];

    public static function addAttachmentTask(string $csvContent, string $name, bool $toReceiver, bool $toSender, array $userEmails): void
    {
        self::$tasks[] = [
            'content' => $csvContent,
            'name' => $name,
            'toReceiver' => $toReceiver,
            'toSender' => $toSender,
            'userEmails' => $userEmails,
        ];
    }

    /**
     * Resets tasks manually if needed in testing or persistent runtimes.
     */
    public static function reset(): void
    {
        self::$tasks = [];
    }

    #[AsEventListener(identifier: 'csvattachment-mail-listener')]
    public function __invoke(MessageEvent $event): void
    {
        if (empty(self::$tasks)) {
            return;
        }

        try {
            $message = $event->getMessage();
            
            if (!method_exists($message, 'getTo') || !method_exists($message, 'attach')) {
                return;
            }

            $toAddresses = $message->getTo();
            if (empty($toAddresses)) {
                return;
            }

            $outgoingEmails = [];
            foreach ($toAddresses as $address) {
                if (method_exists($address, 'getAddress')) {
                    $outgoingEmails[] = strtolower(trim($address->getAddress()));
                }
            }

            foreach (self::$tasks as $task) {
                if (empty($task['content'])) {
                    continue;
                }

                $isSenderMail = false;
                foreach ($outgoingEmails as $outgoing) {
                    if (in_array($outgoing, $task['userEmails'], true)) {
                        $isSenderMail = true;
                        break;
                    }
                }

                $shouldAttach = false;
                if ($isSenderMail && $task['toSender']) {
                    $shouldAttach = true;
                } elseif (!$isSenderMail && $task['toReceiver']) {
                    $shouldAttach = true;
                }

                if ($shouldAttach) {
                    // Attach directly from raw string content in memory
                    $message->attach($task['content'], $task['name'], 'text/csv; charset=utf-8');
                }
            }
        } finally {
            // Always purge memory queue after mail processing completes
            self::$tasks = [];
        }
    }
}