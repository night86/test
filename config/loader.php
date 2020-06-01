<?php

// Instantiate loader
$loader = new \Phalcon\Loader();

// Register directories
$loader->registerDirs(
	array(
		$config->application->controllersDir,
		$config->application->modelsDir,
        $config->application->pluginsDir
	)
)->register();

// Register classes
$loader->registerClasses(
	array(
		"MongoLogger"         => __DIR__.'/../libs/MongoLogger.php',
	)
);

// Register namespaces
$loader->registerNamespaces(array(
	'Signa\Controllers'	=> __DIR__ . '/../controllers/',
	'Signa\Models' 		=> __DIR__ . '/../models/',
	'Signa\Helpers' 	=> __DIR__ . '/../helpers/',
	'Signa\Tasks'       => __DIR__ . '/../tasks/',
	'Signa\Tests'       => __DIR__ . '/../tests/',
	'Signa\Libs'        => __DIR__ . '/../libs/',
	'Phalcon' 			=> __DIR__ . '/../../vendor/phalcon/incubator/Library/Phalcon/'
))->register();

// Custom dumps
function _dump(){

	$args = func_get_args();

	echo '<pre style="background-color:#000;color: #00ff00;border:1px solid rgba(0, 0, 0, 0.15);border-radius:4px 4px 4px 4px;display:block;font-size:13px;line-height:20px;padding:9.5px;white-space:pre-wrap;word-break:break-all;word-wrap:break-word;">';

	foreach($args as $arg){

		if(is_array($arg) && $arg) {

            print_r($arg);
        }
		else {
            var_dump($arg);
        }
	}
	echo '</pre>';
}

function dd(){

	$args = func_get_args();

    if(count($args) === 1){
        die(var_dump($args[0]));
    }
	die(var_dump($args));
}

// Check email pattern
function checkEmail($strEmail){

    return preg_match('/^(\w+[!#\$%&\'\*\+\-\/=\?^_`\.\{\|\}~]*)+(?<!\.)@\w+([_\.-]*\w+)*\.[a-z]{2,6}$/i', $strEmail);
}

// Check application environment
function checkEnv(){

	$env = getenv('APPLICATION_ENV');
	return $env;
}

// Check server name
function serverName(){

    return $_SERVER['SERVER_NAME'];
}

// Return base url
function baseUrl(){

    $protocol = 'http';

    if(isset($_SERVER['HTTPS'])){

        $protocol .= 's';
    }
    return $protocol.'://'.$_SERVER['SERVER_NAME'];
}

// Create recursive directory
function mkdirR($path){

	if (!file_exists($path)) {

		mkdir($path, 0777, true);
	}
}