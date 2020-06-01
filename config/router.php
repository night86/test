<?php

$router = new \Phalcon\Mvc\Router(false);

/**
 * base routing init
 */
$router->add(
    "/:controller/",
    array(
		'namespace' => 'Signa\Controllers',
        "controller" => 1
    )
);
$router->add(
	"/:controller/:action/:params",
	array(
		'namespace' => 'Signa\Controllers',
		"controller" => 1,
		"action"     => 2,
		"params"     => 3
	)
);

/**
 * dentist routing init
 */
$router->add(
	"/dentist/:controller/:action/:params",
	array(
		'namespace' => 'Signa\Controllers\Dentist',
		"controller" => 1,
		"action"     => 2,
		"params"     => 3
	)
);
$router->add(
    "/dentist/:controller/",
    array(
        'namespace' => 'Signa\Controllers\Dentist',
        "controller" => 1
    )
);

/**
 * lab routing init
 */
$router->add(
	"/lab/:controller/:action/:params",
	array(
		'namespace' => 'Signa\Controllers\Lab',
		"controller" => 1,
		"action"     => 2,
		"params"     => 3
	)
);
$router->add(
    "/lab/:controller/",
    array(
        'namespace' => 'Signa\Controllers\Lab',
        "controller" => 1
    )
);

/**
 * signadens routing init
 */
$router->add(
	"/signadens/:controller/:action/:params",
	array(
		'namespace' => 'Signa\Controllers\Signadens',
		"controller" => 1,
		"action"     => 2,
		"params"     => 3
	)
);
$router->add(
    "/signadens/:controller/",
    array(
        'namespace' => 'Signa\Controllers\Signadens',
        "controller" => 1
    )
);

/**
 * supplier routing init
 */
$router->add(
	"/supplier/:controller/:action/:params",
	array(
		'namespace' => 'Signa\Controllers\Supplier',
		"controller" => 1,
		"action"     => 2,
		"params"     => 3
	)
);
$router->add(
    "/supplier/:controller/",
    array(
        'namespace' => 'Signa\Controllers\Supplier',
        "controller" => 1
    )
);

return $router;