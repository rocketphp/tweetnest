<?php
/**
 * RocketPHP (http://rocketphp.io)
 *
 * @package   RocketPHP
 * @link      https://github.com/rocketphp/tweetnest
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace RocketPHPTest\TweetNest;

use Elasticsearch\Client as Elasticsearch;

/** 
 * Elasticsearch client mock
 */ 
class ElasticsearchMock
extends Elasticsearch
{

    /** 
     * Config
     * @access protected
     * @var    array
     */
    protected $_config;

    /** 
     * Data
     * @access protected
     * @var    array
     */
    protected $_data = [];

    public function __construct($config)
    {
        $this->_config = $config;
    }

    public function index($data)
    {
        array_push($this->_data, $data);
        return $data;
    }

    public function search(array $params)
    {
        $q = isset($params['q']) ? $params['q'] : [];
        $text = isset($q['text']) ? $q['text'] : null;

        $body = isset($params['body']) ? $params['body'] : [];
        $bodyQuery = isset($params['body']['query']) ? $params['body']['query'] : [];
        $bodyQueryMatch = isset($params['body']['query']['match']) ? $params['body']['query']['match'] : null;
        $bodyQueryMatchAll = isset($params['body']['query']['match_all']) ? $params['body']['query']['match_all'] : null;

        $hits = [];

        // match all
        if($bodyQueryMatchAll === array())
            $hits = $this->_data;

        // text search
        if ($text && !$bodyQueryMatch) {
            foreach ($this->_data as $hit) {
                if(strpos($hit['body']['text'], $text) !== false)
                    array_push($hits, $hit);
            }
        }

        // match location
        if (!$text && $bodyQueryMatch && array_key_exists('user.location', $bodyQueryMatch)) { 
            foreach ($this->_data as $hit) {
                if($hit['body']['user']['location'] === $bodyQueryMatch['user.location'])
                    array_push($hits, $hit);
            }
        }

        // text search + match location
        if ($text && $bodyQueryMatch && array_key_exists('user.location', $bodyQueryMatch)) { 
            foreach ($this->_data as $hit) {
                if(strpos($hit['body']['text'], $text) !== false
                    && $hit['body']['user']['location'] === $bodyQueryMatch['user.location'])
                    array_push($hits, $hit);
            }
        }

        $result = [
            'hits' => [
                'total' => count($hits),
                'hits' => $hits
            ]
        ];
        return $result;
    }

    public function deleteByQuery($params)
    {
        $successful = count($this->_data);
        $this->_data = []; 
        $result = [
            '_indices' => [
                'total' => [
                    'tweets' => [
                        '_shards' => [
                            'successful' => $successful
                        ]
                    ]
                ]
            ]
        ];
        return $result;
    }
}