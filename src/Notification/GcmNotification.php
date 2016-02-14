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
use Jgut\Tify\Service\GcmService as GcmService;
use Jgut\Tify\Recipient\AbstractRecipient;
use Jgut\Tify\Recipient\GcmRecipient as GcmRecipient;
use Jgut\Tify\Message\AbstractMessage;
use Jgut\Tify\Message\GcmMessage as GcmMessage;

class GcmNotification extends AbstractNotification
{
    /**
     * @param \Jgut\Tify\Service\GcmService       $service
     * @param \Jgut\Tify\Message\GcmMessage       $message
     * @param \Jgut\Tify\Recipient\GcmRecipient[] $recipients
     * @param array                               $options
     */
    public function __construct(GcmService $service, GcmMessage $message, array $recipients = [], array $options = [])
    {
        parent::__construct($service, $message, $recipients, $options);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultOptions()
    {
        return [
            'collapse_key' => null,
            'delay_while_idle' => null,
            'time_to_live' => 2419200,
            'restricted_package_name' => null,
            'dry_run' => null,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setService(AbstractService $service)
    {
        if (!$service instanceof GcmService) {
            throw new \InvalidArgumentException('Service must be an accepted GCM service');
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
        if (!$message instanceof GcmMessage) {
            throw new \InvalidArgumentException('Message must be an accepted GCM message');
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
        if (!$recipient instanceof GcmRecipient) {
            throw new \InvalidArgumentException('Recipient must be an accepted GCM recipient');
        }

        $this->recipients[$recipient->getToken()] = $recipient;

        return $this;
    }
}
