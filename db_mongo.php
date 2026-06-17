<?php
require_once __DIR__ . '/vendor/autoload.php';

try {
    $client = new MongoDB\Client("mongodb://127.0.0.1:27017");
    
    $collection = $client->selectDatabase('dbforlab')->selectCollection('goods');
} catch (Exception $e) {
    die("Помилка підключення до MongoDB: " . $e->getMessage());
}
?>