<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Service\Message;

/**
 * Windows Push Notification Service service message.
 */
class MpnsMessage
{
    const CLASS_IMMEDIATE_TILE = 1;
    const CLASS_IMMEDIATE_TOAST = 2;
    const CLASS_IMMEDIATE_RAW = 3;
    const CLASS_DELAY450_TILE = 11;
    const CLASS_DELAY450_TOAST = 12;
    const CLASS_DELAY450_RAW = 13;
    const CLASS_DELAY900_TILE = 21;
    const CLASS_DELAY900_TOAST = 22;
    const CLASS_DELAY900_RAW = 23;

    const TARGET_TILE = 'tile';
    const TARGET_TOAST = 'toast';
    const TARGET_RAW = 'raw';

    /**
     * Notification target.
     *
     * @var string
     */
    protected $target;

    /**
     * Notification class.
     *
     * @var int
     */
    protected $class;

    /**
     * Notification identification.
     *
     * @var string
     */
    protected $uuid;

    /**
     * Notification title.
     *
     * @var string
     */
    protected $title;

    /**
     * Notification body.
     *
     * @var string
     */
    protected $body;

    /**
     * Screen to navigate to.
     *
     * @var string
     */
    protected $navigateTo;

    /**
     * Notification payload.
     *
     * @var array
     */
    protected $payload = [];

    /**
     * Tile notification count.
     *
     * @var int
     */
    protected $count;

    /**
     * Tile notification background image.
     *
     * @var string
     */
    protected $backgroundImage;

    /**
     * Tile notification reversed background image.
     *
     * @var string
     */
    protected $backBackgroundImage;

    /**
     * Use notification sound.
     *
     * @var bool
     */
    protected $silent = false;

    /**
     * Message sound.
     *
     * @var string
     */
    protected $sound;

    /**
     * WpMessage constructor.
     *
     * @param string $target
     * @param int    $class
     */
    public function __construct($target, $class)
    {
        $this->setTarget($target);
        $this->setClass($class);
    }

    /**
     * Retrieve notification target.
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set notification target.
     *
     * @param string $target
     *
     * @throws \InvalidArgumentException
     */
    public function setTarget($target)
    {
        if (!in_array($target, [self::TARGET_TILE, self::TARGET_TOAST, self::TARGET_RAW])) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid value for notification target', $target));
        }

        $this->target = $target;

