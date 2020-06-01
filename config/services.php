<?php

use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Http\Response\Cookies;
use Signa\Helpers\Logs\LogsHelper;
use Signa\Libs\MongoLogger;
use Signa\Libs\Security;
use Signa\Libs\Access;
use Signa\Libs\PartOfDay;
use Signa\Libs\Mail;
use Signa\Libs\Notifications;

$di = new \Phalcon\DI\FactoryDefault();

$di->set('config', $config);

$di->set('url', function() use ($config) {
	$url = new \Phalcon\Mvc\Url();
	$url->setBaseUri($config->application->baseUri);
	return $url;
});

$di->set('profiler', function () use ($profiler){
	return $profiler;
});

$di->set('toolbar', function () use ($profiler){
	$toolbar = new \Fabfuel\Prophiler\Toolbar($profiler);
	$toolbar->addDataCollector(new \Fabfuel\Prophiler\DataCollector\Request());
	return $toolbar;
});

$di->set('session', function() {
    $session = new \Phalcon\Session\Adapter\Files();
    $session->start();
//    session_abort();
//    session_start(['read_and_close' => true]);
    return $session;
});

$di->set('cookies', function () {
    $cookies = new Cookies();
    $cookies->useEncryption(false);
    return $cookies;
});

$di->set('mail', function () {
    $mail = new Mail();
    return $mail;
});

