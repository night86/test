<?php

namespace Signa\Controllers\Lab;

use Signa\Models\Files;
use Signa\Models\FileSharedUser;

class FileController extends InitController
{
    public function indexAction(){

        $fileSharedUsers = FileSharedUser::find('status = 1 AND user_id = '.$this->currentUser->getId());
        $fileArr = array();

        foreach ($fileSharedUsers as $fileSharedUser){

            $fileArr[] = $fileSharedUser->FileSharedOrganisation->File;
        }

        $this->view->files = $fileArr;
        $this->view->disableSubnav = true;
    }

    public function downloadAction($fileId){

        $this->view->disable();
        $file = Files::findFirst($fileId);
        $allowed = false;

        foreach ($file->FileSharedOrganisation as $fileSharedOrganisation){

            foreach ($fileSharedOrganisation->FileSharedUser as $fileSharedUser){

                if($fileSharedUser->getUserId() == $this->currentUser->getId()){
                    $allowed = true;
                }
            }
        }
        $fileDir = $this->config->application->filesDir . $file->getCreatedBy() . '/';
        $filename = $fileDir. $file->getName();

        if (file_exists($filename) && $allowed){

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
            $this->response->redirect('/lab/file/');
            return true;
        }
    }
}
