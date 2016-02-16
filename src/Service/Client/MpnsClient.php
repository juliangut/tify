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
use Jgut\Tify\Service\Message\MpnsMessage;

/**
 * Windows Push Notification Service client.
 */
class MpnsClient
{
    /**
     * Minimum default headers.
     *
     * @var array
     */
    protected static $defaultHttpHeaders = [
        'Content-Type' => 'text/xml',
        'Accept'       => 'application/*',
    ];

    /**
     * Push notification to remote service.
     *
     * @param string                                 $uri
     * @param \Jgut\Tify\Service\Message\MpnsMessage $message
     *
     * @return array
     */
    public function send($uri, MpnsMessage $message)
    {
        $messageBody = (string) $message;

        $headers = [
            'Content-Length' => strlen($messageBody),
            'X-NotificationClass' => $message->getClass(),
        ];
        if ($message->getTarget() !== MpnsMessage::TARGET_RAW) {
            $headers['X-WindowsPhone-Target'] = $message->getTarget() === MpnsMessage::TARGET_TILE ? 'token' : 'toast';
        }
        if ($message->getUuid() !== null) {
            $headers['X-MessageID'] = $message->getUuid();
        }

        $transport = $this->getTransport($uri, array_merge(self::$defaultHttpHeaders, $headers), $messageBody);
        $transferResponse = curl_exec($transport);

        if (curl_errno($transport) !== CURLE_OK) {
            curl_close($transport);

            throw new ServiceException(curl_errno($transport), curl_error($transport));
        }

        $transferInfo = curl_getinfo($transport);

        curl_close($transport);

        $responseHeaders = '';
        $responseContent = $transferResponse;
        if (isset($transferInfo['header_size']) && $transferInfo['header_size']) {
            $headersSize = $transferInfo['header_size'];
            $responseHeaders = rtrim(substr($transferResponse, 0, $headersSize));
            $responseContent = (strlen($transferResponse) === $headersSize)
                ? ''
                : substr($transferResponse, $headersSize);
        }

        $responseHeaders = preg_split('/(\\r?\\n){2}/', $responseHeaders);

        return $this->getTransferHeaders(
            preg_split('/\\r?\\n/', array_pop($responseHeaders)),
            $responseContent,
            $transferInfo
        );
    }

    /**
     * Retrieve transport handler.
     *
     * @param string $uri
     * @param array  $headers
     * @param string $message
     *
     * @return resource
     */
    protected function getTransport($uri, array $headers, $message)
    {
        $transport = curl_init();

        curl_setopt($transport, CURLOPT_VERBOSE, false);
        curl_setopt($transport, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($transport, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($transport, CURLOPT_TIMEOUT, 60);
        curl_setopt($transport, CURLOPT_CRLF, false);
        curl_setopt($transport, CURLOPT_SSLVERSION, 3);
        curl_setopt($transport, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($transport, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($transport, CURLOPT_AUTOREFERER, true);
        curl_setopt($transport, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($transport, CURLOPT_MAXREDIRS, 10);
        curl_setopt($transport, CURLOPT_UNRESTRICTED_AUTH, false);
        curl_setopt($transport, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($transport, CURLOPT_HEADER, true);
        curl_setopt($transport, CURLOPT_FORBID_REUSE, true);
        curl_setopt($transport, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($transport, CURLOPT_POST, true);

        curl_setopt($transport, CURLOPT_URL, $uri);
        $headerList = [];
        foreach ($headers as $header => $value) {
            $headerList[] = sprintf('%s: %s', $header, is_array($value) ? implode(', ', $value) : (string) $value);
        }
        curl_setopt($transport, CURLOPT_HTTPHEADER, $headerList);
        curl_setopt($transport, CURLOPT_POSTFIELDS, $message);

        return $transport;
    }

    /**
     * Extract headers from transfer results.
     *
     * @param array  $transferHeaders
     * @param string $transferContent
     * @param array  $transferInfo
     *
     * @return array
     */
    protected function getTransferHeaders(array $transferHeaders, $transferContent, array $transferInfo)
    {
        $responseHeaders = [
            'Status'         => $transferInfo['http_code'],
            'Content-Type'   => $transferInfo['content_type'],
            'Content-Length' => strlen($transferContent),
        ];

        foreach ($transferHeaders as $header) {
            if (preg_match('/^HTTP\/(1\.\d) +([1-5][0-9]{2}) +.+$/', $header, $matches)) {
                $responseHeaders['Status'] = $matches[2];
            } elseif (strpos($header, ':') !== false) {
                list($name, $value) = explode(':', $header, 2);
                $responseHeaders[$name] = trim($value);
            }
        }

        return $responseHeaders;

    }
}
