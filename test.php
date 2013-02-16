<pre>
<?php 
ini_set('display_errors', true);
// Test

require_once 'KJRedis.php';


// Test 1: Connect to db

$redis = new KJRedis('127.0.0.1');


// Test 2: Set Value

$redis->set('status', 'test is running')->set('test1' , 'successfully done');


// Test 3: Set multiple Values 

$redis->set(array('test2' => 'also successfully done', 'test3' => 'working'))->set('test3', 'successfully done');


// Test 4: get value

echo $redis->get('status') . PHP_EOL;

// Test 5: get all keys and values

var_dump($redis->getAllKeysWithValue());
// Test 6: delete value

$redis->delete('status');
var_dump($redis->getAllKeysWithValue());

// Test 7: generate error
$redis->set('not', 'a', 'good', 'idea');

// Delete all keys

$redis->flushAll();

?>
</pre>
