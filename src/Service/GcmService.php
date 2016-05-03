<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Service;

use Jgut\Tify\Notification;
use Jgut\Tify\Recipient\AbstractRecipient;
use Jgut\Tify\Recipient\GcmRecipient;
use Jgut\Tify\Result;
use Jgut\Tify\Service\Client\GcmClientBuilder;
use Jgut\Tify\Service\Message\GcmMessageBuilder;
use ZendService\Google\Exception\RuntimeException as GcmRuntimeException;

/**
 * Class GcmService
 */
class GcmService extends AbstractService implements SendInterface
{
    /**
     * Status codes mapping.
     *
     * @see https://developers.google.com/cloud-messaging/http-server-ref
     *
     * @var array
     */
    private static $statusCodes = [
        'MissingRegistration' => 'Missing Registration Token',
        'InvalidRegistration' => 'Invalid Registration Token',
        'NotRegistered' => 'Unregistered Recipient',
        'InvalidPackageName' => 'Invalid Package Name',
        'MismatchSenderId' => 'Mismatched Sender',
        'MessageTooBig' => 'Message Too Big',
        'InvalidDataKey' => 'Invalid Data Key',
        'InvalidTtl' => 'Invalid Time to Live',
        'Unavailable' => 'Timeout',
        'InternalServerError' => 'Internal Server Error',
        'RecipientMessageRateExceeded' => 'Recipient Message Rate Exceeded',
        'TopicsMessageRateExceeded' => 'Topics Message Rate Exceeded',
        'UnknownError' => 'Unknown Error',
    ];

    /**
     * @var \ZendService\Google\Gcm\Client
     */
    protected $pushClient;

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function send(Notification $notification)
    {
        $service = $this->getPushService();

        foreach ($this->getPushMessages($notification) as $message) {
            /** @var \ZendService\Google\Gcm\Message $message */
            $time = new \DateTime('now', new \DateTimeZone('UTC'));

            try {
                $pushResponse = $service->send($message)->getResults();

                foreach ($message->getRegistrationIds() as $token) {
                    $result = new Result($token, $time);

                    if (!array_key_exists($token, $pushResponse) || array_key_exists('error', $pushResponse[$token])) {
                        $result->setStatus(Result::STATUS_ERROR);

                        $errorCode = array_key_exists($token, $pushResponse)
                            ? $pushResponse[$token]['error']
                            : 'UnknownError';
                        $result->setStatusMessage(self::$statusCodes[$errorCode]);
                    }

                    $notification->addResult($result);
                }
            } catch (GcmRuntimeException $exception) {
                foreach ($message->getRegistrationIds() as $token) {
                    $notification->addResult(new Result($token, $time, Result::STATUS_ERROR, $exception->getMessage()));
                }
            }
        }

        $notification->setStatus(Notification::STATUS_SENT);
    }

    /**
     * Get opened client.
     *
     * @return \ZendService\Google\Gcm\Client
     */
    protected function getPushService()
    {
        if ($this->pushClient === null) {
            $this->pushClient = GcmClientBuilder::buildPush($this->getParameter('api_key'));
        }

        return $this->pushClient;
    }

    /**
     * Get push service formatted messages.
     *
     * @param \Jgut\Tify\Notification $notification
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return \ZendService\Google\Gcm\Message
     */
    protected function getPushMessages(Notification $notification)
    {
        /** @var \Jgut\Tify\Recipient\GcmRecipient[] $recipients */
        $recipients = array_filter(
            $notification->getRecipients(),
            function (AbstractRecipient $recipient) {
                return $recipient instanceof GcmRecipient;
            }
        );

        $tokens = array_map(
            function (AbstractRecipient $recipient) {
                return $recipient->getToken();
            },
            $recipients
        );

        foreach (array_chunk($tokens, 100) as $tokensRange) {
            yield GcmMessageBuilder::build($tokensRange, $notification);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedParameters()
    {
        return ['api_key'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRequiredParameters()
    {
        return ['api_key'];
    }
}
