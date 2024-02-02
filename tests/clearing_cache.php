<?php

require_once "../Cache.php";

$cache = new Cache(__DIR__ . "/cache", "test");

$cache->set("test", "value");
$cache->set("test_1", "value_1");
$cache->set("test_2", "value_2");

// $cache->clearCache("test_*");

echo $cache->get("test");
echo $cache->get("test_1");
echo $cache->get("test_2");

// $cache->resetLocalCacheStats();
print_r($cache->getCacheStats());
print_r($cache->getLocalCacheStats());
