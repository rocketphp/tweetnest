<?php
/**
 * RocketPHP (http://rocketphp.io)
 *
 * @package   RocketPHP
 * @link      https://github.com/rocketphp/tweetnest
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace RocketPHP\TweetNest;

/** 
 * Interface for TweetNest objects 
 */
interface TweetNestInterface
{
    public function load();
    public function clear();
    public function save();
    public function tweets();
    public function search(array $q, $limit = 1000);
}
