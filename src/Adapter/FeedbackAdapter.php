<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Adapter;

/**
 * Interface FeedbackAdapter
 */
interface FeedbackAdapter
{
    /**
     * Request feedback information.
     *
     * @return \Jgut\Tify\Result[]
     */
    public function feedback();
}
