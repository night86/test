<?php

namespace Signa\Controllers;

use Signa\Models\Invites;
use Signa\Models\LabDentists;
use Signa\Models\OrganisationTypes;
use Signa\Models\RoleTemplates;
use Signa\Models\Users;
use Signa\Helpers\General;
use Signa\Models\Organisations;
use Signa\Helpers\Translations as Trans;

class ApiController extends ControllerBase
{
    /*
     * Add notification to lab admin
     */
    public function existinguserinvitationAction(){

        if(isset($_GET['status'])){

            $email = rawurldecode($_GET['email']);
            $user = Users::findFirst("email LIKE '".$email."'");
            $status = $_GET['status'];

            if($status === 'accept'){

                $labDentistExist = LabDentists::findFirst('lab_id = '.$_GET['lab'].' AND dentist_id = '.$_GET['den']);

                $closeInvite = Invites::findFirst("email = '".$email."' AND deleted = '0' AND sended = '1' ORDER BY created_at DESC");
                $closeInvite->setRegistered(1);
                $closeInvite->setUpdatedAt(date("Y-m-d H:i:s"));
                $closeInvite->setUpdatedBy($user->getId());
                $closeInvite->save();

                $labDentistExist->setStatus('active');
                $labDentistExist->save();

                $this->session->set('message', array('type' => 'success', 'content' => Trans::make('Successfully added your account to the lab clients.')));
                $this->response->redirect("/");
            }
            else {
                $this->session->set('message', array('type' => 'success', 'content' => Trans::make('The information will be send to lab admin.')));
                $this->response->redirect("/");
            }
        }
        else {
            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make('Invalid url address.')));
            $this->response->redirect("/");
        }
    }

    /*
     * Add properly organisation and role templates to new user - dentist
     */
    public function newuserinvitationAction(){

        if(isset($_GET['status']) && $_GET['status'] === 'accept' && isset($_GET['email']) && isset($_GET['lab']) && isset($_GET['den'])){

            $email = rawurldecode($_GET['email']);
            $password = General::randomString();
            $organisationType = OrganisationTypes::findFirst("slug = 'dentist'");
            $roleTemplate = RoleTemplates::findFirst("organisation_type_id = '".$organisationType->getId()."'");
            $userName = explode('@', $email);

            $checkUser = Users::find("email ='".$email."'");

            if(count($checkUser) == 0){

                $organisation = Organisations::findFirst("id = ".$_GET['den']);
                $organisation->setActive(1);
                $organisation->save();

                $user = new Users();
                $user->setOrganisationId($organisation->id);
                $user->setEmail($email);
                $user->setPassword($password);
                $user->setRoleTemplateId($roleTemplate->getId());
                $user->setActive(1);
                $user->setDeleted(0);

                if ($user->create() !== false) {

                    $user->copyRoles();

                    $closeInvite = Invites::findFirst("email = '".$email."' AND deleted = '0' AND sended = '1' ORDER BY created_at DESC");
                    $closeInvite->setRegistered(1);
                    $closeInvite->setUpdatedAt(date("Y-m-d H:i:s"));
                    $closeInvite->setUpdatedBy($user->getId());
                    $closeInvite->save();

                    $user->save();

                    $labDentist = LabDentists::findFirst("lab_id = ".$_GET['lab']." AND dentist_id = ".$_GET['den']);
                    $labDentist->setStatus('active');
                    $labDentist->save();
                    
                    $params = array('email' => $user->getEmail(), 'password' => $password);

                    $sended = $this->mail->send($user->getEmail(), Trans::make('New account in Signadens'), 'dentistNewAccount', $params);

                    if ($sended) {
                        $this->session->set('message', array('type' => 'success', 'content' => Trans::make('Successfully created and added account to the lab clients. On the mail box will be send yours authorisation data.')));
                    }
                    else {
                        $this->session->set('message', array('type' => 'warning', 'content' => Trans::make('There is an error with creating new account.')));
                    }
                }
                else {
                    $this->session->set('message', array('type' => 'warning', 'content' => Trans::make('There is an error with creating new account.')));
                }
            }
            else {
                $this->session->set('message', array('type' => 'error', 'content' => Trans::make('Account already exists.')));
            }
            $this->response->redirect("/");

        }
        else {
            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make('Invalid url address.')));
            $this->response->redirect("/");
        }
    }

    public function termsofuseAction(){

        if($this->request->isPost()){

            $post = $this->request->getPost();

            if($post['accept_terms'] == 'Inloggen'){

                $this->response->redirect('/api/newuserinvitation?email='.$post['email'].'&lab='.$post['lab'].'&status=accept&name='.$post['name'].'&den='.$post['den']);
            }
        }
        $this->view->disableSubnav = true;

        if(isset($_GET['lab'])){
            $this->view->lab = Organisations::findFirst('id='.$_GET['lab']);
        }
    }
}
