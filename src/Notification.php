<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify;

use Doctrine\Common\Collections\ArrayCollection;
use Jgut\Tify\Adapter\Gcm\GcmMessage;
use Jgut\Tify\Receiver\AbstractReceiver;

/**
 * Notification handler.
 */
class Notification
{
    use ParameterTrait {
        ParameterTrait::setParameter as setDefinedParameter;
    }

    /**
     * Default notification parameters.
     *
     * For an iOS silent notification leave "badge" and "sound" to null and set "content-available" to true
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
        'content-available' => null, // true|1 for silence notifications on iOS
        'category' => null,
        'url-args' => null,
        'expire' => null,

        // GCM
        'collapse_key' => null,
        'delay_while_idle' => null,
        'time_to_live' => GcmMessage::DEFAULT_TTL, // 4 weeks
        'restricted_package_name' => null,
        'dry_run' => null,
    ];

    /**
     * @var \Jgut\Tify\Message
     */
    protected $message;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $receivers;

    /**
     * Notification constructor.
     *
     * @param \Jgut\Tify\Message                                                                $message
     * @param \Jgut\Tify\Receiver\AbstractReceiver|\Jgut\Tify\Receiver\AbstractReceiver[]| null $receivers
     * @param array                                                                             $parameters
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
     * @return \Jgut\Tify\Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set message.
     *
     * @param \Jgut\Tify\Message $message
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
     * @return \Jgut\Tify\Receiver\AbstractReceiver[]
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
     * @param \Jgut\Tify\Receiver\AbstractReceiver $receiver
     *
     * @return $this
     */
    public function addReceiver(AbstractReceiver $receiver)
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
