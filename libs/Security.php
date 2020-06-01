<?php
/**
 * Created by PhpStorm.
 * User: Pawel
 * Date: 2016-08-02
 * Time: 00:16
 */

namespace Signa\Libs;

use Phalcon\Acl\Adapter\Memory as AclList;
use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Resource;
use Phalcon\Config\Adapter\Ini;

/**
 * Class Security
 * set ACL rules
 * @package Signa\Libs
 */
class Security
{
    /**
     * @var AclList
     */
    private $acl;

    /**
     * @var Ini
     */
    private $config;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * Security constructor.
     * @param Ini $config
     */
    public function __construct(Ini $config)
    {
        $this->config = $config;

        // build acl rules
        $this->buildAcl();
    }

    private function createAclRules() {

        $this->acl->setDefaultAction(Acl::DENY);
        $this->acl->setNoArgumentsDefaultAction(Acl::ALLOW);
        $this->acl->addRole('ROLE_ADMIN');

        foreach ($this->config->access as $organisation => $controllers) {

            foreach ($controllers as $controller => $actions) {

                // check if resource exist in ACL
                if (!$this->acl->isResource($controller)) {

                    $access = array_map('strtolower', array_keys((array)$actions));
                    $this->acl->addResource(new Resource($organisation.$controller), $access);
                }

                foreach ($actions as $action => $roles) {

                    foreach ($roles as $role) {

                        // check if role exist in ACL
                        if (!$this->acl->isRole($role)) {
                            $this->acl->addRole($role);
                        }

//if ($role == 'ROLE_LAB_USER_MASTERKEY') {
//    echo $organisation.'<br />';
//    print_r(array_keys((array)$actions));
//    die;
//}
                        if (!$organisation) {
                            $organisation = 'default';
                        }

//                        echo $role.' '.$organisation.$controller.' '.$action.' - '.$organisation.'<br />';

                        $this->acl->allow($role, $organisation.$controller, strtolower($action), function($checkingOrganisation) use ($organisation){
                            if ($organisation == 'default') {
                                return true;
                            } else {
                                return $checkingOrganisation == $organisation;
                            }
                        });
                    }

                    // add manual admin access
                    $this->acl->allow('ROLE_ADMIN', $organisation.$controller, strtolower($action));
                }
            }
        }
    }

    /**
     * create new or rebuild exist rules
     * sometimes when we change roles we need rebuild stored acl
     */
    public function rebuildAcl() {

        $this->acl = new AclList();
        $this->createAclRules();
        $this->saveAcl();
    }

    /**
     * create Acl object from stored files or crate new rules if file don't exist
     */
    private function buildAcl() {

        /**
         * @TODO: store in cache or somewhere
         */
        $this->rebuildAcl();

//        // check if we have stored acl ruls
//        if (!is_file($this->config->application->securityDir."acl.data")) { // if we don;t have stored acl rules we have to build new
//
//            $this->rebuildAcl();
//
//        } else {
//
//            // Restore ACL object from serialized file
//            $this->acl = unserialize(file_get_contents($this->config->application->securityDir."acl.data"));
//        }
    }

    /**
     * @return AclList
     */
    public function getAcl() {

        return $this->acl;
    }

    /**
     * save acl rules in file (so we don't need build it every time)
     */
    private function saveAcl() {

//        file_put_contents($this->config->application->securityDir."acl.data", serialize($this->acl));
    }
}