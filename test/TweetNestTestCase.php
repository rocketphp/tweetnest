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
use FilesystemIterator;

/** 
 * Test case for TweetNest
 */ 
abstract class TweetNestTestCase
extends \PHPUnit_Framework_TestCase
{
    public $eshost;
    public $dir;
    public $fileCount;

    public function badConstructorESHostValues()
    {
        return [
            [''],
            [null],
            [-1],
            [1],
            [1.5],
            [true],
            [false],
            [array()],
            [new \stdClass()]
        ];
    }

    public function badConstructorDirValues()
    {
        return [
            [''],
            [null],
            [-1],
            [1],
            [1.5],
            [true],
            [false],
            [array()],
            [new \stdClass()]
        ];
    }

    public function setUp()
    {
        $this->eshost = '127.0.0.1:9200';
        $this->dir = __DIR__ . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'tweet_files';
        $fi = new FilesystemIterator($this->dir, FilesystemIterator::SKIP_DOTS);
        $this->fileCount = iterator_count($fi);
    }

    public function tearDown()
    { 
    }
}