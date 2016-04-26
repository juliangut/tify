<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Service;

use Jgut\Tify\Notification\AbstractNotification;
use Jgut\Tify\Notification\GcmNotification;
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
     */
    public function send(AbstractNotification $notification)
    {
        if (!$notification instanceof GcmNotification) {
            throw new \InvalidArgumentException('Notification must be an accepted GCM notification');
        }

        $service = $this->getPushService();

        $results = [];

        foreach (array_chunk($notification->getTokens(), 100) as $tokensRange) {
            $message = GcmMessageBuilder::build($tokensRange, $notification);

            $time = new \DateTime;

            try {
                $response = $service->send($message)->getResults();

                foreach ($tokensRange as $token) {
                    if (array_key_exists($token, $response) && !array_key_exists('error', $response[$token])) {
                        $result = new Result($token, $time);
                    } else {
                        $errorCode = array_key_exists($token, $response) ? $response[$token]['error'] : 'UnknownError';

                        $result = new Result(
                            $token,
                            $time,
                            Result::STATUS_ERROR,
                            self::$statusCodes[$errorCode]
                        );
                    }

                    $results[] = $result;
                }
            } catch (GcmRuntimeException $exception) {
                foreach ($tokensRange as $token) {
                    $results[] = new Result($token, $time, Result::STATUS_ERROR, $exception->getMessage());
                }
            }
        }

        $notification->setStatus(AbstractNotification::STATUS_SENT, $results);
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
