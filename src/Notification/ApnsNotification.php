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
use Jgut\Tify\Message\ApnsMessage;
use Jgut\Tify\Recipient\AbstractRecipient;
use Jgut\Tify\Recipient\ApnsRecipient;
use Jgut\Tify\Service\AbstractService;
use Jgut\Tify\Service\ApnsService;

class ApnsNotification extends AbstractNotification
{
    /**
     * @param \Jgut\Tify\Service\ApnsService       $service
     * @param \Jgut\Tify\Message\ApnsMessage       $message
     * @param \Jgut\Tify\Recipient\ApnsRecipient[] $recipients
     * @param array                                $options
     */
    public function __construct(ApnsService $service, ApnsMessage $message, array $recipients = [], array $options = [])
    {
        parent::__construct($service, $message, $recipients, $options);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultOptions()
    {
        return [
            'expire' => null,
            'badge' => null,
            'sound' => null,
            'content_available' => null,
            'category' => null,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setService(AbstractService $service)
    {
        if (!$service instanceof ApnsService) {
            throw new \InvalidArgumentException('Service must be an accepted APNS service');
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
        if (!$message instanceof ApnsMessage) {
            throw new \InvalidArgumentException('Message must be an accepted APNS message');
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
        if (!$recipient instanceof ApnsRecipient) {
            throw new \InvalidArgumentException('Recipient must be an accepted APNS recipient');
        }

        $this->recipients[$recipient->getToken()] = $recipient;

        return $this;
    }
}
