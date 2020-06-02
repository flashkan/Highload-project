<?php

class redisCacheProvider {
    private $address = '127.0.0.1';
    private $pord = '6379';
    private $connection = null;

    private function getConnection(){
        if($this->connection===null){
            $this->connection = new Redis();
            $this->connection->connect($this->address, $this->pord);
        }
        return $this->connection;
    }

    public function get($key){
        $result = false;
        if($c = $this->getConnection()){
            $result = ($c->get($key));
        }
        return $result;
    }

    public function set($key, $value, $time = 0){
        if($c = $this->getConnection()){
            $c->set($key, $value, 300);
        }
    }

    public function del($key){
        if($c = $this->getConnection()){
            $c->delete($key);
        }
    }

    public function clear(){
        if($c = $this->getConnection()){
            $c->flushDB();
        }
    }
}

$redis = new redisCacheProvider();
$a = '9999999999999999999999999';
// $redis->set('a', $a, 300);
$redis->del('a');

function getResult($redis, $a) {
  $result = '';
  $startTime = microtime(true);
  $startMem = memory_get_usage();

  if ($redisResult = $redis->get('a')) {
    $result = $redisResult;
  } else {
    $result = $a;
  }

  var_dump(microtime(true) - $startTime);
  var_dump(memory_get_usage() - $startMem);
  return $result;
}

var_dump(getResult($redis, $a));
