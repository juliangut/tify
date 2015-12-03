<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify;

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
     * @param string $option
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
     * @param string $option
     * @param mixed  $default
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
     * @param array $options
     *
     * @return \Jgut\Tify\Model\BaseOptionedModel
     */
    public function setOptions($options)
    {
        $this->options = [];

        foreach ($options as $option => $value) {
            $this->setOption($option, $value);
        }

        return $this;
    }

    /**
     * Set option.
     *
     * @param string $option
     * @param mixed  $value
     *
     * @return mixed
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;

        return $value;
    }
}
