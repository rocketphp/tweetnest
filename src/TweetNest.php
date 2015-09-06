<?php
/**
 * RocketPHP (http://rocketphp.io)
 *
 * @package   RocketPHP
 * @link      https://github.com/rocketphp/tweetnest
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace RocketPHP\TweetNest;

use Elasticsearch\Client as Elasticsearch;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use InvalidArgumentException;

/** 
 * TweetNest: Search JSON tweet files
 *
 * Use TweetNest when you want to search JSON tweet files by text and location.
 */
class TweetNest 
implements TweetNestInterface
{

    /** 
     * Files
     * @var obj
     */
    public $files;

    /** 
     * Config
     * @var array
     */
    public $config;

    /** 
     * Elasticsearch host
     * @access protected
     * @var    string
     */
    protected $_eshost;

    /** 
     * Elasticsearch index
     * @access protected
     * @var    string
     */
    protected $_esindex;

    /** 
     * Elasticsearch type
     * @access protected
     * @var    string
     */
    protected $_estype;

    /** 
     * Elasticsearch client
     * @access protected
     * @var    Elastic
     */
    protected $_es;

    /** 
     * Tweets directory
     * @access protected
     * @var    string
     */
    protected $_dir;

    /**
     * Constructor
     *
     * @param string $eshost Elasticsearch host
     * @param string $dir    Tweets directory
     */
    public function __construct(
        $eshost, 
        $dir, 
        $esindex = 'tweetnest',
        $estype = 'tweet')
    {

        if(!is_string($eshost) || $eshost === "")
            throw new InvalidArgumentException(
                "Expected string for Elasticsearch host.", 
                1
            );

        if(!is_string($dir) || $dir === "")
            throw new InvalidArgumentException(
                "Expected string for tweet files directory.", 
                1
            );

        $this->_eshost = $eshost;
        $this->_esindex = $esindex;
        $this->_estype = $estype;

        $this->_es = new Elasticsearch(
            ['hosts' => [$eshost]]
        ); 

        $this->_dir = $dir; 
    }
    
    /**
     * Load tweet files into Elasticsearch
     *
     * @return array
     */
    public function load()
    {    
        $result = $this->save();
        return $result;
    }

    /**
     * Clear tweets in elasticsearch
     *
     * @return bool
     */
    public function clear()
    {
        $params = [
            'index' => $this->_esindex,
            'type' => $this->_estype,
            'body' => [
                'query' => [
                    'match_all' => []
                ]
            ]
        ];
        $query = $this->_es->deleteByQuery($params); 
        // return $query['_indices']['tweets']['_shards']['successful'];
        $result = true;
        return $result;
    }

    /**
     * Delete a tweet
     *
     * @todo   Not implemented
     * @return bool
     */
    public function delete()
    {
        /*
        $params = ['index' => 'tweets'];
        $query = $this->_es->indices()->delete($params);
        */
        return false;
    }

    /**
     * Save tweets in elasticsearch
     *
     * @return int
     */
    public function save()
    {   
        $result = null; 
        $i = 0;
        foreach (
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $this->_dir, 
                    RecursiveDirectoryIterator::SKIP_DOTS
                ),
                RecursiveIteratorIterator::SELF_FIRST
            ) as $item
        ) {
            if (!$item->isDir()) { 
                $filename = $this->_dir . 
                            DIRECTORY_SEPARATOR . 
                            $iterator->getSubPathName(); 
                $_tweet = file_get_contents($filename); 
                $tweet = json_decode($_tweet, TRUE);  
                $params = ['index' => $this->_esindex,
                            'type' => $this->_estype,
                            'body' => $tweet];
                $result = $this->_es->index($params); 
                $i++;
            }
        }
        return $i;
    }

    /**
     * Return all tweets
     *
     * @param  int   $limit Limit
     * @return array
     */
    public function tweets($limit = 1000)
    { 
        $params = [
            'index' => $this->_esindex,
            'type' => $this->_estype,
            'size' => $limit,
            'body' => [
                'query' => [
                    'match_all' => []
                ]
            ]
        ];
        $query = $this->_es->search($params);
        if ($query['hits']['total'] >= 1)
            $result = $query['hits']['hits'];
        else
            $result = [];
        return $result;
    }

    /**
     * Search tweets
     *
     * @todo   Allow searching for empty location
     * @todo   Search operator for text and location is OR - change to AND
     * @param  array $q     Query fields and values
     * @param  array $limit Limit results
     * @return array
     */
    public function search(array $q, $limit = 1000)
    {  
        $params = ['index' => $this->_esindex,
                    'type' => $this->_estype,
                    'size' => $limit];

        $continue = false;
        if (isset($q['text']) && $q['text'] !== "") {
            $params['q']['text'] = $q['text'];
            $continue = true;
        }
        if (isset($q['location']) && $q['location'] !== "") {
            $params['body']['query']['match']['user.location'] = $q['location'];
            $continue = true;
        }
        if($continue === false)
            return [];

        $query = $this->_es->search($params);

        if($query['hits']['total'] >= 1)
            $result = $query['hits']['hits'];
        else
            $result = [];

        return $result;
    } 
}