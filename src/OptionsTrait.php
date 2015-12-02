<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat;

trait OptionsTrait
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * Get options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Has option.
     *
     * @param string $option Option
     *
     * @return bool
     */
    public function hasOption($option)
    {
        return array_key_exists($option, $this->options);
    }

    /**
     * Get option.
     *
     * @param string $option  Option
     * @param mixed  $default Default
     *
     * @return mixed
     */
    public function getOption($option, $default = null)
    {
        return $this->hasOption($option) ? $this->options[$option] : $default;
    }

    /**
     * Set options.
     *
     * @param array $options Options
     *
     * @return \Jgut\Pushat\Model\BaseOptionedModel
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Set option.
     *
     * @param string $option Key
     * @param mixed  $value  Value
     *
     * @return mixed
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;

        return $value;
    }
}
