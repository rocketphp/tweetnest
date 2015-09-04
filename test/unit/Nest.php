<?php
/**
 * RocketPHP (http://rocketphp.io)
 *
 * @package   RocketPHP
 * @link      https://github.com/rocketphp/tweetnest
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace RocketPHPTest\TweetNest;

/**
 * @group RocketPHP_TweetNest
 */ 
class TweetNest_UnitTest
extends TweetNestTestCase
{

    public function testConstructor()
    {
        $nest = new TweetNestMock($this->eshost, $this->dir); 
        $this->assertInstanceOf('RocketPHP\\TweetNest\\TweetNest', $nest);
    }

    public function testConstructorSetsElasticSearchHost()
    { 
        $nest = new TweetNestMock($this->eshost, $this->dir); 
        $this->assertSame($nest->getProtectedProperty('_eshost'), $this->eshost);
    }

    public function testConstructorSetsElasticSearchClient()
    { 
        $nest = new TweetNestMock($this->eshost, $this->dir); 
        $this->assertInstanceOf('Elasticsearch\Client', $nest->getProtectedProperty('_es'));
    }

    public function testConstructorSetsDir()
    { 
        $nest = new TweetNestMock($this->eshost, $this->dir); 
        $this->assertSame($nest->getProtectedProperty('_dir'), $this->dir);
    }
 
    public function testLoadIndexesJSONFiles()
    {
        $nest = new TweetNestMock($this->eshost, $this->dir);
        $result = $nest->load();
        $this->assertSame($this->fileCount, $result);
        return $nest;
    }

    public function testSaveIndexesJSONFiles()
    {
        $nest = new TweetNestMock($this->eshost, $this->dir);
        $result = $nest->save();
        $this->assertSame($this->fileCount, $result);
        return $nest;
    }

    /**
     * @depends testSaveIndexesJSONFiles
     */
    public function testTweetsReturnsAllTweets($nest)
    {
        $result = $nest->tweets();
        $this->assertSame($this->fileCount, count($result));
        return $nest;
    }

    /**
     * @depends testTweetsReturnsAllTweets
     */
    public function testSearchTextReturnsHits($nest)
    {
        $result = $nest->search(
            ['text' => 'Test driving my trail shoes']
            );
        $this->assertSame(1, count($result));
        $expected = '558050833343188993';
        $this->assertSame($expected, $result[0]['body']['id_str']);
        return $nest;
    }

    /**
     * @depends testSearchTextReturnsHits
     */
    public function testSearchLocationReturnsHits($nest)
    {
        $result = $nest->search(
            [
                'location' => 'Ciudad Obregón'
            ]
        );
        $this->assertSame(1, count($result));
        $expected = '558100443021475841';
        $this->assertSame($expected, $result[0]['body']['id_str']);
        return $nest;
    }

    /**
     * @depends testSearchLocationReturnsHits
     */
    public function testSearchTextAndLocationReturnsHits($nest)
    {
        $result = $nest->search(
            [
                'text' => 'Arnold',
                'location' => 'USA'
            ]
        );
        $this->assertSame(1, count($result));
        $expected = '558047660176060417';
        $this->assertSame($expected, $result[0]['body']['id_str']);
        return $nest;
    }

    /**
     * @depends testSearchTextAndLocationReturnsHits
     */
    public function testClearDeletesAll($nest)
    {
        $result = $nest->clear();
        $this->assertSame(true, $result);
        $result = $nest->tweets();
        $this->assertSame([], $result);
    }

    /**
     * @dataProvider             badConstructorESHostValues
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Expected string for Elasticsearch host.
     */
    public function testConstructorThrowsExceptionIfInvalidESHost($badValue)
    {  
        $nest = new TweetNestMock($badValue, $this->dir);
    }

    /**
     * @dataProvider             badConstructorDirValues
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Expected string for tweet files directory.
     */
    public function testConstructorThrowsExceptionIfInvalidDir($badValue)
    {  
        $nest = new TweetNestMock($this->eshost, $badValue);
    }

}