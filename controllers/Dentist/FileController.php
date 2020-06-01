<?php

namespace Signa\Controllers\Dentist;

use Signa\Models\Files;
use Signa\Models\FileSharedOrganisation;
use Signa\Models\FileSharedUser;
use Signa\Models\Organisations;
use Signa\Models\SettingFiles;
use Signa\Helpers\Translations as Trans;
use Signa\Helpers\General;

class FileController extends InitController
{
    public function indexAction(){

        // View vars and assets
        $this->assets->collection('footer')
            ->addJs("bower_components/blueimp-file-upload/js/vendor/jquery.ui.widget.js")
            ->addJs("bower_components/blueimp-file-upload/js/jquery.fileupload.js")
            ->addJs("bower_components/blueimp-file-upload/js/jquery.iframe-transport.js")
            ->addJs("js/app/file.js");

        $this->view->files = Files::find('created_by = '.$this->currentUser->getId());
        $this->view->uploadContent = self::getUploadContent();
        $this->view->shareContent = $this->getShareContent();
        $this->view->disableSubnav = true;
    }

    public function downloadAction($fileId){

        $this->view->disable();

        $file = Files::findFirst($fileId);
        $fileDir = $this->config->application->filesDir . $this->currentUser->getId() . '/';
        $filename = $fileDir. $file->getName();

        if (file_exists($filename)) {

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.$file->getNameOriginal().'"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filename));
            readfile($filename);
            exit;
        }
        else {

            $this->session->set('message', ['type' => 'error','content' => 'File does not exist.']);
            $this->response->redirect('/dentist/file/');
            return true;
        }
    }

    public function deleteAction($fileId){

        $this->view->disable();

        $file = Files::findFirst('id = '.$fileId.' AND created_by = '.$this->currentUser->getId());
        $fileDir = $this->config->application->filesDir . $this->currentUser->getId() . '/';
        $filename = $fileDir. $file->getName();

        // Remove file with every connected organisations/users
        if (file_exists($filename)) {

            unlink($filename);

            $file->deleteWithRelations();
            $this->session->set('message', ['type' => 'success','content' => 'Successfully deleted file.']);
        }
        else {
            $file->deleteWithRelations();
            $this->session->set('message', ['type' => 'error','content' => 'File does not exist.']);
        }
        $this->response->redirect('/dentist/file/');
        return true;
    }

    public function uploadAction(){

        $this->view->disable();

        if($this->request->isAjax()){

            if ($this->request->hasFiles() == true) {

                $fileDir = $this->config->application->filesDir;
                $userFileDir = $fileDir . $this->currentUser->getId(). '/';
                $file = $this->request->getUploadedFiles()[0];
                $randomString = General::randomString(6);
                $filename = $randomString.$file->getName();

                if(!is_dir($fileDir)) {

                    mkdirR($fileDir);
                }

                if(!is_dir($userFileDir)) {

                    mkdirR($userFileDir);
                }
                $file->moveTo($userFileDir.$filename);

                $fileModel = new Files();
                $fileModel->setName($filename);
                $fileModel->setNameOriginal($file->getName());
                $fileModel->setSize($file->getSize());
                $fileModel->setType($file->getType());
                $saved = $fileModel->save();

                if($saved){

                    $this->session->set('message', ['type' => 'success','content' => 'File has been added.']);
                    return json_encode(array('status' => true));
                }
                else {
                    $this->session->set('message', ['type' => 'error','content' => 'File cannot be saved.']);
                    return json_encode(array('status' => false));
                }
            }
            else {
                $this->session->set('message', ['type' => 'error','content' => 'A file is missing.']);
                return json_encode(array('status' => false));
            }
        }
        return true;
    }

