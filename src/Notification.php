<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify;

use Doctrine\Common\Collections\ArrayCollection;
use Jgut\Tify\Receiver\Receiver;

/**
 * Notification handler.
 */
class Notification
{
    const TTL_NONE = 0;
    const TTL_IMMEDIATE = 3600; // 1 hour
    const TTL_SHORT = 86400; // 1 day
    const TTL_NORMAL = 604800; // 1 week
    const TTL_EXTENDED = 1209600; // 2 week
    const TTL_LONG = 1814400; // 3 week
    const TTL_EXTRA_LONG = 2419200; // 4 weeks

    use ParameterTrait {
        ParameterTrait::setParameter as setDefinedParameter;
    }

    /**
     * Default notification parameters.
     *
     * For an iOS silent notification leave "badge", "sound" and "content-available" to null.
     *
     * @see https://developer.apple.com/library/ios/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG
     *          /Chapters/TheNotificationPayload.html
     * @see https://developers.google.com/cloud-messaging/http-server-ref#downstream-http-messages-json
     *
     * @var array
     */
    protected $defaultParameters = [
        // APNS
        'badge' => null,
        'sound' => null,
        'content-available' => null, // 1 for silence notifications on iOS
        'category' => null,
        'url-args' => null,
        'expire' => self::TTL_EXTENDED,

        // GCM
        'collapse_key' => null,
        'delay_while_idle' => false,
        'time_to_live' => self::TTL_EXTENDED,
        'restricted_package_name' => null,
        'dry_run' => false, // Best using sandbox at adapter level
    ];

    /**
     * Notification's message.
     *
     * @var Message
     */
    protected $message;

    /**
     * Notification list of receivers.
     *
     * @var Receiver[]
     */
    protected $receivers;

    /**
     * Notification constructor.
     *
     * @param Message             $message
     * @param Receiver|Receiver[] $receivers
     * @param array               $parameters
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(Message $message, $receivers = null, array $parameters = [])
    {
        $this->setParameters(array_merge($this->defaultParameters, $parameters));

        $this->message = $message;

        $this->receivers = new ArrayCollection;
        if ($receivers !== null) {
            if (!is_array($receivers)) {
                $receivers = [$receivers];
            }

            $this->setReceivers($receivers);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setParameter($parameter, $value)
    {
        if (!array_key_exists($parameter, $this->defaultParameters)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid notification parameter', $parameter));
        }

        return $this->setDefinedParameter($parameter, $value);
    }

    /**
     * Get message.
     *
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set message.
     *
     * @param Message $message
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setMessage(Message $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Retrieve list of receivers.
     *
     * @return Receiver[]
     */
    public function getReceivers()
    {
        return $this->receivers->toArray();
    }

    /**
     * Register receivers.
     *
     * @param array $receivers
     *
     * @return $this
     */
    public function setReceivers(array $receivers)
    {
        $this->receivers->clear();

        foreach ($receivers as $receiver) {
            $this->addReceiver($receiver);
        }

        return $this;
    }

    /**
     * Add receiver.
     *
     * @param Receiver $receiver
     *
     * @return $this
     */
    public function addReceiver(Receiver $receiver)
    {
        $this->receivers->add($receiver);

        return $this;
    }

    /**
     * Remove all receivers.
     *
     * @return $this
     */
    public function clearReceivers()
    {
        $this->receivers->clear();

        return $this;
    }
}
