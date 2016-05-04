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

/**
 * Notification handler.
 */
class Notification
{
    use ParameterTrait;

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
     * @var \Jgut\Tify\Message
     */
    protected $message;

    /**
     * @var \Jgut\Tify\Recipient\AbstractRecipient[]
     */
    protected $recipients = [];

    /**
     * Notification results.
     *
     * @var \Jgut\Tify\Result[]
     */
    protected $results;

    /**
     * Notification constructor.
     *
     * @param \Jgut\Tify\Message                   $message
     * @param \Jgut\Tify\Recipient\ApnsRecipient[] $recipients
     * @param array                                $parameters
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(Message $message, array $recipients = [], array $parameters = [])
    {
        $this->message = $message;

        foreach ($recipients as $recipient) {
            $this->addRecipient($recipient);
        }

        $this->setParameters(array_merge($this->defaultParameters, $parameters));

        $this->results = new ArrayCollection;
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
