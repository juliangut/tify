<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Parameter handling.
 */
trait PayloadTrait
{
    /**
     * Data payload.
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $payload;

    /**
     * Initialize payload collection.
     */
    protected function initializePayload()
    {
        if ($this->payload === null) {
            $this->payload = new ArrayCollection;
        }
    }

    /**
     * Get payload.
     *
     * @return array
     */
    public function getPayload()
    {
        $this->initializePayload();

        return $this->payload->toArray();
    }

    /**
     * Set parameters.
     *
     * @param array $payload
     *
     * @return $this
     */
    public function setPayload(array $payload)
    {
        if ($this->payload instanceof ArrayCollection) {
            $this->payload->clear();
        } else {
            $this->initializePayload();
        }

        foreach ($payload as $data => $value) {
            $this->payload->set(trim($data), $value);
        }

        return $this;
    }

    /**
     * Has payload data.
     *
     * @param string $data
     *
     * @return bool
     */
    public function hasData($data)
    {
        $this->initializePayload();

        return $this->payload->containsKey($data);
    }

    /**
     * Get payload data.
     *
     * @param string $data
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getData($data, $default = null)
    {
        $this->initializePayload();

        return $this->payload->containsKey($data) ? $this->payload->get($data) : $default;
    }

    /**
     * Set parameter.
     *
     * @param string $data
     * @param mixed  $value
     *
     * @return $this
     */
    public function setData($data, $value)
    {
        $this->initializePayload();

        $this->payload->set(trim($data), $value);

        return $this;
    }
}
