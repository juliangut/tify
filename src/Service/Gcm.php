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
use Jgut\Tify\Notification\Gcm as GcmNotification;
use Jgut\Tify\Service\Client\GcmBuilder as ClientBuilder;
use Jgut\Tify\Service\Message\GcmBuilder as MessageBuilder;
use ZendService\Google\Exception\RuntimeException as ServiceRuntimeException;

class Gcm extends AbstractService implements SendInterface
{
    /**
     * Status codes translation.
     *
     * @see https://developers.google.com/cloud-messaging/http-server-ref
     *
     * @var array
     */
    private $statusCodes = [
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
    ];

    /**
     * {@inheritdoc}
     */
    protected $definedParameters = [
        'api_key',
    ];

    /**
     * {@inheritdoc}
     */
    protected $requiredParameters = [
        'api_key',
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

        $pushedRecipients = [];

        foreach (array_chunk($notification->getTokens(), 100) as $tokensRange) {
            $message = MessageBuilder::build($tokensRange, $notification);

            $time = new \DateTime;

            try {
                $response = $service->send($message);
                $results = $response->getResults();

                foreach ($tokensRange as $token) {
                    $result = isset($results[$token]) ? $results[$token] : [];

                    $pushedRecipient = [
                        'token' => $token,
                        'date' => $time,
                    ];

                    if (isset($result['error'])) {
                        $pushedRecipient['error'] = $this->statusCodes[$result['error']];
                    }

                    $pushedRecipients[] = $pushedRecipient;
                }
            } catch (ServiceRuntimeException $exception) {
                foreach ($tokensRange as $token) {
                    $pushedRecipients[] = [
                        'token' => $token,
                        'date' => $time,
                        'error' => $exception->getMessage(),
                    ];
                }
            }
        }

        $notification->setSent($pushedRecipients);
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
}
