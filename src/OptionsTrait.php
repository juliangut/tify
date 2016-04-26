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

trait OptionsTrait
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $options = [];

    /**
     * Initialize options collection.
     */
    protected function initializeOptions()
    {
        if (!$this->options instanceof ArrayCollection) {
            $this->options = new ArrayCollection;
        }
    }

    /**
     * Get options.
     *
     * @return array
     */
    public function getOptions()
    {
        $this->initializeOptions();

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
        $this->initializeOptions();

        return $this->options->containsKey($option);
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
        $this->initializeOptions();

        return $this->options->containsKey($option) ? $this->options->get($option) : $default;
    }

    /**
     * Set options.
     *
     * @param array $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        if (!$this->options instanceof ArrayCollection) {
            $this->options->clear();
        } else {
            $this->initializeOptions();
        }

        foreach ($options as $option => $value) {
            $this->options->set($option, $value);
        }

        return $this;
    }

    /**
     * Set option.
     *
     * @param string $option
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption($option, $value)
    {
        $this->initializeParameters();

        $this->options->set($option, $value);

        return $this;
    }
}
