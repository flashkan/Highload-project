<?php
require_once('/vendor/autoload.php');

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$log = new Logger('name');
$log->pushHandler(new StreamHandler('log/my.log', Logger::DEBUG));

function someFunc(&$log)
{
  $log->info('Start function - ' . __FUNCTION__);
  $max = 20000;
  $stap = $max / 4;
  $startMemory = memory_get_usage();

  $log->info('Current stap : 0%');
  $log->debug("Memory usage : $startMemory");

  for ($i=1; $i <= $max; $i++) {
    if ($i % $stap === 0) {
      $log->info('Current stap : ' . $i / $max * 100 . '%');
      $log->debug("Memory usage at the beginning : $startMemory");
      $log->debug('Memory usage at the stap : +'
        . (memory_get_usage() - $startMemory));
    }
    $arr[] = $i;
  }
  $log->info('Finish function - ' . __FUNCTION__);
}

someFunc($log);

var_dump(true);
