<pre>
<?php 
ini_set('display_errors', true);
// Test

require_once 'KJRedis.php';


// Test 1: Connect to db

$redis = new KJRedis('127.0.0.1');


/// Test 2: Set Value

$redis->setnx('status', 'test is running')->setnx('test1' , 'successfully done');


// Test 3: Set multiple Values 

$redis->setnx(array('test2' => 'also successfully done', 'test3' => 'working'))->setnx('test3', 'successfully done');


// Test 4: get value

echo $redis->get('status') . PHP_EOL;

// Test 5: get all keys and values

var_dump($redis->getAllKeysWithValue());
// Test 6: delete value

$redis->delete('status');
var_dump($redis->getAllKeysWithValue());

// Test 7: generate error
$redis->setnx('not', 'a', 'good', 'idea');

// Delete all keys

$redis->flushAll();

?>
</pre>
