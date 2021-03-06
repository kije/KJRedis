<?php
if (session_status() != PHP_SESSION_ACTIVE) {
	session_start(); 
}
require_once 'CustomException.php';

/**
 * Requires phpredis installed! https://github.com/nicolasff/phpredis
 */


class KJException extends CustomException {}

class KJRedis {
	/**
	 * Object-Variable of Redis
	 * @var Redis
	 */
	private $redis, $connectionData;

	/**
	 * @param String $host 		host or unix soket file to connect to redis
	 * @param int 	 $port 		port to connect to redis (optional)
	 * @param double $timeout 	timeout for the connection (optional)
	 * @throws KJException If $args > 3
	 */
	public function __construct($host /*, $port, $timeout*/) {
		$args = func_get_args();
		$connectionData = array();
		$this->redis = new Redis();

		try {
			try{
				if (func_num_args()<=3 && isset($host)) {
					call_user_func_array(array($this->redis, 'connect'), $args);
					if(count($args)>=1)$this->connectionData['host'] = $args[0];
					if(count($args)>=2)$this->connectionData['port'] = $args[1];
					if(count($args)==3)$this->connectionData['timeout'] = $args[2];
				} else {
					throw new KJException('Wrong number of arguments supplied: ' . func_num_args() . ' given, max. 3 expected!' );
				}
			} catch (RedisException $e) {
				throw $e;
				exit();
			}
		} catch (KJException $e) {
			$this->error($e);
			exit();
		}
	}
	
	public function __sleep() {
		return array('redis', 'connectionData');
	}

	public function __wakeup() {
		call_user_func_array(array($this->redis, 'connect'), $this->connectionData);
	}

	public function __call($name, $arguments) {
	 	return call_user_func_array(array($this->redis, $name), $arguments); 
	}

	public static function __set_state($vals) {
		$obj = new KJRedis($vals['connectionData'][0], $vals['connectionData'][1], $vals['connectionData'][3]);

		return $obj;
	}

	public function __set($name, $value) {
		$this->redis->$name = $value;
	}

	public function __get($name) {
		return $this->redis->$name;
	}

	public function __isset($name) {
		return isset($this->redis->$name);
	}

	public function __unset($name) {
		unset($this->redis->$name);
	}

	/**
	 * Shows an error if the display_errors in the php.ini is set
	 * @param  Exception or Subclasses $exception the exception
	 */
	protected function error($exception) {
		if (ini_get('display_errors') && strtolower(ini_get('display_errors')) != 'off' ){
			echo $exception->getMessage() .PHP_EOL. $exception->getTraceAsString();
		}
	}

	

	/**
	 * Set a value for key. Optional you can provide an *Associative array*, then this methode will set for every key => value pair the corresponding value for the key. On Error, it throws a KJException
	 * @param String $key 	the key
	 * @param String $value the value to set
	 * @return KJRedis 		Returs an KJRedis-Object so you can use following syntax: $kjredis->set('text', 1)->set('status', 'active')->set('test', 'works'); 
	 * @throws KJException If $args > 2
	 */
	public function set(/*$key, $value || $asoc_array*/) {
		try {
			if (func_num_args() == 1 && is_array(func_get_arg(0))) {
				foreach (func_get_arg(0) as $key => $value) {
					$this->redis->set($key, $value);
				}
				} else if (func_num_args() == 2) {
					$this->redis->set(func_get_arg(0), func_get_arg(1));
				}
				else {
					throw new KJException('Wrong number of arguments supplied: ' . func_num_args() . ' given, 1 or 2 expected!' );
				}
			} 
		catch (KJException $e) {
			$this->error($e);
			return false;
		}

		return $this; 
	}

	/**
	 * Set a value for key, if it not exists. Optional you can provide an *Associative array*, then this methode will set for every key => value pair the corresponding value for the key. On Error, it throws a KJException
	 * @param String $key 			the key
	 * @param String $value 		the value to set
	 * @return KJRedis || bool 		Returns on succes an KJRedis-Object so you can use following syntax: $kjredis->set('text', 1)->set('status', 'active')->set('test', 'works'); If the $key => $value pair allready exist in Redis, this methode will return false
	 * @throws KJException If $args > 2
	 */
	public function setnx(/*$key, $value || $asoc_array*/) {
		try {
			$ret = true;
			if (func_num_args() == 1 && is_array(func_get_arg(0))) {
				foreach (func_get_arg(0) as $key => $value) {
						$ret = $this->redis->setnx($key, $value);
				}
			} else if (func_num_args() == 2) {
					$ret = $this->redis->setnx(func_get_arg(0), func_get_arg(1));
			} else {
					throw new KJException('Wrong number of arguments supplied: ' . func_num_args() . ' given, 1 or 2 expected!' );
			}
		} catch (KJException $e) {
			$this->error($e);
			return false;
		}

		return ($ret ? $this : false);
	}

	/**
	 * Get all Keys and value as an array
	 * @return array 	Associative array with $key => $value
	 */
	public function getAllKeysWithValue() {
		$return_var = array();
		$keys = $this->redis->keys('*');
		foreach ($keys as $key ) {
			$return_var[$key] = $this->redis->get($key);
		}

		return $return_var;
	}
}
