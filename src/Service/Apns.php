<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Service;

use Jgut\Tify\Exception\NotificationException;
use Jgut\Tify\Exception\ServiceException;
use Jgut\Tify\Notification\AbstractNotification;
use Jgut\Tify\Notification\Apns as ApnsNotification;
use Jgut\Tify\Service\Client\ApnsBuilder as ClientBuilder;
use Jgut\Tify\Service\Message\ApnsBuilder as MessageBuilder;
use ZendService\Apple\Exception\RuntimeException as ServiceRuntimeException;

class Apns extends AbstractService implements SendInterface, FeedbackInterface
{
    const RESULT_OK = 0;

    /**
     * Status codes translation.
     *
     * @var array
     */
    private $statusCodes = [
        0 => 'OK',
        1 => 'Processing Error',
        2 => 'Missing Device Token',
        3 => 'Missing Topic',
        4 => 'Missing Payload',
        5 => 'Invalid Token Size',
        6 => 'Invalid Topic Size',
        7 => 'Invalid Payload Size',
        8 => 'Invalid Token',
        10 => 'Shutdown',
        255 => 'Unknown Error',
    ];

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
     * @throws \Jgut\Tify\Exception\ServiceException
     */
    public function __construct(array $parameters = [], $environment = self::ENVIRONMENT_PROD)
    {
        parent::__construct($parameters, $environment);

        $certificatePath = $this->getParameter('certificate');

        if ($certificatePath === null || !file_exists($certificatePath)) {
            throw new ServiceException(sprintf('Certificate file "%s" does not exist', $certificatePath));
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function send(AbstractNotification $notification)
    {
        if (!$notification instanceof ApnsNotification) {
            throw new \InvalidArgumentException('Notification must be an accepted APNS notification');
        }

        $service = $this->getPushService();

        $pushedDevices = [];

        foreach ($notification->getDevices() as $device) {
            $message = MessageBuilder::build($device, $notification);

            $pushedDevice = [
                'token' => $device->getToken(),
                'date' => new \DateTime,
            ];

            try {
                $response = $service->send($message);

                if ($response->getCode() !== static::RESULT_OK) {
                    $pushedDevice['error'] = $this->statusCodes[$response->getCode()];
                }
            } catch (ServiceRuntimeException $exception) {
                $pushedDevice['error'] = $exception->getMessage();
            }

            $pushedDevices[] = $pushedDevice;
        }

        $notification->setPushed($pushedDevices);

        $service->close();
        $this->pushClient = null;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Jgut\Tify\Exception\NotificationException
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
            $time = new \DateTime(date('c', $response->getTime()));

            $responses[$response->getToken()] = $time;
        }

        $service->close();
        $this->feedbackClient = null;

        return $responses;
    }

    /**
     * Get opened ServiceClient
     *
     * @return \ZendService\Apple\Apns\Client\Message
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
     * @return \ZendService\Apple\Apns\Client\Feedback
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
