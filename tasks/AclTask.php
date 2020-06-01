<?php

namespace Signa\Tasks;

use Signa\Helpers\Date;
use Signa\Helpers\Import;
use Signa\Libs\Products\ProductsList;
use Signa\Libs\Solr;
use Signa\Models\ImportProducts;
use Signa\Models\OrderShortlist;
use Signa\Models\Products;
use Signa\Models\Users;
use Signa\Models\Roles;
use Phalcon\Http\Client\Request;

class AclTask extends \Phalcon\Cli\Task
{
    public function checkForMissingActionsAction()
    {
        $controllers = [
            'guestauth' => [],
            'lab' => [],
            'dentist' => [],
            'shop' => [],
            'signadens' => [],
            'supplier' => []
        ];

        // general
        foreach (glob(APP_PATH . '/controllers/*Controller.php') as $controller) {
            $className = 'Signa\Controllers\\' . basename($controller, '.php');
            $controllers['guestauth'][$className] = [];
            $methods = (new \ReflectionClass($className))->getMethods(\ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                if (\Phalcon\Text::endsWith($method->name, 'Action')) {
                    $controllers['guestauth'][$className][] = $method->name;
                }
            }
        }

        // lab
        foreach (glob(APP_PATH . '/controllers/Lab/*Controller.php') as $controller) {
            $className = 'Signa\Controllers\Lab\\' . basename($controller, '.php');
            $controllers['lab'][$className] = [];
            $methods = (new \ReflectionClass($className))->getMethods(\ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                if (\Phalcon\Text::endsWith($method->name, 'Action')) {
                    $controllers['lab'][$className][] = $method->name;
                }
            }
        }

        // dentist
        foreach (glob(APP_PATH . '/controllers/Dentist/*Controller.php') as $controller) {
            $className = 'Signa\Controllers\Dentist\\' . basename($controller, '.php');
            $controllers['dentist'][$className] = [];
            $methods = (new \ReflectionClass($className))->getMethods(\ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                if (\Phalcon\Text::endsWith($method->name, 'Action')) {
                    $controllers['dentist'][$className][] = $method->name;
                }
            }
        }

        // shop
        foreach (glob(APP_PATH . '/controllers/Shop/*Controller.php') as $controller) {
            $className = 'Signa\Controllers\Shop\\' . basename($controller, '.php');
            $controllers['shop'][$className] = [];
            $methods = (new \ReflectionClass($className))->getMethods(\ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                if (\Phalcon\Text::endsWith($method->name, 'Action')) {
                    $controllers['shop'][$className][] = $method->name;
                }
            }
        }

        // signadens
        foreach (glob(APP_PATH . '/controllers/Signadens/*Controller.php') as $controller) {
            $className = 'Signa\Controllers\Signadens\\' . basename($controller, '.php');
            $controllers['signadens'][$className] = [];
            $methods = (new \ReflectionClass($className))->getMethods(\ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                if (\Phalcon\Text::endsWith($method->name, 'Action')) {
                    $controllers['signadens'][$className][] = $method->name;
                }
            }
        }

        // supplier
        foreach (glob(APP_PATH . '/controllers/Supplier/*Controller.php') as $controller) {
            $className = 'Signa\Controllers\Supplier\\' . basename($controller, '.php');
            $controllers['supplier'][$className] = [];
            $methods = (new \ReflectionClass($className))->getMethods(\ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                if (\Phalcon\Text::endsWith($method->name, 'Action')) {
                    $controllers['supplier'][$className][] = $method->name;
                }
            }
        }

//        print_r($controllers);
//
//        print_r($this->config->access); die;
//        echo "\n" . sprintf('Found connections: %s', count($shortlists));

        $missed = 0;
        $correct = 0;
        foreach ($controllers as $groupName => $controllersInGroup) {
            $gName = $groupName;
            if ($groupName == 'general') { $gName = 'guestauth'; } // we dont have general in security array

            foreach ($controllersInGroup as $classNamespace => $actions) {
                $classNameArr = explode('\\', $classNamespace);
                $classCoreNameArr = explode('Controller', $classNameArr[count($classNameArr) - 1]);
                $className = $this->from_camel_case($classCoreNameArr[0]);
//                echo "\n" . sprintf('Class name: %s', $className);

                foreach ($actions as $k => $action) {
                    $actionNameArr = explode('Action', $action);
                    array_pop($actionNameArr);
                    $actionName = implode('Action', $actionNameArr);
//                    echo "\n" . sprintf('Action name: %s', $actionName);

                    if (isset($this->config->access[$gName][$className][$actionName])) {
                        unset($controllers[$groupName][$classNamespace][$k]);
                        $correct++;
                    } else {
                        $missed++;
//                        unset($controllers[$groupName][$classNamespace][$k]);
                    }
                }
            }
        }

        // clear $controllers array
        foreach ($controllers as $groupName => $controllersInGroup) {
            foreach ($controllersInGroup as $classNamespace => $actions) {
                if (empty($actions)) {
                    unset($controllers[$groupName][$classNamespace]);
                }
            }
        }
        foreach ($controllers as $groupName => $controllersInGroup) {
            if (empty($controllersInGroup)) {
                unset($controllers[$groupName]);
            }
        }

        echo "\n" . sprintf('Missing actions: %s', $missed);
        echo "\n" . sprintf('Correct actions: %s', $correct);
        echo "\n";
        echo "\n";

        print_r($controllers);

        echo "\n";
        echo "\n";
        echo "\n"."Done! :-)"."\n";
        echo "\n";
    }

    private function from_camel_case($input) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }
}