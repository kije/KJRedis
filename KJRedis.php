<?php


/**
 * Requires installed phpredis (phpmodule)! https://github.com/nicolasff/phpredis
 */

class KJException extends Exception {}

class KJRedis {
	/**
	 * Object-Variable of Redis
	 * @var Redis
	 */
	private $redis;

	/**
	 * 
	 * @param String $host 		host or soket to connect to redis
	 * @param int 	 $port 		port to connect to redis (optional)
	 * @param double $timeout 	timeout for the connection (optional)
	 */
	public function __construct($host /*, $port, $timeout*/) {
		$args = func_get_args();
		$this->redis = new Redis();

		try {
			switch (func_num_args()) {
				case 1:
					$this->redis->connect($args[0]);
					break;

				case 2:
					$this->redis->connect($args[0], $args[1]);
					break;

				case 3: 
					$this->redis->connect($args[0], $args[1], $args[2]);
					break;

				default:
					throw new KJException('Wrong number of $arguments: ' . func_num_args() . ' given, max. 3 expected!\n<br />{$e}' );
					break;
			}
		} catch (KJException $e) {
			echo $e->getMessage();
			exit(); // exit script to avoid more errors

		} catch (RedisException $e) {
			echo 'RedisException caught: ' . $e->getMessage();
			exit();
		}
	}
}
