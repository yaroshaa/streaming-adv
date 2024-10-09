<?php

namespace Modules\Feedbacks\Services\Facebook\Webhooks;

use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

/**
 * Class BaseWebhook
 * @package Modules\Feedbacks\Services\Facebook\Webhooks
 */
abstract class BaseWebhook
{
    public static array $availableVerbs = [];

    public static array $availableItems = [];

    protected LoggerInterface $logger;

    protected array $entry;

    protected array $config = [];

    public function __construct(array $entry)
    {
        $this->entry = $entry;
    }

    protected function checkVerb(string $verb): bool
    {
        return in_array($verb, static::$availableVerbs, true);
    }

    protected function checkItem(string $item): bool
    {
        return in_array($item, static::$availableItems, true);
    }

    protected function validateChanges(string $entryId, array $changes): bool
    {
        if (empty($entryId)) {
            $this->getLog()->info('FB webhook - empty "entry - id" value');
            return false;
        }

        if (empty($changes['verb'])) {
            $this->getLog()->info('FB webhook - empty "verb" value');
            return false;
        }

        if (!$this->checkVerb($changes['verb']) || !$this->checkItem($changes['item'])) {
            $this->getLog()->info(sprintf('FB webhook - unsupported "verb" and "item" with values: "%s" for "%s"', $changes['verb'], $changes['item']));
            return false;
        }

        return true;
    }

    public function process(): void
    {
        foreach ($this->entry as $entryItem) {
            $entryId = $entryItem['id'] ?? '0';
            foreach ($entryItem['changes'] as $change) {
                if ($this->validateChanges($entryId, $change['value'])) {
                    $this->processChange($entryId, $change['value']);
                }
            }
        }
    }

    public function setConfig(array $config): BaseWebhook
    {
        $this->config = $config;
        return $this;
    }

    public function setLogger(LoggerInterface $logger): BaseWebhook
    {
        $this->logger = $logger;
        return $this;
    }

    protected function getLog(): LoggerInterface
    {
        if ($this->logger === null) {
            $this->logger = Log::channel();
        }

        return $this->logger;
    }

    protected abstract function processChange(string $entryId, array $changes): void;
}
