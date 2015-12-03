<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Notification;

use Jgut\Tify\Device\AbstractDevice;
use Jgut\Tify\Message\AbstractMessage;
use Jgut\Tify\OptionsTrait;
use Jgut\Tify\Service\AbstractService;

abstract class AbstractNotification
{
    const STATUS_PENDING = 0;
    const STATUS_PUSHED = 1;

    use OptionsTrait;

    /**
     * Default notification options.
     *
     * @var array
     */
    protected $defaultOptions = [];

    /**
     * @var \Jgut\Tify\Service\AbstractService
     */
    protected $service;

    /**
     * @var \Jgut\Tify\Message\AbstractMessage
     */
    protected $message;

    /**
     * @var \Jgut\Tify\Device\AbstractDevice[]
     */
    protected $devices = [];

    /**
     * @var int
     */
    protected $status = self::STATUS_PENDING;

    /**
     * @var \DateTime
     */
    protected $pushTime;

    /**
     * Push results.
     *
     * @var array
     */
    protected $result = [];

    /**
     * @param \Jgut\Tify\Service\AbstractService $service
     * @param \Jgut\Tify\Message\AbstractMessage $message
     * @param \Jgut\Tify\Device\AbstractDevice[] $devices
     * @param array                              $options
     */
    public function __construct(
        AbstractService $service,
        AbstractMessage $message,
        array $devices = [],
        array $options = []
    ) {
        $this->service = $service;
        $this->message = $message;

        foreach ($devices as $device) {
            $this->addDevice($device);
        }

        $this->setOptions(array_merge($this->defaultOptions, $options));
    }

    /**
     * Get service.
     *
     * @return \Jgut\Tify\Service\AbstractService
     */
    final public function getService()
    {
        return $this->service;
    }

    /**
     * Set service.
     *
     * @param \Jgut\Tify\Service\AbstractService $service
     */
    abstract public function setService(AbstractService $service);

    /**
     * Get message.
     *
     * @return \Jgut\Tify\Message\AbstractMessage
     */
    final public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set message.
     *
     * @param \Jgut\Tify\Message\AbstractMessage $message
     */
    abstract public function setMessage(AbstractMessage $message);

    /**
     * Retrieve list of devices.
     *
     * @return \Jgut\Tify\Device\AbstractDevice[]
     */
    final public function getDevices()
    {
        return $this->devices;
    }

    /**
     * Add device.
     *
     * @param \Jgut\Tify\Device\AbstractDevice $device
     */
    abstract public function addDevice(AbstractDevice $device);

    /**
     * Retrieve notification status.
     *
     * @return int
     */
    final public function getStatus()
    {
        return $this->status;
    }

    /**
     * Check if notification status is pushed.
     *
     * @return bool
     */
    final public function isPushed()
    {
        return $this->status === static::STATUS_PUSHED;
    }

    /**
     * Set notification pushed.
     *
     * @param array $result
     */
    final public function setPushed(array $result = [])
    {
        $this->status = static::STATUS_PUSHED;
        $this->pushTime = new \DateTime;
        $this->result = $result;
    }

    /**
     * Set notification pending (not pushed).
     */
    final public function setPending()
    {
        $this->status = static::STATUS_PENDING;
        $this->pushTime = null;
        $this->result = [];
    }

    /**
     * Retrieve push time.
     *
     * @return \DateTime
     */
    final public function getPushTime()
    {
        return $this->pushTime;
    }

    /**
     * Retrieve push result.
     *
     * @return array
     */
    final public function getResult()
    {
        return $this->result;
    }

    /**
     * Retrieve devices tokens list.
     *
     * @return array
     */
    final public function getTokens()
    {
        $tokens = [];

        foreach ($this->devices as $device) {
            $tokens[] = $device->getToken();
        }

        return array_unique(array_filter($tokens));
    }
}
