# tweetnest

`RocketPHP\TweetNest` uses Elasticsearch to search JSON tweet files by text and location.

**_To search tweets_** – start with an instance of TweetNest and search using arrays.

```php
use RocketPHP\TweetNest\TweetNest;

$nest = new TweetNest('127.0.0.1:9200', 'tweet_files');
$result = $nest->search(
    ['text' => 'Arnold',
    'location' => 'USA']
);
var_dump($result);
```

- File issues at https://github.com/rocketphp/tweetnest/issues
- Documentation is at http://rocketphp.io/tweetnest
