<?php

namespace Signa\Controllers;

use Phalcon\Exception;
use Signa\Helpers\Date;
use Signa\Helpers\User;
use Signa\Libs\Mail;
use Signa\Models\FrameworkAgreements;
use Signa\Models\Notifications;
use Signa\Models\Organisations;
use Signa\Models\Users;
use Phalcon\Mvc\View;
use Signa\Models\FileSharedUser;
use Signa\Models\SettingFiles;
use Signa\Helpers\Translations as Trans;

class NotificationController extends ControllerBase
{
    public function indexAction(){

        if ($this->request->hasQuery('type')) {
            $type = $this->request->getQuery('type');
        }
        else {
            $type = '';
        }
        $this->view->notificationType = $type;
    }

    public function archiveAction(){

        $this->view->notifications = Notifications::find('deleted_at IS NULL and archived_at IS NOT NULL AND user_id = '.$this->currentUser->getId().' ORDER BY created_at DESC');
    }

    public function readAction(){

        if($this->request->isPost()){

            $id = $this->request->getPost('id');
            $unread = $this->request->getPost('read_at');
            $organisationId = $this->currentUser->getOrganisationId();

            $notification = Notifications::findFirst($id);
            $dateToCompare = $notification->getCreatedAt();
            $whoToCompare = $notification->getCreatedBy();
            $notifications = Notifications::find(
                [
                    "created_at = :date: AND created_by = :who: AND organisation_to = :organisation:",
                    "bind" => [
                        "date" => $dateToCompare,
                        "who" => $whoToCompare,
                        "organisation" => $organisationId
                    ]
                ]
            );


            foreach ($notifications as $notification){

                if($unread == null) {
                    $notification->setReadAt(date('Y-m-d H:i:s'));
                }
                else {
                    $notification->setReadAt(NULL);
                }
                $notification->save();
            }
            die;
        }
    }

    public function toarchiveAction($id){

        $notification = Notifications::findFirst($id);

        if ($notification && $notification->getUserId() === $this->currentUser->getId()) {

            $notification->setArchivedAt(Date::currentDatetime());
            $notification->save();
        }
        $this->response->redirect('notification/index');
        $this->view->disable();
    }

