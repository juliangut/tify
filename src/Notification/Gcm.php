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
use Jgut\Tify\Device\AbstractDevice;
use Jgut\Tify\Device\Gcm as GcmDevice;
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
     * @param \Jgut\Tify\Service\Gcm  $service
     * @param \Jgut\Tify\Message\Gcm  $message
     * @param \Jgut\Tify\Device\Gcm[] $devices
     * @param array                   $options
     */
    public function __construct(GcmService $service, GcmMessage $message, array $devices = [], array $options = [])
    {
        parent::__construct($service, $message, $devices, $options);
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
    public function addDevice(AbstractDevice $device)
    {
        if (!$device instanceof GcmDevice) {
            throw new \InvalidArgumentException('Device must be an accepted GCM device');
        }

        $this->devices[] = $device;

        return $this;
    }
}
