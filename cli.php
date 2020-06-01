<?php

use Phalcon\Di\FactoryDefault\Cli as CliDI,
    Phalcon\Cli\Console as ConsoleApp;

define('VERSION', '1.0.0');

// Define path to application directory
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__)));

// Using the CLI factory default services container
$di = new CliDI();

// Load the configuration file (if any)
if (is_readable(APPLICATION_PATH . '/config/config.php')) {
    $config = include APPLICATION_PATH . '/config/config.php';
    $di->set('config', $config);
}


define('ROOT_PATH', dirname(__DIR__) . '/');
define('APP_PATH', ROOT_PATH . 'app/');
define('CONFIG_PATH', ROOT_PATH . 'app/config/');
//$config = new Phalcon\Config\Adapter\Ini( CONFIG_PATH . 'localconfig.ini');
//$config->merge(new Phalcon\Config\Adapter\Ini( CONFIG_PATH . 'localconfig_'.$config->server->env.'.ini'));
//$config->merge(include CONFIG_PATH . 'aplicationpaths.php');

# system/application specific configuration
$config = new Phalcon\Config\Adapter\Ini( CONFIG_PATH . 'systemconfig.ini');
# get server->env
$config->merge(new Phalcon\Config\Adapter\Ini( CONFIG_PATH . 'localconfig.ini'));
# get local config for environment
$config->merge(new Phalcon\Config\Adapter\Ini( CONFIG_PATH . 'localconfig_'.$config->server->env.'.ini') );
# get aplication paths
$config->merge(include CONFIG_PATH . 'aplicationpaths.php');
# get alc roles
$config->merge(include CONFIG_PATH . 'security.php');

require_once APP_PATH . '../vendor/autoload.php';

require __DIR__ . '/config/loader.php';

$di->set('db', function() use ($config) {
	return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
		"host" => $config->database->host,
		"username" => $config->database->username,
		"password" => $config->database->password,
		"dbname" => $config->database->name
	));
}, true);

$di->set('collectionManager', function(){
    return new Phalcon\Mvc\Collection\Manager();
}, true);

$di->set('mongo', function () use ($config) {
    $dev = checkEnv();
//    if($dev == 'development'){
    $mongo = new Phalcon\Db\Adapter\MongoDB\Client('mongodb://' . $config['mongodb']['host']);
    return $mongo->selectDatabase($config['mongodb']['dbname']);
//    } else {
//        $mongo = new MongoClient('mongodb://'.$config['mongodb']['host']);
//        return $mongo->selectDB($config['mongodb']['dbname']);
//    }
}, true);

$di->set('config', $config);

$di->setShared('mongoLogger', new \Signa\Libs\MongoLogger());

// Create a console application
$console = new ConsoleApp();
$console->setDI($di);

/**
 * Process the console arguments
 */
$arguments = array();
foreach ($argv as $k => $arg) {
    if ($k == 1) {
        $arguments['task'] = 'Signa\Tasks\\'.ucfirst($arg);
    } elseif ($k == 2) {
        $arguments['action'] = $arg;
    } elseif ($k >= 3) {
        $arguments['params'][] = $arg;
    }
}

// Define global constants for the current task and action
define('CURRENT_TASK',   (isset($argv[1]) ? $argv[1] : null));
define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));

try {
    // Handle incoming arguments
    $console->handle($arguments);
} catch (\Phalcon\Exception $e) {
    echo $e->getMessage();
    exit(255);
}