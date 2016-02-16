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
use Jgut\Tify\Message\MpnsMessage;
use Jgut\Tify\Recipient\AbstractRecipient;
use Jgut\Tify\Recipient\MpnsRecipient;
use Jgut\Tify\Service\AbstractService;
use Jgut\Tify\Service\Message\MpnsMessage as MpnsServiceMessage;
use Jgut\Tify\Service\MpnsService;

class MpnsNotification extends AbstractNotification
{
    /**
     * @param \Jgut\Tify\Service\MpnsService       $service
     * @param \Jgut\Tify\Message\MpnsMessage       $message
     * @param \Jgut\Tify\Recipient\MpnsRecipient[] $recipients
     * @param array                                $options
     */
    public function __construct(MpnsService $service, MpnsMessage $message, array $recipients = [], array $options = [])
    {
        parent::__construct($service, $message, $recipients, $options);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultOptions()
    {
        return [
            'id' => null,
            'target' => MpnsServiceMessage::TARGET_TOAST,
            'class' => MpnsServiceMessage::CLASS_IMMEDIATE_TOAST,
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
        if (!$service instanceof MpnsService) {
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
        if (!$message instanceof MpnsMessage) {
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
        if (!$recipient instanceof MpnsRecipient) {
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
