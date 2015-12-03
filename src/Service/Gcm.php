<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Service;

use Jgut\Pushat\Notification\AbstractNotification;
use Jgut\Pushat\Notification\Gcm as GcmNotification;
use Jgut\Pushat\Service\Client\GcmBuilder as ClientBuilder;
use Jgut\Pushat\Service\Message\GcmBuilder as MessageBuilder;
use ZendService\Google\Exception\RuntimeException as ServiceRuntimeException;

class Gcm extends AbstractService implements PushInterface
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
        'NotRegistered' => 'Unregistered Device',
        'InvalidPackageName' => 'Invalid Package Name',
        'MismatchSenderId' => 'Mismatched Sender',
        'MessageTooBig' => 'Message Too Big',
        'InvalidDataKey' => 'Invalid Data Key',
        'InvalidTtl' => 'Invalid Time to Live',
        'Unavailable' => 'Timeout',
        'InternalServerError' => 'Internal Server Error',
        'DeviceMessageRateExceeded' => 'Device Message Rate Exceeded',
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

        $pushedDevices = [];

        foreach (array_chunk($notification->getTokens(), 100) as $tokensRange) {
            $message = MessageBuilder::build($tokensRange, $notification);

            $time = new \DateTime;

            try {
                $response = $service->send($message);
                $results = $response->getResults();

                foreach ($tokensRange as $token) {
                    $result = isset($results[$token]) ? $results[$token] : [];

                    $pushedDevice = [
                        'token' => $token,
                        'date' => $time,
                    ];

                    if (isset($result['error'])) {
                        $pushedDevice['error'] = $this->statusCodes[$result['error']];
                    }

                    $pushedDevices[] = $pushedDevice;
                }
            } catch (ServiceRuntimeException $exception) {
                foreach ($tokensRange as $token) {
                    $pushedDevices[] = [
                        'token' => $token,
                        'date' => $time,
                        'error' => $exception->getMessage(),
                    ];
                }
            }
        }

        $notification->setPushed($pushedDevices);
    }

    /**
     * Get opened client.
     *
     * @return \ZendService\Google\Gcm\Client
     */
    protected function getPushService()
    {
        if (!isset($this->pushClient)) {
            $this->pushClient = ClientBuilder::buildPush();
        }

        return $this->pushClient;
    }
}
