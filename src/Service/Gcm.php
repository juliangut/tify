<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Service;

use InvalidArgumentException;
use Jgut\Pushat\Exception\NotificationException;
use Jgut\Pushat\Notification\AbstractNotification;
use Jgut\Pushat\Notification\Gcm as GcmNotification;
use Jgut\Pushat\Service\Client\GcmBuilder as ClientBuilder;
use Jgut\Pushat\Service\Message\GcmBuilder as MessageBuilder;
use ZendService\Google\Exception\RuntimeException as ServiceRuntimeException;

class Gcm extends AbstractService implements PushInterface
{
    /**
     * @var \ZendService\Google\Gcm\Client
     */
    protected $pushClient;

    /**
     * {@inheritdoc}
     *
     * @throws \Jgut\Pushat\Exception\NotificationException
     */
    public function send(AbstractNotification $notification)
    {
        if (!$notification instanceof GcmNotification) {
            throw new InvalidArgumentException('Notification must be an accepted GCM notification');
        }

        $service = $this->getPushService();

        $pushedDevices = [];

        foreach (array_chunk($notification->getDevices()->getTokens(), 100) as $tokensRange) {
            $message = MessageBuilder::build($tokensRange, $notification);

            try {
                $this->response = $service->send($message);
            } catch (ServiceRuntimeException $exception) {
                throw new NotificationException($exception->getMessage(), $exception->getCode(), $exception);
            }

            if ($this->response->getSuccessCount() > 0) {
                foreach ($tokensRange as $token) {
                    $pushedDevices[] = $notification->getDevices()->get($token);
                }
            }
        }

        return $pushedDevices;
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

    /**
     * {@inheritdoc}
     */
    protected function getDefinedParameters()
    {
        return [
            'api_key',
        ];
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
        return [
            'api_key',
        ];
    }
}
