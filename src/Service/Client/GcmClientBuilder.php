<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Service\Client;

use Zend\Http\Client\Adapter\Socket;
use Zend\Http\Client as HttpClient;
use ZendService\Google\Gcm\Client;

/**
 * Class GcmClientBuilder
 */
class GcmClientBuilder
{
    /**
     * Get opened push service client.
     *
     * @param string $apiKey
     *
     * @return \ZendService\Google\Gcm\Client
     */
    public static function buildPush($apiKey)
    {
        $client = new Client;
        $client->setApiKey($apiKey);

        $httpClient = new HttpClient(
            null,
            [
                'service' => Socket::class,
                'strictredirects' => true,
                'sslverifypeer' => false,
            ]
        );

        $client->setHttpClient($httpClient);

        return $client;
    }
}
