<?php
include('config.php');
require 'vendor/autoload.php';  
$redis = new Predis\Client();
$keys = $redis->keys("*");
print_r($keys);