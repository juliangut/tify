[![PHP version](https://img.shields.io/badge/PHP-%3E%3D5.5-8892BF.svg?style=flat-square)](http://php.net)
[![Latest Version](https://img.shields.io/packagist/vpre/juliangut/tify.svg?style=flat-square)](https://packagist.org/packages/juliangut/tify)
[![License](https://img.shields.io/github/license/juliangut/tify.svg?style=flat-square)](https://github.com/juliangut/tify/blob/master/LICENSE)

[![Build status](https://img.shields.io/travis/juliangut/tify.svg?style=flat-square)](https://travis-ci.org/juliangut/tify)
[![Style Check](https://styleci.io/repos/47275107/shield)](https://styleci.io/repos/47275107)
[![Code Quality](https://img.shields.io/scrutinizer/g/juliangut/tify.svg?style=flat-square)](https://scrutinizer-ci.com/g/juliangut/tify)
[![Code Coverage](https://img.shields.io/coveralls/juliangut/tify.svg?style=flat-square)](https://coveralls.io/github/juliangut/tify)

[![Total Downloads](https://img.shields.io/packagist/dt/juliangut/tify.svg?style=flat-square)](https://packagist.org/packages/juliangut/tify)
[![Monthly Downloads](https://img.shields.io/packagist/dm/juliangut/tify.svg?style=flat-square)](https://packagist.org/packages/juliangut/tify)

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

* For APNS: `title`, `title-loc-key`, `title-loc-args`, `body`, `loc-key`, `loc-args`, `action-loc-key`, `launch-image`
* For GCM: `title`, `title_loc_key`, `title_loc_args`, `body`, `body_loc_key`, `body_loc_args`, `icon`, `sound`, `tag`, `color`

Messages can hold any number of custom payload data that will compose additional data sent to the destination receivers.

This key/value payload data must comply with some limitations to be fully compatible with different services at once, for this a prefix (`data_` by default) is automatically added to the key. This prefix can be changed or removed if needed, but be aware that payload data should not be a reserved word (`apns`, `from` or any word starting with `google` or `gcm`) or any GCM notification parameters.

*Find APNS message parameters [here](https://developer.apple.com/library/ios/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/Chapters/ApplePushService.html) in table 3-2.*

*Find GCM message parameters [here](https://developers.google.com/cloud-messaging/http-server-ref#table2) in table 2.*

### Notification

It's a container to keep a message and its associated destination receivers.

Notifications are the central unit of work, several notifications can be set into a Tify Service sharing the same adapters but sending different messages to different receivers.

Notifications hold some extra parameters used by the notification services to control behaviour and/or be used in notification creation.

Be aware notification TTL (expire in APNS) is normalized in both services to "2 weeks" instead of the default 4 weeks for GCM and none at all for APNS. There are some convenience constants in Notification class for common TTL.

By clearing receivers list or changing message a notification can be reused as many times as needed.

*Find APNS notification parameters [here](https://developer.apple.com/library/ios/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/Chapters/ApplePushService.html) in table 3-1.*

*Find GCM notification parameters [here](https://developers.google.com/cloud-messaging/http-server-ref#table1) in table 1.*

### Adapter

Adapters will be given notifications to actually send the messages to associated receivers using the corresponding notification service. Receivers will be automatically filtered for the correct service by their type.

For APNS adapter `certificate` parameter is mandatory, denoting the path to the service certificate (.pem file). In GCM `api_key` is the mandatory parameter denoting Google API key.

```php
$apnsAdapter = new \Jgut\Tify\Adapter\ApnsAdapter(['certificate' => 'path_to_certificate.pem']);
$gcmAdapter = new \Jgut\Tify\Adapter\GcmAdapter(['api_key' => 'google_api_key']);
```

### Result

Responses from APNS and GCM push services are very different from one another, Result is a response abstraction in order to provide a common interface to access this non-equal returning data from APNS and GCM services.

This objects are composed of device token, date, status code (a status categorization) and status message (which corresponds to the original APNS or GCM response status).

#### Status Codes

* `STATUS_SUCCESS`, push was successful
* `STATUS_INVALID_DEVICE`, device token provided is invalid
* `STATUS_INVALID_MESSAGE`, message was not properly composed
* `STATUS_RATE_ERROR`, (only for GCM)
* `STATUS_AUTH_ERROR`, (only for GCM)
* `STATUS_SERVER_ERROR`
* `STATUS_UNKNOWN_ERROR`

Among all the result statuses, `STATUS_INVALID_DEVICE` is the most interesting because it is a signal that you should probably remove that token from your database.

### Service

For simplicity instead of handing notifications to adapters one by one 'Tify Service' can be used to send Notifications to its corresponding receivers using correct Adapter, automatically merging notification Results into a single returned array.

## Usage

### Push

Basic usage creating a one message to be sent through different adapters.

```php
use Jgut\Tify\Adapter\ApnsAdapter;
use Jgut\Tify\Adapter\GcmAdapter;
use Jgut\Tify\Message;
use Jgut\Tify\Notification;
use Jgut\Tify\Receiver\ApnsReceiver;
use Jgut\Tify\Receiver\GcmReceiver;
use Jgut\Tify\Service;

$adapters = [
    new GcmAdapter(['api_key' => '00000']),
    new ApnsAdapter(['certificate' => 'path_to_certificate']),
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

$results = $service->push();
```

Sharing the same adapters to send different messages

```php
use Jgut\Tify\Adapter\GcmAdapter;
use Jgut\Tify\Message;
use Jgut\Tify\Notification;
use Jgut\Tify\Receiver\GcmReceiver;
use Jgut\Tify\Service;

$adapters = [
    new GcmAdapter(['api_key' => '00000']),
    new GcmAdapter(['api_key' => '11111']),
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

$results = $service->push();
```

### Feedback

```php
use Jgut\Tify\Adapter\ApnsAdapter;
use Jgut\Tify\Service;

$adapters = [
    new ApnsAdapter(['certificate' => 'path_to_certificate_one']),
    new ApnsAdapter(['certificate' => 'path_to_certificate_two']),
];

$service = new Service($adapters);

$results = $service->feedback();
```

Feedback returns Result objects with token and time of expired device tokens.

## Contributing

Found a bug or have a feature request? [Please open a new issue](https://github.com/juliangut/tify/issues). Have a look at existing issues before.

See file [CONTRIBUTING.md](https://github.com/juliangut/tify/blob/master/CONTRIBUTING.md)
## License

See file [LICENSE](https://github.com/juliangut/tify/blob/master/LICENSE) included with the source code for a copy of the license terms.
