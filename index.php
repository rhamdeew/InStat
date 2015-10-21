<?php

require 'vendor/autoload.php';
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

error_reporting(E_ALL);

ORM::configure('mysql:host=localhost;dbname='.getenv('DB_NAME'));
ORM::configure('username', getenv('DB_USER'));
ORM::configure('password', getenv('DB_PASSWORD'));

$hashTag = getenv('HASHTAG');

define('MAIN',true);
$production = getenv('PRODUCTION');
if($production=='true') {
    define('PRODUCTION',true);
}

require getenv('ROUTER');
?>
