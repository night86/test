<?php

namespace Signa\Tasks;

use Signa\Models\Logs;
use Signa\Models\Purchase;
use Signa\Models\Users;
use Signa\Models\Roles;

class UserTask extends \Phalcon\Cli\Task
{
	/**
	 * default action for now it shouldn't work
	 */
    public function userAction()
    {
        echo "\n \n";
    }

	/**
     * @param array $params
     */
	public function fixEmptyRegisteredFieldAction(/*array $params*/)
    {
		$users = Users::find(array(
			'conditions'	=> 'registered IS NULL OR registered = 0',
			'bind'			=> array()
		));

		// update empty registered date by last login date
		foreach ($users as $user) {
			$user->registered = $user->last_login;
			$user->save();
			echo $user->email."\n";
		}

		echo "\n"."Done!!"."\n";
	}

	public function createRolesFromSecurityAction()
    {
        $security = include APPLICATION_PATH . '/config/security.php';
        $dbRoles = Roles::find();
//        var_dump($dbRoles);
        $dbRolesArr = array();
        $actionRolesArr = array();

        // Get roles from db, to array
        foreach ($dbRoles as $dbRole) {
            $dbRolesArr[] = $dbRole->getName();
        }

        // Get roles from security file
        foreach ($security['access'] as $userType)
        {
            foreach ($userType as $controllerActions)
            {
                foreach ($controllerActions as $action)
                {
                    if(!in_array($action[0], $actionRolesArr))
                        $actionRolesArr[] = $action[0];
                }
            }
        }

        // Compare roles and if not exist, create new one
        foreach ($actionRolesArr as $actionRole) {
            if(!in_array($actionRole, $dbRolesArr))
            {
                $role = new Roles();
                $role->setName($actionRole);
                $role->setDescription($actionRole);
                $role->save();
            }
        }

        echo "\n"."Done!!"."\n";
    }

    public function createFakeImportsAction()
    {
        $fake = json_decode('[{
          "first_name": "Eula",
          "last_name": "Cleatherow",
          "email": "ecleatherow0@youku.com",
          "gender": "Female",
          "ip_address": "31.232.224.217",
          "product": "Chevrolet",
          "url": "https://bloglines.com/elementum/eu/interdum/eu.jpg"
        }, {
          "first_name": "Ross",
          "last_name": "Summerrell",
          "email": "rsummerrell1@independent.co.uk",
          "gender": "Male",
          "ip_address": "147.202.50.80",
          "product": "Scion",
          "url": "http://is.gd/vel/augue/vestibulum.json"
        }, {
          "first_name": "Wylie",
          "last_name": "Sonschein",
          "email": "wsonschein2@tinypic.com",
          "gender": "Male",
          "ip_address": "196.122.54.23",
          "product": "Chevrolet",
          "url": "https://bloglovin.com/pede.js"
        }, {
          "first_name": "Ira",
          "last_name": "Gallichiccio",
          "email": "igallichiccio3@loc.gov",
          "gender": "Male",
          "ip_address": "117.255.8.149",
          "product": "Cadillac",
          "url": "http://addtoany.com/elit.html"
        }, {
          "first_name": "Sela",
          "last_name": "Pagin",
          "email": "spagin4@reference.com",
          "gender": "Female",
          "ip_address": "32.127.99.115",
          "product": "Pontiac",
          "url": "http://typepad.com/vel/augue/vestibulum/rutrum/rutrum/neque/aenean.html"
        }, {
          "first_name": "Cinnamon",
          "last_name": "Swoffer",
          "email": "cswoffer5@blogs.com",
          "gender": "Female",
          "ip_address": "191.203.77.27",
          "product": "Chevrolet",
          "url": "https://chicagotribune.com/viverra/diam/vitae/quam/suspendisse/potenti.jsp"
        }, {
          "first_name": "Randal",
          "last_name": "Ballefant",
          "email": "rballefant6@mozilla.com",
          "gender": "Male",
          "ip_address": "184.111.211.249",
          "product": "Dodge",
          "url": "http://nba.com/vulputate/justo.aspx"
        }, {
          "first_name": "Roxi",
          "last_name": "Hardwidge",
          "email": "rhardwidge7@issuu.com",
          "gender": "Female",
          "ip_address": "45.177.43.32",
          "product": "Porsche",
          "url": "https://over-blog.com/sapien/quis/libero/nullam/sit.jsp"
        }, {
          "first_name": "Silvano",
          "last_name": "Crang",
          "email": "scrang8@vistaprint.com",
          "gender": "Male",
          "ip_address": "57.65.94.134",
          "product": "Pontiac",
          "url": "http://friendfeed.com/luctus.json"
        }, {
          "first_name": "Camilla",
          "last_name": "Fernie",
          "email": "cfernie9@cocolog-nifty.com",
          "gender": "Female",
          "ip_address": "243.252.124.184",
          "product": "Mercury",
          "url": "http://comsenz.com/pede/posuere/nonummy/integer.json"
        }]' ,true);


        foreach ($fake as $k => $f){
            $purchase = new Purchase();
            foreach ($f as $key => $param){
                $purchase->$key = $param;
            }
            $purchase->save();
        }
    }

    public function readFakeImportAction(){
	    $purchase = Purchase::find([
	        [
	            'first_name' => 'Roxi'
            ]
        ]);

	    var_dump(count($purchase));
    }
}

