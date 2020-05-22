<?php
function someFunc($num)
{
  var_dump(memory_get_usage());
  echo "<br>";

  if ($num >= 1000000000) {
    return $num;
  }

  return someFunc(floor($num * 4 / 3));
}

someFunc(3);
