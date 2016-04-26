<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Service;

/**
 * Interface FeedbackInterface
 */
interface FeedbackInterface
{
    /**
     * Request feedback information.
     *
     * @return array
     */
    public function feedback();
}