    public function printAction($type = null){

        if (is_null($type)){

            $rules = array(
                sprintf('deleted_at IS NULL and archived_at IS NULL AND user_id = %s AND (send_at IS NULL OR send_at <= "%s")  ORDER BY created_at DESC', $this->currentUser->getId(), date('Y-m-d'))
            );
            $name = "inbox_" . date("d-m-Y") . '.pdf';
        }
        else {
            $rules = array(
                sprintf('deleted_at IS NULL and archived_at IS NOT NULL AND user_id = %s AND (send_at IS NULL OR send_at <= "%s")  ORDER BY created_at DESC', $this->currentUser->getId(), date('Y-m-d'))
            );
            $name = "archief_" . date("d-m-Y") . '.pdf';
        }
        $notifications = Notifications::find($rules);
        $view = clone $this->view;
        $view->start();
        $view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $view->setVars(array(
            'notofications'        => $notifications,
        ));
        $view->render('pdf','notifications');
        $view->finish();

        $html = $view->getContent();

        $pdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'tempDir' => $this->config->application->cacheDir,
            'setAutoTopMargin' => 'stretch'
        ]);

        $stylesheet = file_get_contents(__DIR__.'/../../public/css/main.css');
        $pdf->WriteHTML($stylesheet,1);
        $pdf->WriteHTML($html,2);

        $pdf->Output($name, "D");

        foreach ($notifications as $notification) {

            if(null !== $notification->getSendAt()) {

                var_dump('EMAIL');
            }
        }
        die;
    }

    public function ajaxlistAction(){

        if($this->request->isAjax()) {

            if ($this->request->hasQuery('type')) {

                $type = ' AND type = '.$this->request->getQuery('type');
            }
            else {
                $type = '';
            }
            $rules = array(
                sprintf('deleted_at IS NULL and archived_at IS NULL AND user_id = %s AND (send_at IS NULL OR send_at <= "%s") %s ORDER BY created_at DESC', $this->currentUser->getId(), date('Y-m-d'), $type)
            );
            $notifications = Notifications::find($rules);

            $notificationsArr = array();

            foreach ($notifications as $key => $notification) {

                if($notification->getType() == 10) {

                    $subject = $notification->getSubject();
                    $supplierName = Organisations::findFirst(FrameworkAgreements::findFirst($notification->getFrameworkAgreementId())->getSupplierId())->getName();
                    $subject .= " (Framework agreement $supplierName)";
                    $notification->setSubject($subject);
                }

                if (!is_null($notification->getReadAt())) {

                    $notificationsArr[$key]['label'] = Trans::make($notification->getTypeLabel());
                    $notificationsArr[$key]['subject'] = $notification->getSubject();
                    $notificationsArr[$key]['created'] = Date::makeDate($notification->getCreatedAt());
                }
                else {
                    $notificationsArr[$key]['label'] = '<strong>' . $notification->getTypeLabel() . '</strong>';
                    $notificationsArr[$key]['subject'] = '<strong>' . $notification->getSubject() . '</strong>';
                    $notificationsArr[$key]['created'] = '<strong realvalue="'.Date::makeDate($notification->getCreatedAt()).'">' . Date::makeDate($notification->getCreatedAt()) . '</strong>';
                }

                $class = in_array($notification->getType(), [9, 10, 11]) ? 'showModalNonReply' : 'showModal';
                $notificationsArr[$key]['actions'] = '<a class="' . $class . ' btn btn-primary btn-sm" data-id="' . $notification->getId() . '" data-description="' . $notification->getDescription() . '" data-subject="' . $notification->getSubject() . '" data-type="'.$notification->getType().'"><i class="pe-7s-mail-open"></i> ' . $this->t->make('Read') . '</a> ';

                if (!is_null($notification->getReadAt())) {
                    $notificationsArr[$key]['actions'] .= '<a class="markAsUnread btn btn-success btn-sm" data-readat="' . $notification->getReadAt() . '" data-id="' . $notification->getId() . '" ><i class="pe-7s-mail"></i> ' . $this->t->make('Mark as unread') . '</a> ';
                }
                $notificationsArr[$key]['actions'] .= '<a href="/notification/toarchive/' . $notification->getId() . '" class="btn btn-warning btn-sm"><i class="pe-7s-trash"></i> ' . $this->t->make('Archive') . '</a>';
            }

            /** @var Notifications $notification */
            foreach ($notifications as $notification) {

                if ($notification->getType() == 9 && null !== $notification->getSendAt() && !$notification->getEmailSended()) {

                    /** @var Users $user */
                    $user = Users::findFirst($notification->getUserId());
                    $mail = new Mail();

                    try {
                        $mail->send($user->getEmail(), $notification->getSubject(), 'frameworkAgreementDue', ['text' => $notification->getDescription()]);
                        $notification->setEmailSended(1);
                        $notification->save();
                    }
                    catch (\Swift_TransportException $exception) {

                    }
                }
            }
            return json_encode(array('data' => $notificationsArr));
        }
    }

    public function ajaxreplyAction(){

        if($this->request->isAjax()){

            $post = $this->request->getPost();

            $notification = Notifications::findFirst($post['id']);
            $newDescription = sprintf(
                '<p><span class=&quot;small&quot;>%s %s (%s):</span><br /><br />%s</p><hr><div style=\'color:#ccc\'>%s</div>',
                $this->currentUser->getFirstname(),
                $this->currentUser->getLastname(),
                $this->currentUser->Organisation->getName(),
                $post['content'],
                $notification->getDescription()
            );

            $tousers = Users::find(
                sprintf(
                    'organisation_id = %s OR organisation_id = %s',
                    $notification->getOrganisationTo(),
                    $notification->getOrganisationFrom()
                ));

            foreach ($tousers as $touser) {

                if ($touser->getId() != $this->currentUser->getId()) {

                    $newNotification = new Notifications();
                    $newNotification->setType($notification->getType());
                    $newNotification->setUserId($touser->getId());
                    $newNotification->setSubject('Re: ' . $notification->getSubject());
                    $newNotification->setDescription($newDescription);
                    $newNotification->setReplyId($post['id']);
                    $newNotification->setOrganisationFrom($notification->getOrganisationTo());
                    $newNotification->setOrganisationTo($notification->getOrganisationFrom());
                    $status = $newNotification->save();

                }
            }
            return json_encode(array('status' => $status));
        }
    }

    public function confirmfileAction($id){

        $currentUserId = $this->currentUser->getId();
        $fileSharedUser = FileSharedUser::findFirst($id);

        if($currentUserId !== $fileSharedUser->getUserId() || !(bool)$fileSharedUser){

            $this->session->set('message', ['type' => 'error', 'content' => 'File does not exist.']);
            $this->response->redirect('/notification/');
            return true;
        }
        $fileSharedUser->setStatus(1);
        $fileSharedUser->save();

        $settingFile = new SettingFiles();
        $settingFile->setFromUserId($fileSharedUser->getCreatedBy());
        $settingFile->setToUserId($currentUserId);
        $settingFile->setAllow(1);
        $settingFile->save();

        $this->session->set('message', ['type' => 'success', 'content' => 'Successfully accepted request.']);
        $this->response->redirect('/notification/');
        return true;
    }

    public function rejectfileAction($id){

        $currentUserId = $this->currentUser->getId();
        $fileSharedUser = FileSharedUser::findFirst($id);

        if($currentUserId !== $fileSharedUser->getUserId() || !(bool)$fileSharedUser){

            $this->session->set('message', ['type' => 'error', 'content' => 'File does not exist.']);
            $this->response->redirect('/notification/');
            return true;
        }
        $fileSharedUser->setStatus(2);
        $fileSharedUser->save();

        $settingFile = new SettingFiles();
        $settingFile->setFromUserId($fileSharedUser->getCreatedBy());
        $settingFile->setToUserId($currentUserId);
        $settingFile->setAllow(0);
        $settingFile->save();

        $this->session->set('message', ['type' => 'success', 'content' => 'Successfully rejected request.']);
        $this->response->redirect('/notification/');
        return true;
    }
}