    public function shareAction($fileId){

        $this->view->disable();

        if($this->request->isAjax()){

            $organisationIds = $this->request->getPost('ids');
            $oldFilesSharedByFile = FileSharedOrganisation::find('file_id = '.$fileId);

            // Add request for each selected organisations
            foreach ($organisationIds as $organisationId){

                // Check if organisation allready has shared file
                $oldFileShare = FileSharedOrganisation::findFirst('file_id = '.$fileId.' AND organisation_id = '.$organisationId);

                if($oldFileShare){

                    continue;
                }
                else {
                    // Share file with organisation and send notification
                    $fileShare = new FileSharedOrganisation();
                    $fileShare->setFileId($fileId);
                    $fileShare->setOrganisationId($organisationId);
                    $savedFileShareOrg = $fileShare->save();

                    if($savedFileShareOrg){

                        $organisation = Organisations::findFirst($organisationId);
                        $organisationUsers = $organisation->users;

                        foreach ($organisationUsers as $organisationUser){

                            // Check if user organisation want to share file
                            $settingFile = SettingFiles::findFirst('from_user_id = '.$this->currentUser->getId().' AND to_user_id = '.$organisationUser->getId());

                            if((bool)$settingFile){

                                if($settingFile->getAllow()){

                                    $fileShareUser = new FileSharedUser();
                                    $fileShareUser->setUserId($organisationUser->getId());
                                    $fileShareUser->setFileSharedOrganisationId($fileShare->getId());
                                    $fileShareUser->setStatus(1);
                                    $fileShareUser->save();

                                    $this->notifications->addNotification(array(
                                        'type' => 3,
                                        'subject' => Trans::make('New file share'),
                                        'description' => Trans::make('In your file list is added new file.')
                                    ),null, null, array($organisationUser->getId()));
                                }
                            }
                            else {
                                $fileShareUser = new FileSharedUser();
                                $fileShareUser->setUserId($organisationUser->getId());
                                $fileShareUser->setFileSharedOrganisationId($fileShare->getId());
                                $savedFileShareUser = $fileShareUser->save();

                                if($savedFileShareUser){

                                    $this->notifications->addNotification(array(
                                        'type' => 3,
                                        'subject' => Trans::make('File share'),
                                        'description' => self::getSharedNotificationContent($fileShareUser->getId())
                                    ),null, null, array($organisationUser->getId()));
                                }
                            }
                        }
                    }
                }
            }

            foreach ($oldFilesSharedByFile as $oldFilesShared){

                if(!in_array($oldFilesShared->getOrganisationId(), $organisationIds)){

                    $oldFilesSharedByUsers = $oldFilesShared->FileSharedUser;

                    foreach ($oldFilesSharedByUsers as $oldFilesSharedUser){

                        $oldFilesSharedUser->delete();
                    }
                    $oldFilesShared->delete();
                }
            }
            $this->session->set('message', ['type' => 'success','content' => 'Users were invited and get notifications.']);
            return json_encode(array('status' => true));
        }
    }

    public function editshareAction($fileId){

        $this->view->disable();

        if($this->request->isAjax()){

            $oldFilesShared = FileSharedOrganisation::find('file_id = '.$fileId);
            $oldFilesSharedOrganisationArr = array();
            $organisationsSelectedArr = array();
            $items = array();

            foreach ($oldFilesShared as $value) {

                $oldFilesSharedOrganisationArr[] = $value->getOrganisationId();
            }

            $organisations = Organisations::find('active = 1 AND deleted_by IS NULL');
            $html = '<p>'.Trans::make('Select the organisation you want to share the file with').'</p>';
            $html .= '<select name="user[]" id="users-list" class="select2-share" multiple>';

            foreach ($organisations as $key => $organisation){

                $selected = '';

                if (in_array($organisation->getId(), $oldFilesSharedOrganisationArr)){

                    $selected = 'selected="selected"';
                    $organisationsSelectedArr[$key]['id'] = $organisation->getId();
                    $organisationsSelectedArr[$key]['name'] = $organisation->getName();
                }
                $items[$key]['id'] = $organisation->getId();
                $items[$key]['name'] = $organisation->getName();
                $html .= '<option value="'.$organisation->getId().'" '.$selected.'>'.$organisation->getName().'</option>';
            }
            $html .= '</select>';
            $html .= self::getInvitedContent($oldFilesShared);
            $html .= '<p>'.Trans::make("After you've clicked on the share button the user will be notified of the new file. You can always revoke access.").'</p>';

            return json_encode(array('status' => true, 'item' => $items, 'html' => $html, 'selected' => $organisationsSelectedArr));
        }
    }

