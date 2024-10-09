<?php

namespace Modules\Feedbacks\Services\Facebook;

use Modules\Feedbacks\Services\Facebook\Webhooks\BaseWebhook;
use Modules\Feedbacks\Services\Facebook\Webhooks\ObjectInterface;
use Modules\Feedbacks\Services\Facebook\Webhooks\PageWebhook;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

/**
 * Class FacebookWebhookService
 * @package Modules\Feedbacks\Services\Facebook
 */
class FacebookWebhookService
{
    private string $object;

    private array $entry;

    private array $config;

    public static array $classMap = [
        ObjectInterface::OBJECT_PAGE => PageWebhook::class,
    ];

    public function __construct(string $object, array $entry, array $config)
    {
        $this->object = $object;
        $this->entry = $entry;
        $this->config = $config;
    }

    public function process(): void
    {
        try {
            if (!in_array($this->object, array_keys(self::$classMap), true)) {
                $this->getLogger()->info(sprintf('FB webhook - unsupported "object" value: %s', $this->object));
                return;
            }

            $className = self::$classMap[$this->object];

            if (class_exists($className)) {
                /** @var BaseWebhook $model */
                $model = new $className($this->entry);
                $model->setLogger($this->getLogger())->setConfig(Arr::get($this->config, 'webhooks.' . $this->object, []))->process();
            } else {
                throw new Exception(sprintf('Non exist FB webhook class "%s"', $className));
            }
        } catch (Exception $exception) {
            $this->getLogger()->error($exception->getMessage());
        }
    }

    public function getLogger(): LoggerInterface
    {
        return Log::channel('fb-webhook');
    }
}
