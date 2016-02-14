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
use Jgut\Tify\Notification\GcmNotification as GcmNotification;
use Jgut\Tify\Result;
use Jgut\Tify\Service\Client\GcmBuilder as ClientBuilder;
use Jgut\Tify\Service\Message\GcmBuilder as MessageBuilder;
use ZendService\Google\Exception\RuntimeException as ServiceRuntimeException;

class GcmService extends AbstractService implements SendInterface
{
    /**
     * Status codes translation.
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

        $service = $this->getPushService($this->getParameter('api_key'));

        $results = [];

        foreach (array_chunk($notification->getTokens(), 100) as $tokensRange) {
            $message = MessageBuilder::build($tokensRange, $notification);

            $time = new \DateTime;

            try {
                $response = $service->send($message)->getResults();

                foreach ($tokensRange as $token) {
                    if (isset($response[$token]) && !isset($response[$token]['error'])) {
                        $result = new Result($token, $time);
                    } elseif (isset($response[$token])) {
                        $result = new Result(
                            $token,
                            $time,
                            Result::STATUS_ERROR,
                            self::$statusCodes[$response[$token]['error']]
                        );
                    } else {
                        $result = new Result(
                            $token,
                            $time,
                            Result::STATUS_ERROR,
                            self::$statusCodes['UnknownError']
                        );
                    }

                    $results[] = $result;
                }
            } catch (ServiceRuntimeException $exception) {
                foreach ($tokensRange as $token) {
                    $results[] = new Result($token, $time, Result::STATUS_ERROR, $exception->getMessage());
                }
            }
        }

        $notification->setSent($results);
    }

    /**
     * Get opened client.
     *
     * @param string $apiKey
     *
     * @return \ZendService\Google\Gcm\Client
     */
    protected function getPushService($apiKey)
    {
        if (!isset($this->pushClient)) {
            $this->pushClient = ClientBuilder::buildPush($apiKey);
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
    protected function getDefaultParameters()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRequiredParameters()
    {
        return ['api_key'];
    }
}
