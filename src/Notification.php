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
use Jgut\Tify\Recipient\AbstractRecipient;
use Jgut\Tify\Service\AbstractService;

/**
 * Notification handler.
 */
abstract class Notification
{
    use ParameterTrait;

    const STATUS_PENDING = 0;
    const STATUS_SENT = 1;

    /**
     * Default notification parameters.
     *
     * @var array
     */
    protected $defaultParameters = [
        // APNS
        'expire' => null,
        'badge' => null,
        'sound' => null,
        'content_available' => null,
        'category' => null,

        // GCM
        'collapse_key' => null,
        'delay_while_idle' => null,
        'time_to_live' => 2419200,
        'restricted_package_name' => null,
        'dry_run' => null,
    ];

    /**
     * @var \Jgut\Tify\Service\AbstractService
     */
    protected $service;

    /**
     * @var \Jgut\Tify\Message
     */
    protected $message;

    /**
     * @var \Jgut\Tify\Recipient\AbstractRecipient[]
     */
    protected $recipients = [];

    /**
     * @var int
     */
    protected $status = self::STATUS_PENDING;

    /**
     * Notification results.
     *
     * @var \Jgut\Tify\Result[]
     */
    protected $results;

    /**
     * Notification constructor.
     *
     * @param \Jgut\Tify\Service\AbstractService       $service
     * @param \Jgut\Tify\Message                       $message
     * @param \Jgut\Tify\Recipient\ApnsRecipient[]     $recipients
     * @param array                                    $parameters
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        AbstractService $service,
        Message         $message,
        array           $recipients = [],
        array           $parameters = []
    ) {
        $this->service = $service;
        $this->message = $message;

        foreach ($recipients as $recipient) {
            $this->addRecipient($recipient);
        }

        $this->setParameters(array_merge($this->defaultParameters, $parameters));

        $this->status = self::STATUS_PENDING;
        $this->results = new ArrayCollection;
    }

    /**
     * Get service.
     *
     * @return \Jgut\Tify\Service\AbstractService
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set service.
     *
     * @param \Jgut\Tify\Service\AbstractService $service
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setService(AbstractService $service)
    {
        $this->service = $service;

        return $this;
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
     * Retrieve list of recipients.
     *
     * @return \Jgut\Tify\Recipient\AbstractRecipient[]
     */
    public function getRecipients()
    {
        return array_values($this->recipients);
    }

    /**
     * Add recipient.
     *
     * @param \Jgut\Tify\Recipient\AbstractRecipient $recipient
     *
     * @return $this
     */
    public function addRecipient(AbstractRecipient $recipient)
    {
        $this->recipients[$recipient->getToken()] = $recipient;

        return $this;
    }

    /**
     * Remove all recipients.
     *
     * @return $this
     */
    public function clearRecipients()
    {
        $this->recipients = [];

        return $this;
    }

    /**
     * Retrieve notification status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Check if notification status is sent.
     *
     * @return bool
     */
    public function isSent()
    {
        return $this->status === static::STATUS_SENT;
    }

    /**
     * Check if notification status is pending.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === static::STATUS_PENDING;
    }

    /**
     * Set notification status.
     *
     * @param int $status
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setStatus($status)
    {
        if (!in_array($status, [self::STATUS_PENDING, self::STATUS_SENT])) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid status', $status));
        }

        $this->status = $status;

        return $this;
    }

    /**
     * Retrieve results.
     *
     * @return \Jgut\Tify\Result[]
     */
    public function getResults()
    {
        return $this->results->toArray();
    }

    /**
     * Add push result.
     *
     * @param \Jgut\Tify\Result $result
     */
    public function addResult(Result $result)
    {
        $this->results->add($result);
    }

    /**
     * Clear push results.
     *
     * @return $this
     */
    public function clearResults()
    {
        $this->results->clear();

        return $this;
    }
}
