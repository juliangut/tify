[![PHP version](https://img.shields.io/badge/PHP-%3E%3D5.5-8892BF.svg?style=flat-square)](http://php.net)
[![Latest Version](https://img.shields.io/packagist/vpre/juliangut/tify.svg?style=flat-square)](https://packagist.org/packages/juliangut/tify)
[![License](https://img.shields.io/github/license/juliangut/tify.svg?style=flat-square)](https://github.com/juliangut/tify/blob/master/LICENSE)

[![Build status](https://img.shields.io/travis/juliangut/tify.svg?style=flat-square)](https://travis-ci.org/juliangut/tify)
[![Style](https://styleci.io/repos/47275107/shield)](https://styleci.io/repos/47275107)
[![Code Quality](https://img.shields.io/scrutinizer/g/juliangut/tify.svg?style=flat-square)](https://scrutinizer-ci.com/g/juliangut/tify)
[![Code Coverage](https://img.shields.io/coveralls/juliangut/tify.svg?style=flat-square)](https://coveralls.io/github/juliangut/tify)
[![Total Downloads](https://img.shields.io/packagist/dt/juliangut/tify.svg?style=flat-square)](https://packagist.org/packages/juliangut/tify)

# Tify

Unified push notification services abstraction layer to connect with Google GCM and Apple APNS services.

## Installation

Install using Composer:

```
composer require juliangut/tify
```

Then require_once the autoload file:

```php
require_once './vendor/autoload.php';
```

## Concepts

### Receiver

Each of the individual devices that will receive push notification. Identified by a device `token` provided by push notification service (APNS or GCM).

```php
new \Jgut\Tify\Receiver\ApnsReceiver('device_token');
new \Jgut\Tify\Receiver\GcmReceiver('device_token');
```

`device_token` follow different formats depending on service type. Review APNS and GCM documentation for proper formatting.

### Message

Messages compose the final information arriving to receivers. GCM and APNS messages hold different information according to each service specification.

In order for the message payload to be created one of the following message parameters must be present:

* For APNS
  * `title`
  * `title_loc_key`
  * `body`
  * `loc_key`
* For GCM
  * `title`
  * `title_loc_key`
  * `body`
  * `body_loc_key`

Messages can hold any number of custom payload data that will compose additional data sent to the destination receivers.

This key/value payload data must comply with some limitations to be fully compatible with different services at once, for this a prefix (`data_` by default) is automatically added to the key. This prefix can be changed or removed if needed, but be aware that payload data should not be a reserved word (`aps`, `from` or any word starting with `google` or `gcm`) or any GCM notification parameters.

*Find APNS message parameters [here](https://developer.apple.com/library/ios/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/Chapters/ApplePushService.html) in table 3-2.*

*Find GCM message parameters [here](https://developers.google.com/cloud-messaging/http-server-ref#table2) in table 2.*

### Notification

Is a container to keep a message and its associated destination receivers.

Notifications are the central unit of work, several notifications can be set into a Tify Service sharing the same adapters but sending different messages to different receivers.

Notifications hold some extra parameters used by the push notification services to control behaviour and/or be used in notification creation.

*Find APNS notification parameters [here](https://developer.apple.com/library/ios/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/Chapters/ApplePushService.html) in table 3-1.*

*Find GCM notification parameters [here](https://developers.google.com/cloud-messaging/http-server-ref#table1) in table 1.*

### Adapter

Adapters will be given notifications to actually send the messages to associated receivers using the corresponding notification service. Receivers will be automatically filtered for the correct service by their type.

For APNS adapter `certificate` parameter is mandatory, denoting the path to the service certificate (.pem file). In GCM `api_key` is the mandatory parameter denoting Google API key.

```php
$apnsService = new \Jgut\Tify\Service\ApnsService(['certificate' => 'path_to_certificate.pem']);
$gcmService = new \Jgut\Tify\Service\GcmService(['api_key' => 'google_api_key']);
```

### Result

Responses from APNS and GCM push services are very different from one another, Result is a response abstraction in order to provide a common interface to access this non-equal returning data from APNS and GCM services.

This objects are composed of device token, date, status and status message (in case of error).

### Service

For simplicity instead of handing notifications to adapters one by one 'Tify Service' can be used to send Notifications to its corresponding receivers using correct provided Adapters, automatically merging notification Results into a single returned array.

## Usage

Basic usage creating a one message to be sent through different adapters.

```php
use Jgut\Tify\Adapter\Apns\ApnsAdapter;
use Jgut\Tify\Adapter\Gcm\GcmAdapter;
use Jgut\Tify\Message;
use Jgut\Tify\Notification;
use Jgut\Tify\Receiver\ApnsReceiver;
use Jgut\Tify\Receiver\GcmReceiver;
use Jgut\Tify\Service;

$adapters = [
    new GcmAdapter(['api_key' => '00000']),
    new ApnsAdapter(['certificate' => 'path_to_certificate'])
];

$message = new Message([
    'title' => 'title',
    'body' => 'body',
]);

$receivers = [
    new GcmReceiver('aaaaaaaaaaa'),
    new GcmReceiver('bbbbbbbbbbb'),
    new ApnsReceiver('ccccccccccc'),
    new ApnsReceiver('ddddddddddd'),
];

$service = new Service($adapters, new Notification($message, $receivers));

$results = $service->send();
```

Sharing the same adapters to send different messages

```php
use Jgut\Tify\Adapter\Gcm\GcmAdapter;
use Jgut\Tify\Message;
use Jgut\Tify\Notification;
use Jgut\Tify\Receiver\GcmReceiver;
use Jgut\Tify\Service;

$adapters = [
    new GcmAdapter(['api_key' => '00000']),
    new GcmAdapter(['api_key' => '11111'])
];

$service = new Service($adapters);

$service->addNotification(new Notification(
    new Message([
        'title' => 'title_one',
        'body' => 'body_one',
    ]),
    [
        new GcmReceiver('aaaaaaaaaaa'),
        new GcmReceiver('bbbbbbbbbbb'),
    ]
));

$service->addNotification(new Notification(
    new Message([
        'title' => 'title_two',
        'body' => 'body_two',
    ]),
    [
        new GcmReceiver('xxxxxxxxxxx'),
        new GcmReceiver('zzzzzzzzzzz'),
    ]
));

$results = $service->send();
```

## Contributing

Found a bug or have a feature request? [Please open a new issue](https://github.com/juliangut/tify/issues). Have a look at existing issues before.

See file [CONTRIBUTING.md](https://github.com/juliangut/tify/blob/master/CONTRIBUTING.md)
## License

See file [LICENSE](https://github.com/juliangut/tify/blob/master/LICENSE) included with the source code for a copy of the license terms.