$di->set('view', function() use ($config) {

	$view = new View();

	$view->setViewsDir($config->application->viewsDir);
    $view->setLayoutsDir($config->application->layoutsDir);

    /**
     * translation adapter init
     */
	$locale = Locale::acceptFromHttp($_SERVER["HTTP_ACCEPT_LANGUAGE"]);

	$view->registerEngines(array(
		'.volt' => function($view, $di) use ($config, $locale) {

			$volt = new VoltEngine($view, $di);

			$volt->setOptions(array(
				'compiledPath' => $config->application->cacheDir,
				'compiledSeparator' => '_',
				'compileAlways' => $config->volt->compileAlways
			));

            $volt->getCompiler()->addFunction(
                'fixUrlSpaces',
                function($var)
                {
                    $fixedVar = preg_replace('/\s+/', '%20', $var);
                    return $fixedVar;
                }
            );

			$volt->getCompiler()->addFunction(
                'unserialize',
                function($viewKey)
                {
					return 'unserialize('.$viewKey.')';
                }
            );

			$volt->getCompiler()->addFunction(
                'array_merges',
                function($array1)
                {
					return 'array_merge('.$array1.')';
                }
            );
            
            $volt->getCompiler()->addFunction(
                'in_array',
                function($needle)
                {
                    return 'in_array('.$needle.')';
                }
            );

            $volt->getCompiler()->addFunction(
                'print_r',
                function($needle)
                {
                    return 'print_r('.$needle.')';
                }
            );

            $volt->getCompiler()->addFunction(
                'str_replace',
                function($needle)
                {
                    return 'str_replace('.$needle.')';
                }
            );
            $volt->getCompiler()->addFunction(
                'var_dump',
                function($needle)
                {
                    return 'var_dump('.$needle.')';
                }
            );

            $volt->getCompiler()->addFunction(
                'sprintf',
                function($needle)
                {
                    return 'sprintf('.$needle.')';
                }
            );

			$volt->getCompiler()->addFilter('isArray', function($resolvedArgs) {
				return 'is_array('.$resolvedArgs.')';
			});

            $volt->getCompiler()->addFunction(
                'timetostr',
                function($arg)
                {
                    return 'date("d-m-Y", ' .$arg. ')';
                }
            );

            $volt->getCompiler()->addFunction(
                'strtotime',
                function($arg)
                {
                    return 'strtotime('.$arg. ')';
                }
            );

            $volt->getCompiler()->addFunction(
                'htmlspecialchars',
                function($arg)
                {
                    return 'htmlspecialchars('.$arg. ')';
                }
            );

            $volt->getCompiler()->addFunction(
                'strip_tags',
                function($arg)
                {
                    return 'strip_tags('.$arg. ')';
                }
            );

            $volt->getCompiler()->addFunction(
                'strlen',
                function($arg)
                {
                    return 'strlen('.$arg. ')';
                }
            );

            $volt->getCompiler()->addFunction(
                'substr',
                function($arg)
                {
                    return 'substr('.$arg. ')';
                }
            );

			$volt->getCompiler()->addFunction(
				'timetostrdt',
				function($arg)
				{
					return 'date_format(new DateTime('.$arg.'), "d-m-Y")';
				}
			);

            $volt->getCompiler()->addFunction(
					'datetimetotime',
					function($arg)
					{
						return 'date_format(new DateTime('.$arg.'), "H:i:s")';
					}
			);

            $volt->getCompiler()->addFunction(
                'exit',
                function($arg)
                {
                    return 'exit('.$arg.')';
                }
            );

            $volt->getCompiler()->addFunction(
                'print_r',
                function($arg)
                {
                    return 'print_r('.$arg.')';
                }
            );

            $volt->getCompiler()->addFunction(
                'count',
                function($arg)
                {
                    return 'count('.$arg.')';
                }
            );

            $volt->getCompiler()->addFunction(
                'isset',
                function($arg)
                {
                    return 'isset('.$arg.')';
                }
            );

            $volt->getCompiler()->addFunction(
                'number_format',
                function($arg)
                {
                    return 'number_format('.$arg.')';
                }
            );

            $volt->getCompiler()->addFunction(
                'json_decode',
                function($arg)
                {
                    return 'json_decode('.$arg.', true)';
                }
            );

            $volt->getCompiler()->addFunction(
                'implode',
                function($arg)
                {
                    return 'implode('.$arg.')';
                }
            );

			$volt->getCompiler()->addFunction(
					'image',
					function($arg)
					{
						return 'Signa\Helpers\View::image("'.$arg.'")';
					}
			);

            $volt->getCompiler()->addFunction(
                'getServerName',
                function()
                {
                    return 'serverName()';
                }
            );

			$volt->getCompiler()->addFunction(
				'staticUrl',
				function($strResourceUrl)
				{
					return Signa\Controllers\ControllerBase::getStaticUrl($strResourceUrl);
				}
			);

			$volt->getCompiler()->addFunction(
				'checkDev',
				function($strResourceUrl) use ($view)
				{
					$dev = checkEnv();
					if($dev == 'development'){
						return 'true';
					} else {
						return 'false';
					}
				}
			);

            // get current part of day for greeting user
            $volt->getCompiler()->addFunction(
                'greetingPartOfDay',
                function() use ($locale)
                {
                    $partOfDay = (new PartOfDay())->getCurrentPartOfDay();
                    return 'Signa\Helpers\Translations::make("'.$partOfDay.'", "'.$locale.'")';
                }
            );

            // get slug of current user organisation
            $volt->getCompiler()->addFunction(
                'organisationSlug',
                function() use ($locale, $di)
                {
                    $user = $di->get('session')->get('auth');
                    if ($user) {
                        return '"'.$user->Organisation->OrganisationType->getSlug().'"';
                    } else {
                        return '"default"';
                    }
                }
            );

            /**
             * check if user has access from ACL
             */
            $volt->getCompiler()->addFunction(
                'hasAccess',
                function($organisation, $controller, $action) use ($view)
                {
                    $access = new Access();
                    if($access->hasAccess($organisation, $controller, $action)){
                        return 'true';
                    } else {
                        return 'false';
                    }
                }
            );

            /**
             * get url for paginator with filters
             */
            $volt->getCompiler()->addFunction(
                'getPageUrl',
                function($pageNumber)
                {
                    return 'Signa\Libs\Paginator::getPageUrl('.$pageNumber.')';
                }
            );

            /**
             * translations filter for volt
             */
			$volt->getCompiler()->addFilter(
				't',
				function($resolvedArgs) use ($locale)
				{
					return 'Signa\Helpers\Translations::make('.$resolvedArgs.', "'.$locale.'")';
				}
			);

            /**
             * date to nl format
             */
            $volt->getCompiler()->addFilter(
                'dttonl',
                function($resolvedArgs)
                {
                    return 'Signa\Helpers\Date::makeDateEU('.$resolvedArgs.')';
                }
            );

            /**
             * date to nl format
             */
            $volt->getCompiler()->addFilter(
                'dttonormal',
                function($resolvedArgs)
                {
                    return 'Signa\Helpers\Date::makeDateNormal('.$resolvedArgs.')';
                }
            );


            /**
             * date to nl format
             */
            $volt->getCompiler()->addFilter(
                'dttoDMY',
                function($resolvedArgs)
                {
                    return 'Signa\Helpers\Date::makeDateEU('.$resolvedArgs.')';
                }
            );

            /**
             * Truncate strings for volt
             */
            $volt->getCompiler()->addFilter(
                'truncate',
                function ($str) {
                    return 'Signa\Helpers\View::truncate(' . $str . ')';
                }
            );

            $volt->getCompiler()->addFilter(
                'decodeString',
                function ($str) {
                    return 'Signa\Helpers\View::decodeString(' . $str . ')';
                }
            );

            /**
             * add version to files
             */
            $volt->getCompiler()->addFilter(
                'addVersion',
                function($asset) {
                    return 'Signa\Helpers\Version::addVersion(' . $asset . ')';
                }
            );

			return $volt;
		},
		'.phtml' => 'Phalcon\Mvc\View\Engine\Php'
	));

	return $view;
}, true);


