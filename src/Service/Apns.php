<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Service;

use DateTime;
use DateTimeZone;
use InvalidArgumentException;
use Jgut\Pushat\Exception\ServiceException;
use Jgut\Pushat\Exception\NotificationException;
use Jgut\Pushat\Notification\AbstractNotification;
use Jgut\Pushat\Notification\Apns as ApnsNotification;
use Jgut\Pushat\Service\Client\ApnsBuilder as ClientBuilder;
use Jgut\Pushat\Service\Message\ApnsBuilder as MessageBuilder;
use ZendService\Apple\Apns\Response\Message as PushServiceResponse;
use ZendService\Apple\Exception\RuntimeException as ServiceRuntimeException;

class Apns extends AbstractService implements PushInterface, FeedbackInterface
{
    /**
     * {@inheritdoc}
     */
    protected $definedParameters = [
        'certificate',
        'pass_phrase',
    ];

    /**
     * {@inheritdoc}
     */
    protected $defaultParameters = [
        'pass_phrase' => null,
    ];

    /**
     * {@inheritdoc}
     */
    protected $requiredParameters = [
        'certificate',
    ];

    /**
     * @var \ZendService\Apple\Apns\Client\Message
     */
    protected $pushClient;

    /**
     * @var \ZendService\Apple\Apns\Client\Feedback
     */
    protected $feedbackClient;

    /**
     * {@inheritdoc}
     *
     * @throws \Jgut\Pushat\Exception\ServiceException
     */
    public function __construct($environment = AbstractService::ENVIRONMENT_PROD, array $parameters = [])
    {
        parent::__construct($environment, $parameters);

        $certificatePath = $this->getParameter('certificate');

        if ($certificatePath === null || !file_exists($certificatePath)) {
            throw new ServiceException(sprintf('Certificate file "%s" does not exist', $certificatePath));
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     * @throws \Jgut\Pushat\Exception\NotificationException
     */
    public function send(AbstractNotification $notification)
    {
        if (!$notification instanceof ApnsNotification) {
            throw new InvalidArgumentException('Notification must be an accepted APNS notification');
        }

        $service = $this->getPushService();

        $pushedDevices = [];

        foreach ($notification->getDevices() as $device) {
            $message = MessageBuilder::build($device, $notification);

            try {
                $this->response = $service->send($message);
            } catch (ServiceRuntimeException $exception) {
                throw new NotificationException($exception->getMessage(), $exception->getCode(), $exception);
            }

            if ($this->response->getCode() === PushServiceResponse::RESULT_OK) {
                $pushedDevices[] = $device;
            }
        }

        return $pushedDevices;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Jgut\Pushat\Exception\NotificationException
     */
    public function feedback()
    {
        $service = $this->getFeedbackService();

        try {
            $feedbackResponse = $service->feedback();
        } catch (ServiceRuntimeException $exception) {
            throw new NotificationException($exception->getMessage(), $exception->getCode(), $exception);
        }

        $responses = [];

        foreach ($feedbackResponse as $response) {
            $time = new DateTime(date('c', $response->getTime()));
            $time->setTimeZone(new DateTimeZone('UTC'));

            $responses[$response->getToken()] = $time;
        }

        return $responses;
    }

    /**
     * Get opened ServiceClient
     *
     * @return \ZendService\Apple\Apns\Client\AbstractClient
     */
    protected function getPushService()
    {
        if (!isset($this->pushClient)) {
            $this->pushClient = ClientBuilder::buildPush(
                $this->getParameter('certificate'),
                $this->getParameter('pass_phrase'),
                $this->isProductionEnvironment()
            );
        }

        return $this->pushClient;
    }

    /**
     * Get opened ServiceFeedbackClient
     *
     * @return \ZendService\Apple\Apns\Client\AbstractClient
     */
    protected function getFeedbackService()
    {
        if (!isset($this->feedbackClient)) {
            $this->feedbackClient = ClientBuilder::buildFeedback(
                $this->getParameter('certificate'),
                $this->getParameter('pass_phrase'),
                $this->isProductionEnvironment()
            );
        }

        return $this->feedbackClient;
    }
}