    public function ajaxrevokeAction($id){

        $this->view->disable();

        if($this->request->isAjax()) {

            $fileSharedUser = FileSharedUser::findFirst("id = '".$id."' AND created_by = '".$this->currentUser->getId()."'");

            if ($fileSharedUser->delete()) {

                return json_encode(array('status' => true, 'message' => Trans::make('User is revoked')));
            }
        }
        return json_encode(array('status' => false, 'message' => Trans::make('Cannot do this operation')));
    }

    public function ajaxresendAction($id){

        $this->view->disable();

        if($this->request->isAjax()) {

            $fileSharedUser = FileSharedUser::findFirst("id = '".$id."' AND created_by = '".$this->currentUser->getId()."'");

            $this->notifications->addNotification(array(
                'type' => 3,
                'subject' => Trans::make('File reshare'),
                'description' => self::getSharedNotificationContent($fileSharedUser->getId())
            ), null, null, array($fileSharedUser->getUserId()));

            return json_encode(array('status' => true, 'message' => Trans::make('Invitation is resend')));
        }
        return json_encode(array('status' => false, 'message' => Trans::make('Cannot do this operation')));
    }

    private static function getInvitedContent($fileSharedOrganisations){

        $fileSharedUsers = array();

        foreach ($fileSharedOrganisations as $fileSharedOrganisation){

            foreach ($fileSharedOrganisation->FileSharedUser as $fileSharedUser){

                $fileSharedUsers[] = $fileSharedUser;
            }
        }
        $html = '<h4>'.Trans::make('Invited').'</h4>';
        $html .= '<table class="table table-striped table-bordered" cellspacing="0" width="100%"><thead>
                <tr><th>'.Trans::make('Name').'</th>
                <th>'.Trans::make('Date').'</th>
                <th>'.Trans::make('Status').'</th>
                <th></th>
                <th></th>
                </tr></thead><tbody>';

        foreach ($fileSharedUsers as $fileSharedUser){

            $html .= '<tr>
                <td>'.$fileSharedUser->User->getFullname().'</td>
                <td>'.$fileSharedUser->getCreatedAt().'</td>
                <td>'.Trans::make($fileSharedUser->getStatus()).'</td>
                <td><a href="#" class="btn btn-warning revoke-file" data-url="/dentist/file/ajaxrevoke/'.$fileSharedUser->getId().'"><i class="pe-7s-close-circle"></i> '.Trans::make('Revoke access').'</a></td>
                <td><a href="#" class="btn btn-primary resend-file" data-url="/dentist/file/ajaxresend/'.$fileSharedUser->getId().'"><i class="pe-7s-refresh-2"></i> '.Trans::make('Resend invitation').'</a></td>
                </tr>';
        }
        $html .= '</tbody></table>';

        return $html;
    }

    private static function getUploadContent(){

        $html = '<p>'.Trans::make('Select a file on your computer to upload').'</p>';
        $html .= '<input id="fileupload" type="file" name="file" data-url="/dentist/file/upload">';
        $html .= '<div id="uploader"></div>';
        $html .= '<div id="progress"><div class="bar" style="width: 0%;"></div></div>';

        return $html;
    }

    private function getShareContent(){

        $users = Organisations::find('active = 1 AND deleted_by IS NULL');
        $html = '<p>'.Trans::make('Select the organisation you want to share the file with').'</p>';
        $html .= '<select name="user[]" id="users-list" class="select2-input" multiple>';

        foreach ($users as $user){

            $html .= '<option value="'.$user->getId().'">'.$user->getName().'</option>';
        }
        $html .= '</select>';
        $html .= '<p>'.Trans::make("After you've clicked on the share button the user will be notified of the new file. You can always revoke access.").'</p>';

        return $html;
    }

    private static function getSharedNotificationContent($fileShareId){

        $html = '<p>'.Trans::make("You have been requested to ...").'</p>';
        $html .= '<p><a href=&quot;/notification/confirmfile/'.$fileShareId.'&quot; >'.Trans::make("Confirm the invitation").'</p>';
        $html .= '<p><a href=&quot;/notification/rejectfile/'.$fileShareId.'&quot; >'.Trans::make("Reject the invitation").'</p>';

        return $html;
    }
}
