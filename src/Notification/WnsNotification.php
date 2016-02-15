<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Notification;

use Jgut\Tify\Message\AbstractMessage;
use Jgut\Tify\Message\WnsMessage;
use Jgut\Tify\Recipient\AbstractRecipient;
use Jgut\Tify\Recipient\WnsRecipient;
use Jgut\Tify\Service\AbstractService;
use Jgut\Tify\Service\Message\WnsMessage as WnsServiceMessage;
use Jgut\Tify\Service\WnsService;

class WnsNotification extends AbstractNotification
{
    /**
     * @param \Jgut\Tify\Service\WnsService       $service
     * @param \Jgut\Tify\Message\WnsMessage       $message
     * @param \Jgut\Tify\Recipient\WnsRecipient[] $recipients
     * @param array                               $options
     */
    public function __construct(WnsService $service, WnsMessage $message, array $recipients = [], array $options = [])
    {
        parent::__construct($service, $message, $recipients, $options);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultOptions()
    {
        return [
            'target' => WnsServiceMessage::TARGET_TOAST,
            'priority' => WnsServiceMessage::CLASS_IMMEDIATE_TOAST,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setService(AbstractService $service)
    {
        if (!$service instanceof WnsService) {
            throw new \InvalidArgumentException('Service must be an accepted Windows Phone service');
        }

        $this->service = $service;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setMessage(AbstractMessage $message)
    {
        if (!$message instanceof WnsMessage) {
            throw new \InvalidArgumentException('Message must be an accepted Windows Phone message');
        }

        $this->message = $message;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function addRecipient(AbstractRecipient $recipient)
    {
        if (!$recipient instanceof WnsRecipient) {
            throw new \InvalidArgumentException('Recipient must be an accepted Windows Phone recipient');
        }

        $this->recipients[base64_encode($recipient->getToken())] = $recipient;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function getTokens()
    {
        return array_map(
            function ($recipient) {
                return base64_decode($recipient);
            },
            array_keys($this->recipients)
        );
    }
}
