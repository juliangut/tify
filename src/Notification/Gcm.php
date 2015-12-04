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
use Jgut\Tify\Service\Gcm as GcmService;
use Jgut\Tify\Recipient\AbstractRecipient;
use Jgut\Tify\Recipient\Gcm as GcmRecipient;
use Jgut\Tify\Message\AbstractMessage;
use Jgut\Tify\Message\Gcm as GcmMessage;

class Gcm extends AbstractNotification
{
    /**
     * {@inheritdoc}
     */
    protected $defaultOptions = [
        'collapse_key' => null,
        'delay_while_idle' => null,
        'time_to_live' => null,
        'restricted_package_name' => null,
        'dry_run' => null,
    ];

    /**
     * @param \Jgut\Tify\Service\Gcm     $service
     * @param \Jgut\Tify\Message\Gcm     $message
     * @param \Jgut\Tify\Recipient\Gcm[] $recipients
     * @param array                      $options
     */
    public function __construct(GcmService $service, GcmMessage $message, array $recipients = [], array $options = [])
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

        $this->recipients[] = $recipient;

        return $this;
    }
}
