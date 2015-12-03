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
use ZendService\Apple\Apns\Client\Feedback;
use ZendService\Apple\Apns\Client\Message;

class ApnsBuilder
{
    /**
     * Get opened push service client.
     *
     * @param string $certificate
     * @param string $passPhrase
     * @param bool   $production
     *
     * @return \ZendService\Apple\Apns\Client\Message
     */
    public static function buildPush($certificate, $passPhrase = '', $production = true)
    {
        return static::buildClient(new Message, $certificate, $passPhrase, $production);
    }

    /**
     * Get opened feedback service client.
     *
     * @param string $certificate
     * @param string $passPhrase
     * @param bool   $production
     *
     * @return \ZendService\Apple\Apns\Client\Feedback
     */
    public static function buildFeedback($certificate, $passPhrase = '', $production = true)
    {
        return static::buildClient(new Feedback, $certificate, $passPhrase, $production);
    }

    /**
     * Get opened client.
     *
     * @param \ZendService\Apple\Apns\Client\AbstractClient $client
     * @param string                                        $certificate
     * @param string                                        $passPhrase
     * @param bool                                          $production
     *
     * @return \ZendService\Apple\Apns\Client\AbstractClient
     */
    protected static function buildClient(AbstractClient $client, $certificate, $passPhrase = '', $production = true)
    {
        try {
            $client->open(
                (bool) $production ? AbstractClient::PRODUCTION_URI : AbstractClient::SANDBOX_URI,
                $certificate,
                $passPhrase
            );
        } catch (\Exception $exception) {
            throw new ServiceException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $client;
    }
}
