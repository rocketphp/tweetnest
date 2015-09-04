<?php
/**
 * RocketPHP (http://rocketphp.io)
 *
 * @package   RocketPHP
 * @link      https://github.com/rocketphp/tweetnest
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace RocketPHPTest\TweetNest;

use RocketPHP\TweetNest\TweetNest;

/** 
 * TweetNest mock
 */ 
class TweetNestMock
extends TweetNest
{

    public function __construct($eshost, $dir)
    {
        parent::__construct($eshost, $dir);

        $this->_es = new ElasticsearchMock([
            'hosts' => [$eshost]
            ]);
    }
       
    public function getProtectedProperty($name)
    {
        return $this->{$name};
    }
}