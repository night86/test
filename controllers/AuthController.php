<?php

namespace Signa\Controllers;

use Signa\Models\Users;
use Signa\Models\UserResetPasswords;
use Signa\Helpers\Import;
use Signa\Helpers\Translations as Trans;

class AuthController extends ControllerBase
{
    public function loginAction(){

        if ($this->request->isPost()) {

            // Get the data from the user
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $remember = $this->request->getPost('remember');

            // Find the user in the database
            $user = Users::findFirst(
                array(
                    "(email = :email:) AND password = :password: AND active = '1' AND deleted = '0'",
                    'bind' => array(
                        'email' => $email,
                        'password' => md5($password)
                    )
                )
            );
            $userTmp = Users::findFirst("email = '".$email."'");

            if ($user != false) {

                $this->mongoLogger->createLog(
                    array(
                        'datetime' => date('d-m-Y H:i:s'),
                        'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                        'page' => '/login',
                        'action' => 'login',
                        'user_id' => $user->getId(),
                        'username' => $user->getFirstname() . ' ' . $user->getLastname(),
                        'email' => $email,
                        'organisation_id' => $user->getOrganisationId(),
                        'state' => 'success',
                        'isopened' => false
                    ),
                    $email);

                $this->_registerSession($user);
                $newUser = ($user->getLastLogin() == NULL) ? true : false;
                $user->updateLastLogin();

                if ((bool)$remember) {
                    $this->cookies->set('rememberMe', $this->session->get('auth')->getId(), time() + 86400, '/');
                }

                // Forward to the controller if the user is valid
                if($newUser == true || $user->getStartPage() == 1){

                    $redirectUrl = $user->Organisation->OrganisationType->getSlug().'/index/start/';
                }
                else {
                    $redirectUrl = $this->access->getDefaultUrl();
                }
                $this->response->redirect($redirectUrl);
                $this->view->disable();
                return;
            }
            elseif ($userTmp != false) {

                $state = 'wrong password';

                if ($userTmp->getActive() != 1) {
                    $state = 'not activated';
                }

                if ($userTmp->getDeleted() != 0) {
                    $state = 'user deleted';
                }

                $this->mongoLogger->createLog(
                    array(
                        'datetime' => date('d-m-Y H:i:s'),
                        'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                        'page' => '/login',
                        'action' => 'login',
                        'user_id' => $userTmp->getId(),
                        'username' => $userTmp->getFirstname() . ' ' . $userTmp->getLastname(),
                        'email' => $email,
                        'organisation_id' => $userTmp->getOrganisationId(),
                        'state' => 'success',
                        'isopened' => false
                    ),
                    $email
                );
            }
            else {
                $this->mongoLogger->createLog(
                    array(
                        'datetime' => date('d-m-Y H:i:s'),
                        'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                        'page' => '/login',
                        'action' => 'login',
                        'user_id' => null,
                        'username' => null,
                        'email' => $email,
                        'organisation_id' => null,
                        'state' => 'user not exist',
                        'isopened' => false
                    ),
                    $email
                );
            }

            $message = [
                'type' => 'error',
                'content' => 'Wrong email/password.'
            ];
            $this->session->set('message', $message);
        }
        $this->view->isHeader = false;
    }

    public function logoutAction(){

        $this->cookies->get('rememberMe')->delete();

        $this->session->destroy();
        $this->response->redirect('/');
        $this->view->disable();
        return;
    }

    private function _registerSession($user){

        $userRolesArr = array();

        foreach ($user->UserRoles as $userRole) {

            if ($userRole->Role) {

                $userRolesArr[] = $userRole->Role->getName();
            }
        }
        $this->session->set('auth', $user);
        $this->session->set('roles', $userRolesArr);
    }

    public function forgetpasswordAction(){

        if ($this->request->isPost()) {

            $user = Users::findFirstByEmail($this->request->getPost('email'));

            if (!$user) {

                $message = [
                    'type' => 'error',
                    'content' => 'Success: If there is an account associated, the email for password recovery is send.'
                ];
                $this->session->set('message', $message);
            }
            else {
                $resetPassword = new UserResetPasswords();
                $resetPassword->setUserId($user->getId());
                $resetPassword->save();

                if ($resetPassword->save()) {

                    $sendEmail = $this->mail->send($user->getEmail(), Trans::make("Reset your password"), 'resetPassword', array('resetUrl' => $this->baseUrl.'/auth/resetpassword/'.$resetPassword->getCode()));

                    if($sendEmail != false){
                        $message = [
                            'type' => 'success',
                            'content' => 'Success! Please check your messages for an email reset password.'
                        ];
                    }
                    else {
                        $message = [
                            'type' => 'error',
                            'content' => 'Error when sending email.'
                        ];
                    }
                    $this->session->set('message', $message);
                }
                else {
                    foreach ($resetPassword->getMessages() as $message) {

                        $messages = [
                            'type' => 'success',
                            'content' => $message
                        ];
                        $this->session->set('message', $messages);
                    }
                }
            }
        }
        $this->view->isHeader = false;
    }

    public function resetpasswordAction(){

        $params = $this->dispatcher->getParams();
        $code = $params[0];
        $resetPassword = UserResetPasswords::findFirstByCode($code);

        if (!$resetPassword) {

            $message = [
                'type' => 'error',
                'content' => 'Invalid reset code.'
            ];
            $this->session->set('message', $message);
            $this->response->redirect('/');
            $this->view->disable();
            return;
        }

        if ($resetPassword->getReset() != 'N') {

            $message = [
                'type' => 'error',
                'content' => 'Password was already resetted.'
            ];
            $this->session->set('message', $message);
            $this->response->redirect('/');
            $this->view->disable();
            return;
        }
        $resetPassword->setReset('Y');

        // Change confirmation to 'reset'
        if (!$resetPassword->save()) {

            foreach ($resetPassword->getMessages() as $message) {

                $messages = [
                    'type' => 'success',
                    'content' => $message
                ];
                $this->session->set('message', $messages);
            }
        }
        $this->resetUserPassword($resetPassword->getUserId());
    }

    private function resetUserPassword($userId){

        $user = Users::findFirst($userId);
        $newPassword = Import::generateRandomString();
        $user->setPassword($newPassword);

        if (!$user->save()) {

            foreach ($user->getMessages() as $message) {

                $messages = [
                    'type' => 'success',
                    'content' => $message
                ];
                $this->session->set('message', $messages);
            }
            $this->response->redirect('/');
            $this->view->disable();
            return;
        }

        $sendEmail = $this->mail->send($user->getEmail(), Trans::make("New password"), 'newPassword', array('newUrl' => '', 'password' => $newPassword));

        if($sendEmail != false){
            $message = [
                'type' => 'success',
                'content' => Trans::make('New password has been sent')
            ];
        }
        else {
            $message = [
                'type' => 'error',
                'content' => Trans::make('Error while changing password, please contact Signadens admin')
            ];
        }

        $this->session->set('message', $message);
        $this->response->redirect('/auth/login');
        $this->view->disable();
        return;
    }
}
