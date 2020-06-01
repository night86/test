<?php

use Phalcon\Events\Event;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\View;

/**
 * Namespaced template rendering for the Phalcon PHP framework
 *
 * This plugin overrides Phalcon's normal render call in order to change the
 * path of the template based on the controller namespace (or any factor you like).
 * As-in, only the deepest component of the namespace is used, so rendering for the
 * controller `MyApp\Controllers\Front\IndexController` will look for templates in
 * `[views-dir]/front/index/[action]`.
 *
 * To use this plugin:
 *
 *     // The application must have the event manager set
 *     $application->setEventsManager($di['eventsManager']);
 *
 *     // Listen for the application's viewRender event
 *     $di['eventsManager']->attach('application:viewRender', new NamespacedRenderer());
 *
 * Based on an issue comment in the phalcon/cphalcon repository:
 * https://github.com/phalcon/cphalcon/issues/692#issuecomment-19109638
 */
class NamespacedRenderer extends Plugin
{
    /**
     * Override the normal view rendering call with one that honours namespaces
     *
     * @param Event $event
     * @param Application $application
     * @param View $view
     * @return bool
     */
    public function viewRender(Event $event, Application $application, View $view)
    {
        $dispatcher = $this->dispatcher;

		$formatedNamespace = strtolower(str_replace('\\', '/', $dispatcher->getNamespaceName()));
        $namespace = basename($formatedNamespace);
		$project = explode('/', $formatedNamespace)[0];
		$projectDirectory = '';
//		if ($project != 'signadens')
//		{
//			$projectDirectory = $project.'/';
//		}
		if ($namespace != 'controllers')
		{
			$controllerName = $namespace . '/' . $dispatcher->getControllerName();
		}
		else
		{
			$controllerName = $dispatcher->getControllerName();
		}
        $view->render($projectDirectory.$controllerName, $dispatcher->getActionName());

        // This handler must return false to prevent the render event bubbling to the
        // normal renderer and breaking the output (ie. a nested main template render)
        return false;
    }
}

