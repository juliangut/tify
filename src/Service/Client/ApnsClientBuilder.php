<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Service\Client;

use Jgut\Tify\Exception\ServiceException;
use ZendService\Apple\Apns\Client\AbstractClient;
use ZendService\Apple\Apns\Client\Feedback as FeedbackClient;
use ZendService\Apple\Apns\Client\Message as MessageClient;

/**
 * Class ApnsClientBuilder
 */
class ApnsClientBuilder
{
    /**
     * Get opened push service client.
     *
     * @param string $certificate
     * @param string $passPhrase
     * @param bool   $sandbox
     *
     * @throws \Jgut\Tify\Exception\ServiceException
     *
     * @return \ZendService\Apple\Apns\Client\Message
     */
    public static function buildPush($certificate, $passPhrase = '', $sandbox = false)
    {
        return static::buildClient(new MessageClient, $certificate, $passPhrase, $sandbox);
    }

    /**
     * Get opened feedback service client.
     *
     * @param string $certificate
     * @param string $passPhrase
     * @param bool   $sandbox
     *
     * @throws \Jgut\Tify\Exception\ServiceException
     *
     * @return \ZendService\Apple\Apns\Client\Feedback
     */
    public static function buildFeedback($certificate, $passPhrase = '', $sandbox = false)
    {
        return static::buildClient(new FeedbackClient, $certificate, $passPhrase, $sandbox);
    }

    /**
     * Get opened client.
     *
     * @param \ZendService\Apple\Apns\Client\AbstractClient $client
     * @param string                                        $certificate
     * @param string                                        $passPhrase
     * @param bool                                          $sandbox
     *
     * @throws \Jgut\Tify\Exception\ServiceException
     *
     * @return \ZendService\Apple\Apns\Client\AbstractClient
     *
     * @codeCoverageIgnore
     */
    protected static function buildClient(AbstractClient $client, $certificate, $passPhrase = '', $sandbox = false)
    {
        try {
            $client->open(
                (bool) $sandbox ? AbstractClient::SANDBOX_URI : AbstractClient::PRODUCTION_URI,
                $certificate,
                $passPhrase
            );
        } catch (\Exception $exception) {
            throw new ServiceException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $client;
    }
}
