<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Notification;

use Jgut\Tify\Service\AbstractService;
use Jgut\Tify\Service\WpService;
use Jgut\Tify\Recipient\AbstractRecipient;
use Jgut\Tify\Recipient\WpRecipient;
use Jgut\Tify\Message\AbstractMessage;
use Jgut\Tify\Message\WpMessage;

class WpNotification extends AbstractNotification
{
    /**
     * @param \Jgut\Tify\Service\WpService       $service
     * @param \Jgut\Tify\Message\WpMessage       $message
     * @param \Jgut\Tify\Recipient\WpRecipient[] $recipients
     * @param array                              $options
     */
    public function __construct(WpService $service, WpMessage $message, array $recipients = [], array $options = [])
    {
        parent::__construct($service, $message, $recipients, $options);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setService(AbstractService $service)
    {
        if (!$service instanceof WpService) {
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
        if (!$message instanceof WpMessage) {
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
        if (!$recipient instanceof WpRecipient) {
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
