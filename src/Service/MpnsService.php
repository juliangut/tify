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
use Jgut\Tify\Notification\MpnsNotification;
use Jgut\Tify\Result;
use Jgut\Tify\Service\Client\MpnsClient;
use Jgut\Tify\Service\Message\MpnsMessageBuilder;

class MpnsService extends AbstractService implements SendInterface
{
    const RESULT_OK = 'OK';

    /**
     * Status codes translation.
     *
     * @see https://msdn.microsoft.com/en-us/library/windows/apps/ff941100%28v=vs.105%29.aspx
     *
     * @var array
     */
    private static $statusCodes = [
        'OK' => 'OK',
        'queueFull' => 'Device message queue is full, message was discarded',
        'suppressed' => 'Notification was suppressed by Push Notification Service',
        'unauthorized' => 'Sending message is unauthorized',
        'expired' => 'The subscription is invalid and is not present on the Push Notification Service',
        'limitReached' => 'Notifications limit reached',
        'disconnected' => 'The device is in a disconnected state',
        'unavailable' => 'The Push Notification Service is unable to process the request',
        'unknownError' => 'Unknown Error',
    ];

    /**
     * @var \Jgut\Tify\Service\Client\MpnsClient
     */
    protected $pushClient;

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function send(AbstractNotification $notification)
    {
        if (!$notification instanceof MpnsNotification) {
            throw new \InvalidArgumentException('Notification must be an accepted Windows Phone notification');
        }

        $service = $this->getPushService();
        $message = MpnsMessageBuilder::build($notification);

        $results = [];

        foreach ($notification->getTokens() as $recipientToken) {
            $time = new \DateTime;

            $response = $service->send($recipientToken, $message);

            $responseStatus = $this->parseResponseStatus($response);

            if ($responseStatus === self::RESULT_OK) {
                $result = new Result($recipientToken, $time);
            } else {
                $result = new Result(
                    $recipientToken,
                    $time,
                    Result::STATUS_ERROR,
                    self::$statusCodes[$responseStatus]
                );
            }

            $results[] = $result;
        }

        $notification->setSent($results);
    }

    /**
     * Get opened client.
     *
     * @return \Jgut\Tify\Service\Client\MpnsClient
     */
    protected function getPushService()
    {
        if (!isset($this->pushClient)) {
            $this->pushClient = new MpnsClient;
        }

        return $this->pushClient;
    }

    /**
     * Parse response status parts to identify notification final status.
     *
     * @param array $responseStatus
     *
     * @return string
     */
    protected function parseResponseStatus(array $responseStatus)
    {
        static $httpStatusMapper = [
            401 => 'unauthorized',
            404 => 'expired',
            406 => 'limitReached',
            412 => 'disconnected',
            503 => 'unavailable',
        ];

        if ($responseStatus['Status'] === 200) {
            return $this->parse200ResponseStatus($responseStatus);
        }

        if (in_array((int) $responseStatus['Status'], array_keys($httpStatusMapper))) {
            return $httpStatusMapper[(int) $responseStatus['Status']];
        }

        return 'unknownError';
    }

    /**
     * Parse HTTP 200 status response to identify notification final status.
     *
     * Windows Phone Push Notification Service headers available to check against:
     *  X-NotificationStatus
     *  X-DeviceConnectionStatus
     *  X-SubscriptionStatus
     *
     * @param array $responseStatus
     *
     * @return string
     */
    protected function parse200ResponseStatus(array $responseStatus)
    {
        static $statusMapper = [
            'received' => self::RESULT_OK,
            'queuefull' => 'queueFull',
            'suppressed' => 'suppressed',
        ];

        if (in_array(strtolower($responseStatus['X-NotificationStatus']), array_keys($statusMapper))) {
            return $statusMapper[strtolower($responseStatus['X-NotificationStatus'])];
        }

        return 'unknownError';
    }
}
