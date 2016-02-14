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
use Jgut\Tify\Notification\WpNotification;
use Jgut\Tify\Result;
use JildertMiedema\WindowsPhone\WindowsPhonePushNotification;

class WpService extends AbstractService implements SendInterface
{
    const RESULT_OK = 'OK';

    const PRIORITY_IMMEDIATE_TILE = 1;
    const PRIORITY_IMMEDIATE_TOAST = 2;
    const PRIORITY_IMMEDIATE_RAW = 3;
    const PRIORITY_DELAY450_TILE = 11;
    const PRIORITY_DELAY450_TOAST = 12;
    const PRIORITY_DELAY450_RAW = 13;
    const PRIORITY_DELAY900_TILE = 21;
    const PRIORITY_DELAY900_TOAST = 22;
    const PRIORITY_DELAY900_RAW = 23;

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
     * @var \JildertMiedema\WindowsPhone\WindowsPhonePushNotification
     */
    protected $pushClient;

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function send(AbstractNotification $notification)
    {
        if (!$notification instanceof WpNotification) {
            throw new \InvalidArgumentException('Notification must be an accepted Windows Phone notification');
        }

        $service = $this->getPushService();
        $message = $notification->getMessage();

        $results = [];

        foreach ($notification->getTokens() as $recipientToken) {
            $time = new \DateTime;

            $response = $service->pushToast(
                $recipientToken,
                $message->getOption('title'),
                $message->getOption('body')
            );

            $responseStatus = $this->parseResponseStatus([
                'httpStatus' => '',
                'notificationStatus' => array_key_exists('X-NotificationStatus', $response)
                    ? $response['X-NotificationStatus']
                    : null,
                'subscriptionStatus' => array_key_exists('X-SubscriptionStatus', $response)
                    ? $response['X-SubscriptionStatus']
                    : null,
            ]);

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
     * @return \JildertMiedema\WindowsPhone\WindowsPhonePushNotification
     */
    protected function getPushService()
    {
        if (!isset($this->pushClient)) {
            $this->pushClient = new WindowsPhonePushNotification;
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

        if ($responseStatus['httpStatus'] === 200) {
            return $this->parse200ResponseStatus($responseStatus);
        }

        if (in_array($responseStatus['httpStatus'], array_keys($httpStatusMapper))) {
            return $httpStatusMapper[$responseStatus['httpStatus']];
        }

        return 'unknownError';
    }

    /**
     * Parse HTTP 200 status response to identify notification final status.
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
            'suppressed' =>'suppressed',
        ];

        if (in_array(strtolower($responseStatus['httpStatus']), array_keys($statusMapper))) {
            return $statusMapper[strtolower($responseStatus['httpStatus'])];
        }

        return 'unknownError';
    }
}
