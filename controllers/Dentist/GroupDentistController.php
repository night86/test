<?php

namespace Signa\Controllers\Dentist;

use Signa\Models\Users;
use Signa\Helpers\User as UserHelper;
use Signa\Helpers\Translations as Trans;

class GroupDentistController extends InitController
{
    public function indexAction(){

        if($this->request->isPost()){

            $email = $this->request->getPost('email');
            $user = Users::findFirst("email LIKE '".$email."'");
            $title = Trans::make("Invitation to cooperate with"). ' ' .$this->currentUser->Organisation->getName();
            $emailEncoded = urlencode($email);

            if($user !== false){

                $acceptUrl = '/api/existinguserinvitation?email='.$emailEncoded.'&org='.$this->currentUser->getOrganisationId().'&status=accept';
                $declineUrl = $this->baseUrl.'/api/existinguserinvitation?status=decline';
                $params = array('declineUrl' => $declineUrl, 'button' => array('url' => $acceptUrl, 'text'=> Trans::make("Accept")));

                $sended = $this->mail->send(array($user->getEmail() => $user->getFullName()), $title, 'inviteDentalGroupExistingDentist', $params);
            }
            else {
                $userName = explode('@', $email);
                $acceptUrl = '/api/newuserinvitation?email='.$emailEncoded.'&org='.$this->currentUser->getOrganisationId().'&status=accept';
                $params = array('button' => array('url' => $acceptUrl, 'text'=> Trans::make("Accept")));
                $sended = $this->mail->send(array($email => $userName[0]), $title, 'inviteDentalGroupExistingDentist', $params);
            }

            return json_encode(array('status' => (bool)$sended, 'email' => $email));
        }

        // View vars and assets
        $this->assets->collection('footer')
            ->addJs("js/app/client.js");
        $this->view->users = Users::find(array('deleted = 0 AND organisation_id = '.$this->currentUser->getOrganisationId()));
        $this->view->active = array($this->t->make('No'),$this->t->make('Yes'));
        $this->view->inviteContent = $this->inviteContent();
    }

    public function editAction($id){

        $user = Users::findFirst($id);

        if ($this->currentUser->Organisation->OrganisationType->getSlug() != 'signadens' && $user->Organisation->getOrganisationTypeId() != $this->currentUser->Organisation->getOrganisationTypeId()) {

            $this->response->redirect('/'.$this->currentUser->Organisation->OrganisationType->getSlug().'/group_dentist/');
            $this->view->disable();
            return;
        }

        if ($this->request->isPost()){

            $user->setPassword($this->request->getPost('password'));
            $user->setFirstname($this->request->getPost('firstname'));
            $user->setLastname($this->request->getPost('lastname'));
            $user->setAddress($this->request->getPost('address'));
            $user->setZipCode($this->request->getPost('zip_code'));
            $user->setCity($this->request->getPost('city'));
            $user->setCountry($this->request->getPost('country'));
            $user->setTelephone($this->request->getPost('telephone'));
            $user->setActive($this->request->getPost('active'));
            $user->setEmail($this->request->getPost('email'));

            if ($user->save() !== false) {

                $this->session->set('message', ['type' => 'success','content' => Trans::make('User has been edited.')]);
                $this->response->redirect('/'.$this->currentUser->Organisation->OrganisationType->getSlug().'/group_dentist/');
                $this->view->disable();
                return;
            }
            else {
                $this->session->set('message', ['type' => 'warning','content' => Trans::make("User can't be edited.")]);
                $this->response->redirect('/'.$this->currentUser->Organisation->OrganisationType->getSlug().'/group_dentist/edit/'.$user->getId());
                $this->view->disable();
                return;
            }
        }
        $this->view->user = $user;
        $this->view->organisation = $this->currentUser->Organisation->getName();
        $this->view->active = array($this->t->make('No'),$this->t->make('Yes'));
    }

    public function deactivateAction($id){

        $user = Users::findFirst($id);

        if($user){

            $user->deactivate();
            $this->session->set('message', ['type' => 'success','content' => Trans::make('User has been deactivated.')]);
        }
        else {
            $this->session->set('message', ['type' => 'warning','content' => Trans::make('User is allready deactivated.')]);
        }
        $this->response->redirect('/'.$this->currentUser->Organisation->OrganisationType->getSlug().'/group_dentist/');
        $this->view->disable();
        return;
    }

    public function activateAction($id){

        $user = Users::findFirst($id);

        if($user) {
            $user->activate();
            $this->session->set('message', ['type' => 'success','content' => Trans::make('User has been activated.')]);
        }
        else {
            $this->session->set('message', ['type' => 'warning','content' => Trans::make('User is allready activated.')]);
        }
        $this->response->redirect('/'.$this->currentUser->Organisation->OrganisationType->getSlug().'/group_dentist/');
        $this->view->disable();
        return;
    }

    /**
     * login as user without password - only for admins
     */
    public function loginasuserAction($userId){

        $user = Users::findFirst($userId);

        if (!$user) {
            throw new \Exception('User not exist');
        }

        $userHelper = new UserHelper($this->session, $this->cookies);
        $redirectUrl = '';

        try {
            $userHelper->logInAsUser($user, $this->currentUser);
            $redirectUrl = $this->access->getDefaultUrl();

        } catch (\Exception $e) {
            $this->view->error = $e->getMessage();
            return;
        }
        $this->response->redirect($redirectUrl);
    }

    /**
     * back to admin from logged user (from login without password)
     */
    public function backtoadminAction(){

        $userHelper = new UserHelper($this->session, $this->cookies);
        $admin = $userHelper->backToAdminUser();
        $this->response->redirect('/'.$this->currentUser->Organisation->OrganisationType->getSlug().'/group_dentist/');
    }

    private static function inviteContent()
    {
        $html = '<p>'.Trans::make("By entering a dentist admin e-mailadres you'll be able to invite them. If this dentist isn' registered they will receive an email to create an account.").'</p>';
        $html .= '<label>'.Trans::make("Admin E-mail").'</label><input type="email" name="email" id="email-value" class="form-control">';

        return $html;
    }
}