$di->set('db', function() use ($config, $di) {
	$connection = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
		"host" => $config->database->host,
		"username" => $config->database->username,
		"password" => $config->database->password,
		"dbname" => $config->database->name
	));

	# @todo, read from config / querystring
//	if( true ){
//		#
//		# set listner to log database queries
//		#
//		$eventsManager = $di->getShared('eventsManager');
//		$dbListener    = new \Signa\Helpers\DatabaseListener();
//		$eventsManager->attach('db', $dbListener);
//		$connection->setEventsManager($eventsManager);
//	}

	return $connection;
}, true);

$di->set('router', function(){
    require __DIR__.'/router.php';
    return $router;
});

$di->set('security', function(){
    $security = new Phalcon\Security();
    $security->setWorkFactor(12);
    return $security;
}, true);

$di->set('messages', function() {
    $messages = new Messages();
    return $messages;
}, true);

$di->set('flash', function(){
    $flash = new \Phalcon\Flash\Direct(array(
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ));
    return $flash;
});
$di->set('flashSession', function(){
    $flash = new \Phalcon\Flash\Session(array(
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ));
    return $flash;
});

$di->set('dispatcher', function() use ($di) {
    $eventsManager = $di->getShared('eventsManager');
    $routing = new RoutingCheck($di);
    $eventsManager->attach('dispatch', $routing);

    $dispatcher = new Phalcon\Mvc\Dispatcher();
    $dispatcher->setEventsManager($eventsManager);
	$dispatcher->setDefaultNamespace('Signa\Controllers');
    return $dispatcher;
});

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

$di->set('collectionManager', function(){
	return new Phalcon\Mvc\Collection\Manager();
}, true);

/**
 * Access Control List
 */
$di->set('acl', function () use ($di, $config){

    $aclSecurity = new Security($config);
    return $aclSecurity->getAcl();

});

$di->set('access', function() {
    return new Access();
});

$di->set('mailer', function() use ($config){

	#
	# setup smtp connection
	#
    $transport = (new \Swift_SmtpTransport($config->mailer->smtp_host, $config->mailer->smtp_port/*, $config->mailer->smtp_protocol*/))
		->setUsername($config->mailer->smtp_user)
		->setPassword($config->mailer->smtp_pass)
	;
//	$transport->setLocalDomain('[127.0.0.1]');
    $mailer = new \Swift_Mailer($transport);
    return $mailer;
});

#
# Where is roles used for
#
$di->set('roles', function()
{
	$roles = new Roles();
	return $roles;
});

$di->set('notifications', function() {
    return new Notifications();
});

$di->set('t', function() {
    return new Signa\Helpers\Translations;
});

$di->set('simpleView', function() use ($config, $di) {
    $simpleView = new Phalcon\Mvc\View\Simple();
    $simpleView->setViewsDir($config->application->viewsDir);
    $simpleView->setDI($di);
    $simpleView->registerEngines(
        [
            ".volt"  => function ($simpleView) {
                $config = $this->getConfig();

                $volt = new VoltEngine($simpleView, $this);
                $volt->setOptions([
                    'compiledPath' => $config->application->cacheDir,
                    'compiledSeparator' => '_',
                    'compileAlways' => $config->volt->compileAlways
                ]);
                $volt->getCompiler()->addFilter(
                    't',
                    function($resolvedArgs)
                    {
                        return 'Signa\Helpers\Translations::make('.$resolvedArgs.')';
                    }
                );
                $volt->getCompiler()->addFunction(
                    'productImage',
                    function($arg)
                    {
                        return 'Signa\Helpers\View::productImage('.$arg.')';
                    }
                );
                $volt->getCompiler()->addFunction(
                    'fixUrlSpaces',
                    function($var)
                    {
                        $fixedVar = preg_replace('/\s+/', '%20', $var);
                        return $fixedVar;
                    }
                );
                $volt->getCompiler()->addFilter('isArray', function($resolvedArgs) {
                    return 'is_array('.$resolvedArgs.')';
                });
                $volt->getCompiler()->addFunction(
                    'timetostrdt',
                    function($arg)
                    {
                        return 'date_format(new DateTime('.$arg.'), "d-m-Y")';
                    }
                );
                $volt->getCompiler()->addFunction(
                    'datetimetotime',
                    function($arg)
                    {
                        return 'date_format(new DateTime('.$arg.'), "H:i:s")';
                    }
                );

                return $volt;
            },
        ]
    );
    return $simpleView;
});

$di->setShared('mongoLogger', new MongoLogger($profiler));

$di->set('imageThumb', function () {
    return new \Signa\Libs\ImageThumb();
});