        return $this;
    }

    /**
     * Retrieve notification class.
     *
     * @return int
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set notification class.
     *
     * @param int $class
     *
     * @throws \InvalidArgumentException
     */
    public function setClass($class)
    {
        static $validClasses = [
            self::CLASS_IMMEDIATE_TILE,
            self::CLASS_IMMEDIATE_TOAST,
            self::CLASS_IMMEDIATE_RAW,
            self::CLASS_DELAY450_TILE,
            self::CLASS_DELAY450_TOAST,
            self::CLASS_DELAY450_RAW,
            self::CLASS_DELAY900_TILE,
            self::CLASS_DELAY900_TOAST,
            self::CLASS_DELAY900_RAW,
        ];

        if (!in_array($class, $validClasses)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid value for notification class', $class));
        }

        $this->class = $class;

        return $this;
    }

    /**
     * Retrieve notification identifier.
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set notification|null identifier.
     *
     * @param string $uuid
     *
     * @throws \InvalidArgumentException
     */
    public function setUuid($uuid = null)
    {
        if ($uuid !== null) {
            $uuid = trim($uuid);

            if (!preg_match('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', strtolower($uuid))) {
                throw new \InvalidArgumentException(sprintf('"%s" is not a valid UUID', $uuid));
            }
        }

        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Retrieve notification title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set notification title.
     *
     * @param string|null $title
     */
    public function setTitle($title = null)
    {
        $this->title = $title === null ? null : trim($title);

        return $this;
    }

    /**
     * Retrieve notification body.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set notification body.
     *
     * @param string|null $body
     */
    public function setBody($body = null)
    {
        $this->body = $body === null ? null : trim($body);

        return $this;
    }

    /**
     * Retrieve string to navigate to.
     *
     * @return string
     */
    public function getNavigateTo()
    {
        return ltrim($this->navigateTo, '/');
    }

    /**
     * Set screen to navigate to.
     *
     * @param string|null $navigateTo
     */
    public function setNavigateTo($navigateTo = null)
    {
        $this->navigateTo = $navigateTo === null ? null : ('/' . trim($navigateTo, ' /'));

        return $this;
    }

    /**
     * Get notification payload data.
     *
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Set notification payload data.
     *
     * @param array $payload
     */
    public function setPayload(array $payload)
    {
        $this->clearPayload();

        foreach ($payload as $k => $v) {
            $this->addPayload($k, $v);
        }

        return $this;
    }

    /**
     * Add notification payload data.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function addPayload($key, $value)
    {
        $key = trim($key);

        if ($key === '') {
            throw new \InvalidArgumentException('$key must be a non-empty string');
        }

        if (array_key_exists($key, $this->payload)) {
            throw new \RuntimeException('$key conflicts with current set notification payload data');
        }

        $this->payload[$key] = $value;

        return $this;
    }

    /**
     * Clear notification payload data.
     */
    public function clearPayload()
    {
        $this->payload = [];

        return $this;
    }

    /**
     * Retrieve tile notification count.
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set tile notification count.
     *
     * @param int $count
     */
    public function setCount($count)
    {
        $this->count = (int) $count;

        return $this;
    }

    /**
     * Retrieve tile notification background image.
     *
     * @return string
     */
    public function getBackgroundImage()
    {
        return $this->backgroundImage;
    }

    /**
     * Retrieve tile notification reverse background image.
     *
     * @param string $backgroundImage
     */
    public function setBackgroundImage($backgroundImage)
    {
        $this->backgroundImage = $backgroundImage;

        return $this;
    }

    /**
     * Retrieve tile notification background image.
     *
     * @return string
     */
    public function getBackBackgroundImage()
    {
        return $this->backBackgroundImage;
    }

    /**
     * Retrieve tile notification reverse background image.
     *
     * @param string $backBackgroundImage
     */
    public function setBackBackgroundImage($backBackgroundImage)
    {
        $this->backBackgroundImage = $backBackgroundImage;

        return $this;
    }

    /**
     * Retrieve message sound state.
     *
     * @return bool
     */
    public function isSilent()
    {
        return $this->silent;
    }

    /**
     * Set the use of message sound.
     *
     * @param bool $silent
     */
    public function setSilent($silent)
    {
        $this->silent = $silent === true;

        return $this;
    }

    /**
     * Retrieve message sound.
     *
     * @return string
     */
    public function getSound()
    {
        return $this->sound;
    }

    /**
     * Set message sound.
     *
     * @param string|null $sound
     */
    public function setSound($sound = null)
    {
        $this->sound = $sound;

        return $this;
    }

    /**
     * Retrieve composed message content.
     *
     * @see https://msdn.microsoft.com/library/windows/apps/jj662938%28v=vs.105%29.aspx
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->target === self::TARGET_TOAST) {
            return $this->composeToastMessage();
        } elseif ($this->target === self::TARGET_TILE) {
            return $this->composeTileMessage();
        }

        return $this->composeRawMessage();
    }

    /**
     * Compose Toast message.
     *
     * @return string
     */
    protected function composeToastMessage()
    {
        $xml = new \SimpleXMLElement(
            '<?xml version="1.0" encoding="utf-8"?><wp:Notification xmlns:wp="WPNotification" />'
        );

        $message = $xml->addChild('wp:Toast');
        $message->addChild('wp:Text1', htmlspecialchars($this->title, ENT_XML1 | ENT_QUOTES));
        $message->addChild('wp:Text2', htmlspecialchars($this->body, ENT_XML1 | ENT_QUOTES));

        if ($this->navigateTo !== null || count($this->payload) > 0) {
            $message->addChild(
                'wp:Param',
                $this->navigateTo . (count($this->payload) ? '?' . http_build_query($this->payload) : '')
            );
        }

        if ($this->isSilent()) {
            $silent = $message->addChild('wp:Sound');
            $silent->addAttribute('Silent', 'true');
        } elseif ($this->sound !== null) {
            $message->addChild('wp:Sound', htmlspecialchars($this->sound, ENT_XML1 | ENT_QUOTES));
        }

        return $xml->asXML();
    }

    /**
     * Compose Tile message.
     *
     * @return string
     */
    protected function composeTileMessage()
    {
        $xml = new \SimpleXMLElement(
            '<?xml version="1.0" encoding="utf-8"?><wp:Notification xmlns:wp="WPNotification" />'
        );

        $message = $xml->addChild('wp:Tile');

        $message->addChild('wp:Title', htmlspecialchars($this->title, ENT_XML1 | ENT_QUOTES));
        if ($this->count !== null) {
            $message->addChild('wp:Count', $this->count);
        }
        if ($this->backgroundImage !== null) {
            $message->addChild('wp:BackgroundImage', $this->backgroundImage);
        }

        if ($this->navigateTo !== null || count($this->payload) > 0) {
            $message->addChild(
                'wp:Param',
                $this->navigateTo . (count($this->payload) ? '?' . http_build_query($this->payload) : '')
            );
        }

        $message->addChild('wp:BackTitle', htmlspecialchars($this->title, ENT_XML1 | ENT_QUOTES));
        $message->addChild('wp:BackContent', htmlspecialchars($this->body, ENT_XML1 | ENT_QUOTES));
        if ($this->backBackgroundImage !== null) {
            $message->addChild('wp:BackBackgroundImage', $this->backBackgroundImage);
        }

        if ($this->isSilent()) {
            $silent = $message->addChild('wp:Sound');
            $silent->addAttribute('Silent', 'true');
        } elseif ($this->sound !== null) {
            $message->addChild('wp:Sound', htmlspecialchars($this->sound, ENT_XML1 | ENT_QUOTES));
        }

        return $xml->asXML();
    }

    /**
     * Compose Raw messages.
     *
     * @return string
     */
    protected function composeRawMessage()
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><notification/>');

        $message = $xml->addChild('message');

        $message->addChild('title', htmlspecialchars($this->title, ENT_XML1 | ENT_QUOTES));
        $message->addChild('body', htmlspecialchars($this->body, ENT_XML1 | ENT_QUOTES));
        if ($this->navigateTo !== null) {
            $message->addChild('navigateTo', $this->navigateTo);
        }

        if (count($this->payload) > 0) {
            $parameters = $message->addChild('parameters');

            foreach ($this->payload as $parameter => $value) {
                $parameters->addChild($parameter, htmlspecialchars($value, ENT_XML1 | ENT_QUOTES));
            }
        }

        return $xml->asXML();
    }
}
