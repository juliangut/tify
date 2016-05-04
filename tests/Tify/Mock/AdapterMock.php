<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Mock;


use Jgut\Tify\Adapter\AbstractAdapter;

class AdapterMock extends AbstractAdapter
{
    /**
     * {@inheritdoc}
     */
    protected function getDefinedParameters()
    {
        return ['param1'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRequiredParameters()
    {
        return ['param1'];
    }
}
